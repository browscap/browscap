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
     * @param string                  $divisionName
     *
     * @throws \UnexpectedValueException
     * @return array
     */
    public function expand(Division $division, $divisionName)
    {
        $allInputDivisions = $this->parseDivision(
            $division,
            $divisionName
        );

        return $this->expandProperties($allInputDivisions);
    }

    /**
     * Render a single division
     *
     * @param \Browscap\Data\Division $division
     * @param string                  $divisionName
     *
     * @return array
     */
    private function parseDivision(Division $division, $divisionName)
    {
        $output = array();

        foreach ($division->getUserAgents() as $uaData) {
            $output = array_merge(
                $output,
                $this->parseUserAgent(
                    $uaData,
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
     * @param boolean  $lite
     * @param integer  $sortIndex
     * @param string   $divisionName
     *
     * @throws \LogicException
     * @return array
     */
    private function parseUserAgent(array $uaData, $lite, $sortIndex, $divisionName)
    {
        if (!isset($uaData['properties']) || !is_array($uaData['properties'])) {
            throw new \LogicException('properties are missing or not an array for key "' . $uaData['userAgent'] . '"');
        }

        $uaProperties = $uaData['properties'];

        if (!isset($uaProperties['Parent'])) {
            throw new \LogicException('the "parent" property is missing for key "' . $uaData['userAgent'] . '"');
        }

        if (array_key_exists('platform', $uaData)) {
            $platform     = $this->getDataCollection()->getPlatform($uaData['platform']);
            $platformData = $platform->getProperties();
        } else {
            $platformData = array();
        }

        if (array_key_exists('engine', $uaData)) {
            $engine     = $this->getDataCollection()->getEngine($uaData['engine']);
            $engineData = $engine->getProperties();
        } else {
            $engineData = array();
        }

        if (array_key_exists('device', $uaData)) {
            $device     = $this->getDataCollection()->getDevice($uaData['device']);
            $deviceData = $device->getProperties();
        } else {
            $deviceData = array();
        }

        $ua = $uaData['userAgent'];

        $output = array(
            $ua => array_merge(
                array(
                    'lite' => $lite,
                    'sortIndex' => $sortIndex,
                    'division' => $divisionName
                ),
                $platformData,
                $engineData,
                $deviceData,
                $uaProperties
            )
        );

        if (!isset($uaData['children']) || !is_array($uaData['children']) || !count($uaData['children'])) {
            return $output;
        }

        foreach ($uaData['children'] as $child) {
            $output = array_merge(
                $output,
                $this->parseChildren($ua, $child)
            );
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
     *
     * @throws \LogicException
     * @return array[]
     */
    private function parseChildren($ua, array $uaDataChild)
    {
        $output = array();

        if (isset($uaDataChild['platforms']) && is_array($uaDataChild['platforms'])) {
            foreach ($uaDataChild['platforms'] as $platform) {
                $platformData = $this->getDataCollection()->getPlatform($platform);
                $uaBase       = str_replace('#PLATFORM#', $platformData->getMatch(), $uaDataChild['match']);

                if (array_key_exists('engine', $uaDataChild)) {
                    $engine     = $this->getDataCollection()->getEngine($uaDataChild['engine']);
                    $engineData = $engine->getProperties();
                } else {
                    $engineData = array();
                }

                if (array_key_exists('device', $uaDataChild)) {
                    $device     = $this->getDataCollection()->getDevice($uaDataChild['engine']);
                    $deviceData = $device->getProperties();
                } else {
                    $deviceData = array();
                }

                $properties = array_merge(
                    ['Parent' => $ua],
                    $engineData,
                    $deviceData,
                    $platformData->getProperties()
                );

                if (isset($uaDataChild['properties'])
                    && is_array($uaDataChild['properties'])
                ) {
                    $childProperties = $uaDataChild['properties'];

                    $properties = array_merge($properties, $childProperties);
                }

                $output[$uaBase] = $properties;
            }
        } else {
            $properties = ['Parent' => $ua];

            if (array_key_exists('engine', $uaDataChild)) {
                $engine     = $this->getDataCollection()->getEngine($uaDataChild['engine']);
                $engineData = $engine->getProperties();
            } else {
                $engineData = array();
            }

            if (array_key_exists('device', $uaDataChild)) {
                $device     = $this->getDataCollection()->getDevice($uaDataChild['engine']);
                $deviceData = $device->getProperties();
            } else {
                $deviceData = array();
            }

            $properties = array_merge($properties, $engineData, $deviceData);

            if (isset($uaDataChild['properties'])
                && is_array($uaDataChild['properties'])
            ) {
                $childProperties = $uaDataChild['properties'];

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

        foreach (array_keys($allInputDivisions) as $key) {
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
            $properties  = $allInputDivisions[$key];

            foreach ($parents as $parent) {
                if (!isset($allInputDivisions[$parent])) {
                    continue;
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

            foreach (array_keys($browserData) as $propertyName) {
                $properties[$propertyName] = $this->trimProperty($browserData[$propertyName]);
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
     * trims the value of a property and converts the string values "true" and "false" to boolean
     *
     * @param string $propertyValue
     *
     * @return string|boolean
     */
    public function trimProperty($propertyValue)
    {
        switch ((string) $propertyValue) {
            case 'true':
                $propertyValue = true;
                break;
            case 'false':
                $propertyValue = false;
                break;
            default:
                $propertyValue = trim($propertyValue);
                break;
        }

        return $propertyValue;
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
