<?php

namespace Browscap\Writer;

use Psr\Log\LoggerInterface;

/**
 * Class BrowscapIniGenerator
 *
 * @package Browscap\Generator
 */
class IniWriter implements WriterInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @var resource
     */
    private $file = null;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = fopen($file, 'w');
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * closes the Writer and the written File
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function close()
    {
        fclose($this->file);
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Generate the header
     *
     * @param string[] $comments
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderHeader(array $comments = array())
    {
        $this->getLogger()->debug('rendering comments');

        foreach ($comments as $comment) {
            fputs($this->file, ';;; ' . $comment . PHP_EOL);
        }

        fputs($this->file, PHP_EOL);

        return $this;
    }

    /**
     * renders the version information
     *
     * @param string[] $versionData
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderVersion(array $versionData = array())
    {
        $this->getLogger()->debug('rendering version information');

        fputs($this->file, $this->renderDivisionHeader('Browscap Version'));

        fputs($this->file, '[GJK_Browscap_Version]' . PHP_EOL);

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        if (!isset($versionData['format'])) {
            $versionData['format'] = '';
        }

        if (!isset($versionData['type'])) {
            $versionData['type'] = '';
        }

        fputs($this->file, 'Version=' . $versionData['version'] . PHP_EOL);
        fputs($this->file, 'Released=' . $versionData['released'] . PHP_EOL);
        fputs($this->file, 'Format=' . $versionData['format'] . PHP_EOL);
        fputs($this->file, 'Type=' . $versionData['type'] . PHP_EOL . PHP_EOL);

        return $this;
    }

    /**
     * renders the header for a division
     *
     * @param string $division
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderDivisionHeader($division)
    {
        fputs($this->file, ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; ' . $division . PHP_EOL . PHP_EOL);

        return $this;
    }

    /**
     * renders all found useragents into a string
     *
     * @param array[] $allDivisions
     * @param string  $output
     * @param array   $allProperties
     *
     * @throws \InvalidArgumentException
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderDivisionBody(array $allDivisions, $output, array $allProperties)
    {
        return $this;
    }

    /**
     * @param \Browscap\Formatter\FormatterInterface $formatter
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function addFormatter(\Browscap\Formatter\FormatterInterface $formatter)
    {
        return $this;
    }

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function addFilter(\Browscap\Filter\FilterInterface $filter)
    {
        return $this;
    }

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
        $this->getLogger()->debug('build output for ini file');
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
        $this->getLogger()->debug('rendering all divisions');

        foreach ($allDivisions as $key => $properties) {
            if (!isset($properties['division'])) {
                throw new \InvalidArgumentException('"division" is missing for key "' . $key . '"');
            }

            $this->getLogger()->debug(
                'rendering division "' . $properties['division'] . '" - "' . $key . '"'
            );

            if (!$this->firstCheckProperty($key, $properties, $allDivisions)) {
                $this->getLogger()->debug('first check failed on key "' . $key . '" -> skipped');

                continue;
            }

            if (BuildGenerator::OUTPUT_TYPE_LITE === $this->type
                && (!isset($properties['lite']) || !$properties['lite'])
            ) {
                $this->getLogger()->debug('key "' . $key . '" is not enabled for lite mode -> skipped');

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
                    continue;
                }

                if (!CollectionParser::isOutputProperty($property)) {
                    continue;
                }

                if (BuildGenerator::OUTPUT_TYPE_FULL !== $this->type && CollectionParser::isExtraProperty($property)) {
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
}
