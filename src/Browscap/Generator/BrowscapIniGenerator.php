<?php

namespace Browscap\Generator;

/**
 * Class BrowscapIniGenerator
 *
 * @package Browscap\Generator
 */
class BrowscapIniGenerator extends AbstractGenerator
{
    /**
     * @var string
     */
    private $format = null;

    /**
     * @var string
     */
    private $type = null;

    /**
     * Generate and return the formatted browscap data
     *
     * @param string $format
     * @param string $type
     *
     * @return string
     */
    public function generate($format = BuildGenerator::OUTPUT_FORMAT_PHP, $type = BuildGenerator::OUTPUT_TYPE_FULL)
    {
        $this->logger->debug('build output for ini file');
        $this->format = $format;
        $this->type   = $type;

        if (!empty($this->collectionData['DefaultProperties'])) {
            $defaultPropertyData = $this->collectionData['DefaultProperties'];
        } else {
            $defaultPropertyData = array();
        }

        return $this->render(
            $this->collectionData,
            $this->renderHeader() . $this->renderVersion(),
            array_keys(array('Parent' => '') + $defaultPropertyData)
        );
    }

    /**
     * Generate the header
     *
     * @return string
     */
    private function renderHeader()
    {
        $this->logger->debug('rendering comments');
        $header = '';

        foreach ($this->getComments() as $comment) {
            $header .= ';;; ' . $comment . PHP_EOL;
        }

        $header .= PHP_EOL;

        return $header;
    }

