<?php

namespace Browscap\CollectionParser;

use Psr\Log\LoggerInterface;
use Browscap\Generator\DataCollection;

/**
 * Class CollectionParser
 *
 * @package Browscap\Generator
 */
class SorterParser implements ChildrenParserInterface
{
    /**
     * @var \Browscap\Generator\DataCollection
     */
    private $collection;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * Set the data collection
     *
     * @param \Browscap\Generator\DataCollection $collection
     * @return \Browscap\Generator\CollectionParser
     */
    public function setDataCollection(DataCollection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Get the data collection
     *
     * @throws \LogicException
     * @return \Browscap\Generator\DataCollection
     */
    public function getDataCollection()
    {
        if (!isset($this->collection)) {
            throw new \LogicException('Data collection has not been set yet - call setDataCollection');
        }

        return $this->collection;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Generator\CollectionParser
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Returns all available Versions for a given Division
     *
     * @param array $division
     *
     * @return array
     */
    public function getDivisions(array $division)
    {
        if (isset($division['versions']) && is_array($division['versions'])) {
            return array(array_shift($division['versions']));
        }

        return array('0.0');
    }

    /**
     * @param array   $allDivisions
     * @param array   $userAgents
     * @param string  $majorVer
     * @param string  $minorVer
     * @param boolean $lite
     * @param integer $sortIndex
     * @param string  $divisionName
     * @param string  $filename
     *
     * @throws \UnexpectedValueException
     * @return array
     */
    public function handleSingleDivision(array $allDivisions, array $userAgents, $majorVer, $minorVer, $lite,
        $sortIndex, $divisionName, $filename)
    {
        // skip core file from checking
        if (in_array($userAgents[0]['userAgent'], array('DefaultProperties', '*'))) {
            return $allDivisions;
        }

        $division = $this->getDataCollection()->getDivision($filename);

        if (!isset($userAgents[0]['properties']['Parent'])) {
            throw new \UnexpectedValueException(
                'the "parent" property is missing for key "' . $userAgents[0]['userAgent'] . '"'
            );
        }

        return $allDivisions;
    }

    /**
     * checks if platform properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     */
    public function checkPlatformData(array $properties, $message)
    {
        // do nothing here
    }

    /**
     * checks if platform properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     */
    public function checkEngineData(array $properties, $message)
    {
        // do nothing here
    }

    /**
     * Render the properties of a single User Agent
     *
     * @param array  $properties
     * @param string $majorVer
     * @param string $minorVer
     *
     * @return string[]
     */
    public function parseProperties(array $properties, $majorVer, $minorVer)
    {
        return $properties;
    }
}
