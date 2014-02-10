<?php

namespace Browscap\Generator;

use Monolog\Logger;

class BrowscapCsvGenerator extends AbstractGenerator
{
    /**
     * Generate and return the formatted browscap data
     *
     * @return string
     */
    public function generate()
    {
        return $this->render(
            $this->collectionData,
            $this->renderVersion(),
            array_keys(array('Parent' => '') + $this->collectionData['DefaultProperties'])
        );
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
        $this->log('rendering CSV header');
        $output .= '"PropertyName","AgentID","MasterParent","LiteMode"';

        foreach ($allProperties as $property) {

            if (in_array($property, array('lite', 'sortIndex', 'Parents', 'division'))) {
                continue;
            }

            $output .= ',"' . $property . '"';
        }

        $output .= PHP_EOL;

        $counter = 1;

        $this->log('rendering all divisions');
        foreach ($allDivisions as $key => $properties) {
            $this->log('rendering division "' . $properties['division'] . '" - "' . $key . '"');

            $counter++;

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

            // create output - csv

            $output .= '"' . $key . '"'; // PropertyName
            $output .= ',"' . $counter . '"'; // AgentID

            if ('DefaultProperties' === $key
                || '*' === $key || empty($properties['Parent'])
                || 'DefaultProperties' == $properties['Parent']
            ) {
                $masterParent = 'true';
            } else {
                $masterParent = 'false';
            }

            $output .= ',"' . $masterParent . '"'; // MasterParent

            $output .= ',"'
                . ((!isset($properties['lite']) || !$properties['lite']) ? 'false' : 'true') . '"'; // LiteMode

            foreach ($allProperties as $property) {
                if (in_array($property, array('lite', 'sortIndex', 'Parents', 'division'))) {
                    continue;
                }

                if (!isset($properties[$property])) {
                    $value = '';
                } else {
                    $value = $properties[$property];
                }

                $valueOutput = $value;

                switch (CollectionParser::getPropertyType($property)) {
                    case 'boolean':
                        if (true === $value || $value === 'true') {
                            $valueOutput = 'true';
                        } elseif (false === $value || $value === 'false') {
                            $valueOutput = 'false';
                        }
                        break;
                    case 'string':
                    case 'generic':
                    case 'number':
                    default:
                        // nothing t do here
                        break;
                }

                if ('unknown' === $valueOutput) {
                    $valueOutput = '';
                }

                $output .= ',"' . $valueOutput . '"';
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
        $header = '"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL;

        $versionData = $this->getVersionData();

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        $header .= '"' . $versionData['version'] . '","' . $versionData['released'] . '"' . PHP_EOL;

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
