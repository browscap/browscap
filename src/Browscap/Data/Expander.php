<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   Browscap
 * @package    Data
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Data;

use Psr\Log\LoggerInterface;

/**
 * Class Expander
 *
 * @category   Browscap
 * @package    Data
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class Expander
{
    const TYPE_STRING   = 'string';
    const TYPE_GENERIC  = 'generic';
    const TYPE_NUMBER   = 'number';
    const TYPE_BOOLEAN  = 'boolean';
    const TYPE_IN_ARRAY = 'in_array';

    /**
     * @var \Browscap\Data\DataCollection
     */
    private $collection = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * Set the data collection
     *
     * @param \Browscap\Data\DataCollection $collection
     * @return \Browscap\Data\Expander
     */
    public function setDataCollection(DataCollection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * splits a version into the major and the minor version
     *
     * @param string $version
     *
     * @return string[]
     */
    public function getVersionParts($version)
    {
        $dots = explode('.', $version, 2);

        $majorVer = $dots[0];
        $minorVer = (isset($dots[1]) ? $dots[1] : 0);

        return array($majorVer, $minorVer);
    }

    /**
     * @param \Browscap\Data\Division $division
     * @param string                  $majorVer
     * @param string                  $minorVer
     * @param string                  $divisionName
     *
     * @throws \UnexpectedValueException
     * @return array
     */
    public function expand(Division $division, $majorVer, $minorVer, $divisionName)
    {
        $allInputDivisions = $this->parseDivision(
            $division,
            $majorVer,
            $minorVer,
            $divisionName
        );

        return $this->expandProperties($allInputDivisions);
    }

    /**
     * Render a single division
     *
     * @param \Browscap\Data\Division $division
     * @param string                  $majorVer
     * @param string                  $minorVer
     * @param string                  $divisionName
     *
     * @return array
     */
    private function parseDivision(Division $division, $majorVer, $minorVer, $divisionName)
    {
        $output = array();

        foreach ($division->getUserAgents() as $uaData) {
            $output = array_merge(
                $output,
                $this->parseUserAgent(
                    $uaData,
                    $majorVer,
                    $minorVer,
                    $division->isLite(),
                    $division->getSortIndex(),
                    $divisionName
                )
            );
        }

        return $output;
    }

    /**
     * Render a single User Agent block
     *
     * @param string[] $uaData
     * @param string   $majorVer
     * @param string   $minorVer
     * @param boolean  $lite
     * @param integer  $sortIndex
     * @param string   $divisionName
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

        if (!isset($uaProperties['Parent'])) {
            throw new \LogicException('the "parent" property is missing for key "' . $uaData['userAgent'] . '"');
        }

        if (array_key_exists('platform', $uaData)) {
            $platform     = $this->getDataCollection()->getPlatform($uaData['platform']);
            $platformData = $this->parseProperties($platform->getProperties(), $majorVer, $minorVer);
        } else {
            $platformData = array();
        }

        if (array_key_exists('engine', $uaData)) {
            $engine     = $this->getDataCollection()->getEngine($uaData['engine']);
            $engineData = $this->parseProperties($engine->getProperties(), $majorVer, $minorVer);
        } else {
            $engineData = array();
        }

        $ua = $this->parseProperty($uaData['userAgent'], $majorVer, $minorVer);

        $output = array(
            $ua => array_merge(
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

        foreach ($uaData['children'] as $child) {
            $output = array_merge(
                $output,
                $this->parseChildren($ua, $child, $majorVer, $minorVer)
            );
        }

        return $output;
    }

    /**
     * Render the properties of a single User Agent
     *
     * @param string[] $properties
     * @param string   $majorVer
     * @param string   $minorVer
     *
     * @return string[]
     */
    private function parseProperties(array $properties, $majorVer, $minorVer)
    {
        $output = array();
        foreach ($properties as $property => $value) {
            $output[$property] = $this->parseProperty($value, $majorVer, $minorVer);
        }
        return $output;
    }

    /**
     * Render the property of a single User Agent
     *
     * @param string $value
     * @param string $majorVer
     * @param string $minorVer
     *
     * @return string
     */
    public function parseProperty($value, $majorVer, $minorVer)
    {
        return str_replace(
            array('#MAJORVER#', '#MINORVER#'),
            array($majorVer, $minorVer),
            $value
        );
    }

    /**
     * Get the data collection
     *
     * @throws \LogicException
     * @return \Browscap\Data\DataCollection
     */
    public function getDataCollection()
    {
        if (!isset($this->collection)) {
            throw new \LogicException('Data collection has not been set yet - call setDataCollection');
        }

        return $this->collection;
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
        $output = array();

        // @todo This needs work here. What if we specify platforms AND versions?
        // We need to make it so it does as many permutations as necessary.
        if (isset($uaDataChild['platforms']) && is_array($uaDataChild['platforms'])) {
            foreach ($uaDataChild['platforms'] as $platform) {
                $platformData = $this->getDataCollection()->getPlatform($platform);
                $uaBase       = str_replace('#PLATFORM#', $platformData->getMatch(), $uaDataChild['match']);

                if (array_key_exists('engine', $uaDataChild)) {
                    $engine     = $this->getDataCollection()->getEngine($uaDataChild['engine']);
                    $engineData = $this->parseProperties($engine->getProperties(), $majorVer, $minorVer);
                } else {
                    $engineData = array();
                }

                $properties = array_merge(
                    $this->parseProperties(['Parent' => $ua], $majorVer, $minorVer),
                    $engineData,
                    $this->parseProperties($platformData->getProperties(), $majorVer, $minorVer)
                );

                if (isset($uaDataChild['properties'])
                    && is_array($uaDataChild['properties'])
                ) {
                    $childProperties = $this->parseProperties($uaDataChild['properties'], $majorVer, $minorVer);

                    $properties = array_merge($properties, $childProperties);
                }

                $output[$this->parseProperty($uaBase, $majorVer, $minorVer)] = $properties;
            }
        } else {
            $properties = $this->parseProperties(['Parent' => $ua], $majorVer, $minorVer);

            if (array_key_exists('engine', $uaDataChild)) {
                $engine     = $this->getDataCollection()->getEngine($uaDataChild['engine']);
                $engineData = $this->parseProperties($engine->getProperties(), $majorVer, $minorVer);
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

            $output[$this->parseProperty($uaDataChild['match'], $majorVer, $minorVer)] = $properties;
        }

        return $output;
    }

    /**
     * checks if platform properties are set inside a properties array
     *
     * @param string[] $properties
     * @param string   $message
     *
     * @throws \LogicException
     */
    private function checkPlatformData(array $properties, $message)
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
     * @param string[] $properties
     * @param string   $message
     *
     * @throws \LogicException
     */
    private function checkEngineData(array $properties, $message)
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
     * expands all properties for all useragents to make sure all properties are set and make it possible to skip
     * incomplete properties and remove duplicate definitions
     *
     * @param array $allInputDivisions
     *
     * @throws \UnexpectedValueException
     * @return array
     */
    private function expandProperties(array $allInputDivisions)
    {
        $this->getLogger()->debug('expand all properties');
        $allDivisions = array();

        $ua                = $this->collection->getDefaultProperties()->getUserAgents();
        $defaultproperties = $ua[0]['properties'];

        foreach ($allInputDivisions as $key => $properties) {
            $this->getLogger()->debug('expand all properties for key "' . $key . '"');

            $userAgent = $key;
            $parents   = array($userAgent);

            while (isset($allInputDivisions[$userAgent]['Parent'])) {
                if ($userAgent === $allInputDivisions[$userAgent]['Parent']) {
                    break;
                }

                $parents[] = $allInputDivisions[$userAgent]['Parent'];
                $userAgent = $allInputDivisions[$userAgent]['Parent'];
            }
            unset($userAgent);

            $parents     = array_reverse($parents);
            $browserData = $defaultproperties;

            foreach ($parents as $parent) {
                if (!isset($allInputDivisions[$parent])) {
                    continue;
                }

                if (!isset($allInputDivisions[$parent]['Parent'])) {
                    throw new \UnexpectedValueException(
                        'Parent entry not defined for key "' . $parent . '"'
                    );
                }

                if (!is_array($allInputDivisions[$parent])) {
                    throw new \UnexpectedValueException(
                        'Parent "' . $parent . '" is not an array for key "' . $key . '"'
                    );
                }

                if ($key !== $parent
                    && isset($allInputDivisions[$parent]['sortIndex'])
                    && isset($properties['sortIndex'])
                    && ($allInputDivisions[$parent]['division'] !== $properties['division'])
                ) {
                    if ($allInputDivisions[$parent]['sortIndex'] >= $properties['sortIndex']) {
                        throw new \UnexpectedValueException(
                            'sorting not ready for key "'
                            . $key . '"'
                        );
                    }
                }

                $browserData = array_merge($browserData, $allInputDivisions[$parent]);
            }

            array_pop($parents);
            $browserData['Parents'] = implode(',', $parents);
            unset($parents);

            foreach ($browserData as $propertyName => $propertyValue) {
                switch ((string) $propertyValue) {
                    case 'true':
                        $properties[$propertyName] = true;
                        break;
                    case 'false':
                        $properties[$propertyName] = false;
                        break;
                    default:
                        $properties[$propertyName] = trim($propertyValue);
                        break;
                }
            }

            unset($browserData);

            $allDivisions[$key] = $properties;

            if (!isset($properties['Version'])) {
                throw new \UnexpectedValueException('Version property not found for key "' . $key . '"');
            }

            $completeVersions = explode('.', $properties['Version'], 2);

            if (!isset($properties['MajorVer'])) {
                $properties['MajorVer'] = (string) $completeVersions[0];
            } elseif ($properties['MajorVer'] !== (string) $completeVersions[0]) {
                throw new \UnexpectedValueException(
                    'MajorVersion from properties does not match with Version for key "' . $key . '"'
                );
            }

            if (isset($completeVersions[1])) {
                $minorVersion = (string) $completeVersions[1];
            } else {
                $minorVersion = '0';
            }

            if (!isset($properties['MinorVer'])) {
                $properties['MinorVer'] = $minorVersion;
            } elseif ($properties['MinorVer'] !== $minorVersion) {
                throw new \UnexpectedValueException(
                    'MinorVersion from properties does not match with Version for key "' . $key . '"'
                );
            }

            $allDivisions[$key] = $properties;
        }

        return $allDivisions;
    }

    /**
     * @return \Psr\Log\LoggerInterface $logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Data\Expander
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
