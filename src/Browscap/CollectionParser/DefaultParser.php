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
     * Returns all available Versions for a given Division
     *
     * @param array $division
     *
     * @return array
     */
    public function getDivisions(array $division)
    {
        if (isset($division['versions']) && is_array($division['versions'])) {
            return $division['versions'];
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
        if (!isset($userAgents[0]['properties']['Parent'])) {
            throw new \UnexpectedValueException(
                'the "parent" property is missing for key "' . $userAgents[0]['userAgent'] . '"'
            );
        }

        if (!isset($allDivisions[$userAgents[0]['properties']['Parent']])) {
            throw new \UnexpectedValueException(
                'the "parent" element "' . $userAgents[0]['properties']['Parent']
                . '" for key "' . $userAgents[0]['userAgent'] . '" is not added before the element, '
                . 'please change the SortIndex'
            );
        }

        $divisions = $this->parseDivision(
            $userAgents,
            $majorVer,
            $minorVer,
            $lite,
            $sortIndex,
            $divisionName,
            $filename
        );

        foreach ($divisions as $key => $divisionData) {
            if (isset($allDivisions[$key])) {
                throw new \UnexpectedValueException('Division "' . $key . '" is defined twice');
            }

            $allDivisions[$key] = $divisionData;
        }

        return $allDivisions;
    }

    /**
     * Render a single division
     *
     * @param array   $userAgents
     * @param string  $majorVer
     * @param string  $minorVer
     * @param boolean $lite
     * @param integer $sortIndex
     * @param string  $divisionName
     *
     * @return array
     */
    private function parseDivision(array $userAgents, $majorVer, $minorVer, $lite, $sortIndex, $divisionName)
    {
        $output = array();

        foreach ($userAgents as $uaData) {
            $output = array_merge(
                $output,
                $this->parseUserAgent(
                    $uaData,
                    $majorVer,
                    $minorVer,
                    $lite,
                    $sortIndex,
                    $divisionName
                )
            );
        }

        return $output;
    }

    /**
     * Render a single User Agent block
     *
     * @param array   $uaData
     * @param string  $majorVer
     * @param string  $minorVer
     * @param boolean $lite
     * @param integer $sortIndex
     * @param string  $divisionName
     *
     * @throws \LogicException
     * @return array
     */
    private function parseUserAgent(array $uaData, $majorVer, $minorVer, $lite, $sortIndex, $divisionName)
    {
        if (!isset($uaData['properties']) || !is_array($uaData['properties'])) {
            throw new \LogicException('properties are missing or not an array for key "' . $uaData['userAgent'] . '"');
        }

        $uaProperties = $this->parseProperties($uaData['properties'], $majorVer, $minorVer);

        if (!in_array($uaData['userAgent'], array('DefaultProperties', '*'))) {
            $this->checkPlatformData(
                $uaProperties,
                'the properties array contains platform data for key "' . $uaData['userAgent']
                . '", please use the "platform" keyword'
            );

            $this->checkEngineData(
                $uaProperties,
                'the properties array contains engine data for key "' . $uaData['userAgent']
                . '", please use the "engine" keyword'
            );

            if (!isset($uaProperties['Parent'])) {
                throw new \LogicException('the "parent" property is missing for key "' . $uaData['userAgent'] . '"');
            }
        }

        if (array_key_exists('platform', $uaData)) {
            $platform     = $this->getDataCollection()->getPlatform($uaData['platform']);
            $platformData = $platform['properties'];
        } else {
            $platformData = array();
        }

        if (array_key_exists('engine', $uaData)) {
            $engine     = $this->getDataCollection()->getEngine($uaData['engine']);
            $engineData = $this->parseProperties($engine['properties'], $majorVer, $minorVer);
        } else {
            $engineData = array();
        }

        $output = array(
            $uaData['userAgent'] => array_merge(
                array(
                    'lite' => $lite,
                    'sortIndex' => $sortIndex,
                    'division' => $divisionName
                ),
                $platformData,
                $engineData,
                $uaProperties
            )
        );

        if (!isset($uaData['children']) || !is_array($uaData['children']) || !count($uaData['children'])) {
            return $output;
        }

        if (isset($uaData['children']['match'])) {
            throw new \LogicException(
                'the children property has to be an array of arrays for key "' . $uaData['userAgent'] . '"'
            );
        }

        foreach ($uaData['children'] as $child) {
            if (!is_array($child)) {
                throw new \LogicException(
                    'each entry of the children property has to be an array for key "' . $uaData['userAgent'] . '"'
                );
            }

            if (!isset($child['match'])) {
                throw new \LogicException(
                    'each entry of the children property requires an "match" entry for key "'
                    . $uaData['userAgent'] . '"'
                );
            }

            $output = array_merge(
                $output,
                $this->parseChildren($uaData['userAgent'], $child, $majorVer, $minorVer)
            );
        }

        return $output;
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
    private function parseChildren($ua, array $uaDataChild, $majorVer, $minorVer)
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