    /**
     * renders all found useragents into a string
     *
     * @param array[] $allDivisions
     * @param string  $output
     * @param array   $allProperties
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    private function render(array $allDivisions, $output, array $allProperties)
    {
        $this->logger->debug('rendering all divisions');

        foreach ($allDivisions as $key => $properties) {
            if (!isset($properties['division'])) {
                throw new \InvalidArgumentException('"division" is missing for key "' . $key . '"');
            }

            $this->logger->debug(
                'rendering division "' . $properties['division'] . '" - "' . $key . '"'
            );

            if (!$this->firstCheckProperty($key, $properties, $allDivisions)) {
                $this->logger->debug('first check failed on key "' . $key . '" -> skipped');

                continue;
            }

            if (BuildGenerator::OUTPUT_TYPE_LITE === $this->type
                && (!isset($properties['lite']) || !$properties['lite'])
            ) {
                $this->logger->debug('key "' . $key . '" is not enabled for lite mode -> skipped');

                continue;
            }

            if (!$this->checkSplit($key, $properties)) {
                $this->logger->debug('key "' . $key . '" is not enabled for actual split file -> skipped');

                continue;
            }

            if (!in_array($key, array('DefaultProperties', '*'))) {
                $parent = $allDivisions[$properties['Parent']];
            } else {
                $parent = array();
            }

            $propertiesToOutput = $properties;

            foreach ($propertiesToOutput as $property => $value) {
                if (!isset($parent[$property])) {
                    continue;
                }

                $parentProperty = $parent[$property];

                switch ((string) $parentProperty) {
                    case 'true':
                        $parentProperty = true;
                        break;
                    case 'false':
                        $parentProperty = false;
                        break;
                    default:
                        $parentProperty = trim($parentProperty);
                        break;
                }

                if ($parentProperty != $value) {
                    continue;
                }

                unset($propertiesToOutput[$property]);
            }

            if (BuildGenerator::OUTPUT_TYPE_FULL !== $this->type) {
                // check if only extra properties are in the actual division
                // skip that division if the extra properties are not in the output
                $propertiesToCheck = $propertiesToOutput;

                unset($propertiesToCheck['Parent']);

                foreach (array_keys($propertiesToCheck) as $property) {
                    if (!CollectionParser::isOutputProperty($property)
                        || CollectionParser::isExtraProperty($property)
                    ) {
                        unset($propertiesToCheck[$property]);
                    }
                }

                if (empty($propertiesToCheck)) {
                    continue;
                }
            }

            // create output - php
            if ('DefaultProperties' === $key
                || '*' === $key || empty($properties['Parent'])
                || 'DefaultProperties' == $properties['Parent']
            ) {
                $output .= $this->renderDivisionHeader($properties['division']);
            }

            $output .= '[' . $key . ']' . PHP_EOL;

            foreach ($allProperties as $property) {
                if (!isset($propertiesToOutput[$property])) {
                    // $this->logger->debug(
                        // 'property "' . $property . '" is not available for output -> skipped'
                    // );

                    continue;
                }

                if (!CollectionParser::isOutputProperty($property)) {
                    // $this->logger->debug(
                        // 'property "' . $property . '" is not defined to be in the output -> skipped'
                    // );

                    continue;
                }

                if (BuildGenerator::OUTPUT_TYPE_FULL !== $this->type && CollectionParser::isExtraProperty($property)) {
                    $this->logger->debug(
                        'property "' . $property . '" is defined to be in the full output -> skipped'
                    );

                    continue;
                }

                $value       = $propertiesToOutput[$property];
                $valueOutput = $value;

                switch (CollectionParser::getPropertyType($property)) {
                    case CollectionParser::TYPE_STRING:
                        if (BuildGenerator::OUTPUT_FORMAT_PHP === $this->format) {
                            $valueOutput = '"' . $value . '"';
                        }
                        break;
                    case CollectionParser::TYPE_BOOLEAN:
                        if (true === $value || $value === 'true') {
                            $valueOutput = 'true';
                        } elseif (false === $value || $value === 'false') {
                            $valueOutput = 'false';
                        }
                        break;
                    case CollectionParser::TYPE_IN_ARRAY:
                        $valueOutput = CollectionParser::checkValueInArray($property, $value);
                        break;
                    default:
                        // nothing t do here
                        break;
                }

                $output .= $property . '=' . $valueOutput . PHP_EOL;

                unset($value, $valueOutput);
            }

            $output .= PHP_EOL;
        }

        return $output;
    }

    /**
     * @param string  $key
     * @param array   $properties
     *
     * @throws \UnexpectedValueException
     * @return bool
     */
    private function checkSplit($key, array $properties)
    {
        $this->logger->debug('check if key if available for split');

        if (!isset($properties['split-file'])) {
            throw new \UnexpectedValueException('property "split-file" not found for key "' . $key . '"');
        }

        $splits = array(
            'A' => BuildGenerator::OUTPUT_TYPE_SPLITA,
            'B' => BuildGenerator::OUTPUT_TYPE_SPLITB,
            'C' => BuildGenerator::OUTPUT_TYPE_SPLITC,
            'D' => BuildGenerator::OUTPUT_TYPE_SPLITD,
            'E' => BuildGenerator::OUTPUT_TYPE_SPLITE
        );

        if (!in_array($this->type, $splits)) {
            // actual no split ->take it to the output
            return true;
        }

        foreach ($splits as $filetype => $type) {
            if ($this->type === $type && $properties['split-file'] === $filetype) {
                return true;
            }
        }

        return false;
    }

    /**
     * renders the version information
     *
     * @return string
     */
    private function renderVersion()
    {
        $this->logger->debug('rendering version information');
        $header = $this->renderDivisionHeader('Browscap Version');

        $header .= '[GJK_Browscap_Version]' . PHP_EOL;

        $versionData = $this->getVersionData();

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        $header .= 'Version=' . $versionData['version'] . PHP_EOL;
        $header .= 'Released=' . $versionData['released'] . PHP_EOL;
        $header .= 'Format=' . $this->format . PHP_EOL;
        $header .= 'Type=' . $this->type . PHP_EOL . PHP_EOL;

        return $header;
    }

    /**
     * renders the header for a division
     *
     * @param string $division
     *
     * @return string
     */
    private function renderDivisionHeader($division)
    {
        return ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; ' . $division . PHP_EOL . PHP_EOL;
    }
}
