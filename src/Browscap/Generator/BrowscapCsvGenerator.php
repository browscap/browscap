<?php

namespace Browscap\Generator;

use Monolog\Logger;

/**
 * Class BrowscapCsvGenerator
 *
 * @package Browscap\Generator
 */
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
     * @param array[] $allDivisions
     * @param string  $output
     * @param array   $allProperties
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

            if (!$this->firstCheckProperty($key, $properties, $allDivisions)) {
                continue;
            }

            // create output - csv

            $output .= '"' . $key . '"'; // PropertyName
            $output .= ',"' . $counter . '"'; // AgentID
            $output .= ',"' . $this->detectMasterParent($key, $properties) . '"'; // MasterParent

            $output .= ',"'
                . ((!isset($properties['lite']) || !$properties['lite']) ? 'false' : 'true') . '"'; // LiteMode

            foreach ($allProperties as $property) {
                if (!CollectionParser::isOutputProperty($property)) {
                    continue;
                }

                $output .= ',"' . $this->formatValue($property, $properties) . '"';
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
