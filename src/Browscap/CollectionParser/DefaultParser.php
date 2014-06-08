<?php

namespace Browscap\CollectionParser;

use Psr\Log\LoggerInterface;
use Browscap\Generator\DataCollection;

/**
 * Class CollectionParser
 *
 * @package Browscap\Generator
 */
class DefaultParser implements ChildrenParserInterface
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
    public function parseChildren($ua, array $uaDataChild, $majorVer, $minorVer)
    {
        if (isset($uaDataChild['properties'])) {
            if (!is_array($uaDataChild['properties'])) {
                throw new \LogicException(
                    'the properties entry has to be an array for key "' . $uaDataChild['match'] . '"'
                );
            }

            if (isset($uaDataChild['properties']['Parent'])) {
                throw new \LogicException(
                    'the Parent property must not set inside the children array for key "' . $uaDataChild['match'] . '"'
                );
            }
        }

        $output = array();

        // @todo This needs work here. What if we specify platforms AND versions?
        // We need to make it so it does as many permutations as necessary.
        if (isset($uaDataChild['platforms']) && is_array($uaDataChild['platforms'])) {
            foreach ($uaDataChild['platforms'] as $platform) {
                $platformData = $this->getDataCollection()->getPlatform($platform);
                $uaBase       = str_replace('#PLATFORM#', $platformData['match'], $uaDataChild['match']);

                if (array_key_exists('engine', $uaDataChild)) {
                    $engine     = $this->getDataCollection()->getEngine($uaDataChild['engine']);
                    $engineData = $this->parseProperties($engine['properties'], $majorVer, $minorVer);
                } else {
                    $engineData = array();
                }

                $properties = array_merge(
                    $this->parseProperties(['Parent' => $ua], $majorVer, $minorVer),
                    $engineData,
                    $this->parseProperties($platformData['properties'], $majorVer, $minorVer)
                );

                if (isset($uaDataChild['properties'])
                    && is_array($uaDataChild['properties'])
                ) {
                    $childProperties = $this->parseProperties($uaDataChild['properties'], $majorVer, $minorVer);

                    $this->checkPlatformData(
                        $childProperties,
                        'the properties array contains platform data for key "' . $uaBase
                        . '", please use the "platforms" keyword'
                    );

                    $this->checkEngineData(
                        $childProperties,
                        'the properties array contains engine data for key "' . $uaBase
                        . '", please use the "engine" keyword'
                    );

                    $properties = array_merge($properties, $childProperties);
                }

                $output[$uaBase] = $properties;
            }
        } else {
            $properties = $this->parseProperties(['Parent' => $ua], $majorVer, $minorVer);

            if (array_key_exists('engine', $uaDataChild)) {
                $engine     = $this->getDataCollection()->getEngine($uaDataChild['engine']);
                $engineData = $this->parseProperties($engine['properties'], $majorVer, $minorVer);
            } else {
                $engineData = array();
            }

            $properties = array_merge($properties, $engineData);

            if (isset($uaDataChild['properties'])
                && is_array($uaDataChild['properties'])
            ) {
                $childProperties = $this->parseProperties($uaDataChild['properties'], $majorVer, $minorVer);

                $this->checkPlatformData(
                    $childProperties,
                    'the properties array contains platform data for key "' . $ua
                    . '", please use the "platforms" keyword'
                );

                $this->checkEngineData(
                    $childProperties,
                    'the properties array contains engine data for key "' . $ua
                    . '", please use the "engine" keyword'
                );

                $properties = array_merge($properties, $childProperties);
            }

            $output[$uaDataChild['match']] = $properties;
        }

        return $output;
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
        if (array_key_exists('Platform', $properties)
            || array_key_exists('Platform_Description', $properties)
            || array_key_exists('Platform_Maker', $properties)
            || array_key_exists('Platform_Bits', $properties)
            || array_key_exists('Platform_Version', $properties)
        ) {
            throw new \LogicException($message);
        }
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
        if (array_key_exists('RenderingEngine_Name', $properties)
            || array_key_exists('RenderingEngine_Version', $properties)
            || array_key_exists('RenderingEngine_Description', $properties)
            || array_key_exists('RenderingEngine_Maker', $properties)
        ) {
            throw new \LogicException($message);
        }
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
        $output = array();
        foreach ($properties as $property => $value) {
            $value = str_replace(
                array('#MAJORVER#', '#MINORVER#'),
                array($majorVer, $minorVer),
                $value
            );

            $output[$property] = $value;
        }
        return $output;
    }
}
