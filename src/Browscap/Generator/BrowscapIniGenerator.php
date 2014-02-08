<?php

namespace Browscap\Generator;

use Monolog\Logger;

class BrowscapIniGenerator extends AbstractGenerator
{
    /**
     * Generate and return the formatted browscap data
     *
     * @return string
     */
    public function generate()
    {
        if (!empty($this->collectionData['DefaultProperties'])) {
            $defaultPropertiyData = $this->collectionData['DefaultProperties'];
        } else {
            $defaultPropertiyData = array();
        }

        $this->log('start parsing');
        return $this->render(
            $this->collectionData,
            $this->renderHeader(),
            array_keys(array('Parent' => '') + $defaultPropertiyData)
        );
    }

    /**
     * Generate the header
     *
     * @return string
     */
    private function renderHeader()
    {
        $header = '';

        $this->log('rendering comments');
        foreach ($this->getComments() as $comment) {
            $header .= ';;; ' . $comment . PHP_EOL;
        }

        $header .= PHP_EOL;

        $header .= $this->renderVersion();

        return $header;
    }

    /**
     * renders all found useragents into a string
     *
     * @param array  $allDivisions
     * @param string $output
     * @param array  $allProperties
     *
     * @return string
     */
    private function render(array $allDivisions, $output, array $allProperties)
    {
        $this->log('rendering all divisions');
        foreach ($allDivisions as $key => $properties) {
            $this->log('rendering division "' . $properties['division'] . '" - "' . $key . '"');

            if (!isset($properties['Version'])) {
                $this->log('skipping division "' . $properties['division'] . '" - version information is missing');
                continue;
            }

            if (!isset($properties['Parent'])
                && 'DefaultProperties' !== $key
                && '*' !== $key
            ) {
                $this->log('skipping division "' . $properties['division'] . '" - no parent defined');
                continue;
            }

            if ($this->liteOnly && (!isset($properties['lite']) || !$properties['lite'])) {
                $this->log('skipping division "' . $properties['division'] . '" - not available for lite mode');
                continue;
            }

            if ('DefaultProperties' !== $key && '*' !== $key) {
                if (!isset($allDivisions[$properties['Parent']])) {
                    $this->log('skipping division "' . $properties['division'] . '" - parent not found');
                    continue;
                }

                $parent = $allDivisions[$properties['Parent']];
            } else {
                $parent = array();
            }

            if (isset($parent['Version'])) {
                $completeVersions = explode('.', $parent['Version'], 2);

                $parent['MajorVer'] = (string) $completeVersions[0];

                if (isset($completeVersions[1])) {
                    $parent['MinorVer'] = (string) $completeVersions[1];
                } else {
                    $parent['MinorVer'] = 0;
                }
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
                $output .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; ' . $properties['division'] . PHP_EOL . PHP_EOL;
            }

            $output .= '[' . $key . ']' . PHP_EOL;

            foreach ($allProperties as $property) {
                if (!isset($propertiesToOutput[$property])) {
                    continue;
                }

                if (in_array($property, array('lite', 'sortIndex', 'Parents', 'division'))) {
                    continue;
                }

                if ((!$this->includeExtraProperties) && CollectionParser::isExtraProperty($property)) {
                    $this->log('skipping property "' . $property . '" from output - is not an extra property');
                    continue;
                }

                $value       = $propertiesToOutput[$property];
                $valueOutput = $value;

                switch (CollectionParser::getPropertyType($property)) {
                    case 'string':
                        if ($this->quoteStringProperties) {
                            $valueOutput = '"' . $value . '"';
                        }
                        break;
                    case 'boolean':
                        if (true === $value || $value === 'true') {
                            $valueOutput = 'true';
                        } elseif (false === $value || $value === 'false') {
                            $valueOutput = 'false';
                        }
                        break;
                    case 'generic':
                    case 'number':
                    default:
                        // nothing t do here
                        break;
                }

                $output .= $property . '=' . $valueOutput . PHP_EOL;
            }

            $output .= PHP_EOL;
        }

        return $output;
    }

    /**
     * renders the version information
     *
     * @return string
     */
    private function renderVersion()
    {
        $this->log('rendering version information');
        $header = ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; Browscap Version' . PHP_EOL . PHP_EOL;

        $header .= '[GJK_Browscap_Version]' . PHP_EOL;

        $versionData = $this->getVersionData();

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        $header .= 'Version=' . $versionData['version'] . PHP_EOL;
        $header .= 'Released=' . $versionData['released'] . PHP_EOL . PHP_EOL;

        return $header;
    }

    /**
     * @param string $message
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    private function log($message)
    {
        if (null === $this->logger) {
            return $this;
        }

        $this->logger->log(Logger::DEBUG, $message);

        return $this;
    }
}
