<?php

namespace Browscap\CollectionParser;

use Psr\Log\LoggerInterface;
use Browscap\Generator\DataCollection;

/**
 * Class CollectionParser
 *
 * @package Browscap\Generator
 */
interface ChildrenParserInterface
{
    /**
     * Set the data collection
     *
     * @param \Browscap\Generator\DataCollection $collection
     * @return \Browscap\Generator\CollectionParser
     */
    public function setDataCollection(DataCollection $collection);

    /**
     * Get the data collection
     *
     * @throws \LogicException
     * @return \Browscap\Generator\DataCollection
     */
    public function getDataCollection();

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Generator\CollectionParser
     */
    public function setLogger(LoggerInterface $logger);

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
        $sortIndex, $divisionName, $filename);

    /**
     * checks if platform properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     */
    public function checkPlatformData(array $properties, $message);

    /**
     * checks if platform properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     */
    public function checkEngineData(array $properties, $message);

    /**
     * Render the properties of a single User Agent
     *
     * @param array  $properties
     * @param string $majorVer
     * @param string $minorVer
     *
     * @return string[]
     */
    public function parseProperties(array $properties, $majorVer, $minorVer);

    /**
     * Returns all available Versions for a given Division
     *
     * @param array $division
     *
     * @return array
     */
    public function getDivisions(array $division);
}
