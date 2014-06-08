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
     * Render the children section in a single User Agent block
     *
     * @param string $ua
     * @param array  $uaDataChild
     * @param string $majorVer
     * @param string $minorVer
     *
     * @throws \LogicException
     * @return array[]
     */
    public function parseChildren($ua, array $uaDataChild, $majorVer, $minorVer);

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
}
