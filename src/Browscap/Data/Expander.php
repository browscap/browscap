<?php

declare(strict_types=1);

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
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Data;

use Psr\Log\LoggerInterface;

/**
 * Class Expander
 *
 * @category   Browscap
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
     * This store the components of the pattern id that are later merged into a string. Format for this
     * can be seen in the resetPatternId method.
     *
     * @var array
     */
    private $patternId = [];

    /**
     * Set the data collection
     *
     * @param  \Browscap\Data\DataCollection $collection
     *
     * @return \Browscap\Data\Expander
     */
    public function setDataCollection(DataCollection $collection) : self
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
    public function getVersionParts(string $version) : array
    {
        $dots = explode('.', $version, 2);

        $majorVer = $dots[0];
        $minorVer = (isset($dots[1]) ? $dots[1] : 0);

        return [$majorVer, $minorVer];
    }

    /**
     * @param \Browscap\Data\Division $division
     * @param string                  $divisionName
     *
     * @throws \UnexpectedValueException
     * @return array
     */
    public function expand(Division $division, string $divisionName) : array
    {
        $allInputDivisions = $this->parseDivision(
            $division,
            $divisionName
        );

        return $this->expandProperties($allInputDivisions);
    }

    /**
     * Resets the pattern id
     *
     * @return void
     */
    private function resetPatternId()
    {
        $this->patternId = [
            'division' => '',
            'useragent' => '',
            'platform' => '',
            'device' => '',
            'child' => '',
        ];
    }

    /**
     * Render a single division
     *
     * @param \Browscap\Data\Division $division
     * @param string                  $divisionName
     *
     * @return array
     */
    private function parseDivision(Division $division, string $divisionName) : array
    {
        $output = [];

        $i = 0;
        foreach ($division->getUserAgents() as $uaData) {
            $this->resetPatternId();
            $this->patternId['division']  = $division->getFileName();
            $this->patternId['useragent'] = $i;

            $output = array_merge(
                $output,
                $this->parseUserAgent(
                    $uaData,
                    $division->isLite(),
                    $division->isStandard(),
                    $division->getSortIndex(),
                    $divisionName
                )
            );
            ++$i;
        }

        return $output;
    }

    /**
     * Render a single User Agent block
     *
     * @param string[] $uaData
     * @param bool     $lite
     * @param bool     $standard
     * @param int      $sortIndex
     * @param string   $divisionName
     *
     * @return array
     */
    private function parseUserAgent(array $uaData, bool $lite, bool $standard, int $sortIndex, string $divisionName) : array
    {
        if (!isset($uaData['properties']) || !is_array($uaData['properties'])) {
            throw new \LogicException('properties are missing or not an array for key "' . $uaData['userAgent'] . '"');
        }

        $uaProperties = $uaData['properties'];

        if (!isset($uaProperties['Parent'])) {
            throw new \LogicException('the "parent" property is missing for key "' . $uaData['userAgent'] . '"');
        }

        if (array_key_exists('platform', $uaData)) {
            $this->patternId['platform'] = $uaData['platform'];
            $platform                    = $this->getDataCollection()->getPlatform($uaData['platform']);

            if (!$platform->isLite()) {
                $lite = false;
            }

            if (!$platform->isStandard()) {
                $standard = false;
            }

            $platformData = $platform->getProperties();
        } else {
            $this->patternId['platform'] = '';
            $platformData                = [];
        }

        if (array_key_exists('engine', $uaData)) {
            $engine     = $this->getDataCollection()->getEngine($uaData['engine']);
            $engineData = $engine->getProperties();
        } else {
            $engineData = [];
        }

        if (array_key_exists('device', $uaData)) {
            $device     = $this->getDataCollection()->getDevice($uaData['device']);
            $deviceData = $device->getProperties();

            if (!$device->isStandard()) {
                $standard = false;
            }
        } else {
            $deviceData = [];
        }

        $ua = $uaData['userAgent'];

        $output = [
            $ua => array_merge(
                [
                    'lite' => $lite,
                    'standard' => $standard,
                    'sortIndex' => $sortIndex,
                    'division' => $divisionName,
                ],
                $platformData,
                $engineData,
                $deviceData,
                $uaProperties
            ),
        ];

        if (!isset($uaData['children']) || !is_array($uaData['children']) || !count($uaData['children'])) {
            return $output;
        }

        $i = 0;
        foreach ($uaData['children'] as $child) {
            $this->patternId['child'] = $i;
            if (isset($child['devices']) && is_array($child['devices'])) {
                // Replace our device array with a single device property with our #DEVICE# token replaced
                foreach ($child['devices'] as $deviceMatch => $deviceName) {
                    $this->patternId['device'] = $deviceMatch;
                    $subChild                  = $child;
                    $subChild['match']         = str_replace('#DEVICE#', $deviceMatch, $subChild['match']);
                    $subChild['device']        = $deviceName;
                    unset($subChild['devices']);
                    $output = array_merge(
                        $output,
                        $this->parseChildren($ua, $subChild, $lite, $standard)
                    );
                }
            } else {
                $this->patternId['device'] = '';
                $output                    = array_merge(
                    $output,
                    $this->parseChildren($ua, $child, $lite, $standard)
                );
            }
            ++$i;
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
    public function parseProperty(string $value, string $majorVer, string $minorVer) : string
    {
        return str_replace(
            ['#MAJORVER#', '#MINORVER#'],
            [$majorVer, $minorVer],
            $value
        );
    }

    /**
     * Get the data collection
     *
     * @throws \LogicException
     * @return \Browscap\Data\DataCollection
     */
    public function getDataCollection() : DataCollection
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
     * @param bool   $lite
     * @param bool   $standard
     *
     * @return \array[]
     */
    private function parseChildren(string $ua, array $uaDataChild, bool $lite = true, bool $standard = true) : array
    {
        $output = [];

        if (isset($uaDataChild['platforms']) && is_array($uaDataChild['platforms'])) {
            foreach ($uaDataChild['platforms'] as $platform) {
                $this->patternId['platform'] = $platform;
                $properties                  = ['Parent' => $ua, 'lite' => $lite, 'standard' => $standard];
                $platformData                = $this->getDataCollection()->getPlatform($platform);

                if (!$platformData->isLite()) {
                    $properties['lite'] = false;
                }

                if (!$platformData->isStandard()) {
                    $properties['standard'] = false;
                }

                $uaBase = str_replace('#PLATFORM#', $platformData->getMatch(), $uaDataChild['match']);

                if (array_key_exists('engine', $uaDataChild)) {
                    $engine     = $this->getDataCollection()->getEngine($uaDataChild['engine']);
                    $engineData = $engine->getProperties();
                } else {
                    $engineData = [];
                }

                if (array_key_exists('device', $uaDataChild)) {
                    $device     = $this->getDataCollection()->getDevice($uaDataChild['device']);
                    $deviceData = $device->getProperties();

                    if (!$device->isStandard()) {
                        $properties['standard'] = false;
                    }
                } else {
                    $deviceData = [];
                }

                $properties = array_merge(
                    $properties,
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

                $properties['PatternId'] = $this->getPatternId();

                $output[$uaBase] = $properties;
            }
        } else {
            $properties = ['Parent' => $ua, 'lite' => $lite, 'standard' => $standard];

            if (array_key_exists('engine', $uaDataChild)) {
                $engine     = $this->getDataCollection()->getEngine($uaDataChild['engine']);
                $engineData = $engine->getProperties();
            } else {
                $engineData = [];
            }

            if (array_key_exists('device', $uaDataChild)) {
                $device     = $this->getDataCollection()->getDevice($uaDataChild['device']);
                $deviceData = $device->getProperties();

                if (!$device->isStandard()) {
                    $properties['standard'] = false;
                }
            } else {
                $deviceData = [];
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

            $uaBase                      = str_replace('#PLATFORM#', '', $uaDataChild['match']);
            $this->patternId['platform'] = '';

            $properties['PatternId'] = $this->getPatternId();

            $output[$uaBase] = $properties;
        }

        return $output;
    }

    /**
     * Builds and returns the string pattern id from the array components
     *
     * @return string
     */
    private function getPatternId() : string
    {
        return sprintf(
            '%s::u%d::c%d::d%s::p%s',
            $this->patternId['division'],
            $this->patternId['useragent'],
            $this->patternId['child'],
            $this->patternId['device'],
            $this->patternId['platform']
        );
    }

    /**
     * checks if platform properties are set inside a properties array
     *
     * @param string[] $properties
     * @param string   $message
     *
     * @throws \LogicException
     * @return void
     */
    private function checkPlatformData(array $properties, string $message)
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
     * @return void
     */
    private function checkEngineData(array $properties, string $message)
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
    private function expandProperties(array $allInputDivisions) : array
    {
        $this->getLogger()->debug('expand all properties');
        $allDivisions = [];

        $ua                = $this->collection->getDefaultProperties()->getUserAgents();
        $defaultproperties = $ua[0]['properties'];

        foreach (array_keys($allInputDivisions) as $key) {
            $this->getLogger()->debug('expand all properties for key "' . $key . '"');

            $userAgent = $key;
            $parents   = [$userAgent];

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
                $properties[$propertyName] = $this->trimProperty((string) $browserData[$propertyName]);
            }

            unset($browserData);

            $allDivisions[$key] = $properties;

            if (!isset($properties['Version'])) {
                throw new \UnexpectedValueException('Version property not found for key "' . $key . '"');
            }

            $completeVersions = explode('.', $properties['Version'], 2);

            $properties['MajorVer'] = (string) $completeVersions[0];

            if (isset($completeVersions[1])) {
                $minorVersion = (string) $completeVersions[1];
            } else {
                $minorVersion = '0';
            }

            $properties['MinorVer'] = $minorVersion;

            $allDivisions[$key] = $properties;
        }

        return $allDivisions;
    }

    /**
     * trims the value of a property and converts the string values "true" and "false" to boolean
     *
     * @param string|bool $propertyValue
     *
     * @return string|bool
     */
    public function trimProperty(string $propertyValue)
    {
        switch ($propertyValue) {
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
    public function getLogger() : LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Data\Expander
     */
    public function setLogger(LoggerInterface $logger) : self
    {
        $this->logger = $logger;

        return $this;
    }
}
