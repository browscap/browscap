<?php

namespace Browscap\Writer;

use Psr\Log\LoggerInterface;

/**
 * Class BrowscapCsvGenerator
 *
 * @package Browscap\Generator
 */
class CsvWriter implements WriterInterface
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

        fputs($this->file, '"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL);

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        fputs($this->file, '"' . $versionData['version'] . '","' . $versionData['released'] . '"' . PHP_EOL);

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
     * @return string
     */
    public function generate()
    {
        $this->getLogger()->debug('build output for csv file');

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
        $this->getLogger()->debug('rendering CSV header');

        $output .= '"PropertyName","AgentID","MasterParent","LiteMode"';

        foreach ($allProperties as $property) {

            if (!CollectionParser::isOutputProperty($property)) {
                continue;
            }

            if (CollectionParser::isExtraProperty($property)) {
                continue;
            }

            $output .= ',"' . $property . '"';
        }

        $output .= PHP_EOL;

        $counter = 1;

        $this->getLogger()->debug('rendering all divisions');
        foreach ($allDivisions as $key => $properties) {
            $this->getLogger()->debug('rendering division "' . $properties['division'] . '" - "' . $key . '"');

            $counter++;

            if (!$this->firstCheckProperty($key, $properties, $allDivisions)) {
                $this->getLogger()->debug('first check failed on key "' . $key . '" -> skipped');

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

                if (CollectionParser::isExtraProperty($property)) {
                    continue;
                }

                $output .= ',"' . $this->formatValue($property, $properties) . '"';

                unset($property);
            }

            $output .= PHP_EOL;

            unset($properties);
        }

        return $output;
    }
}
