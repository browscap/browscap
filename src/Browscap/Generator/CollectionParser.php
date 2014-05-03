<?php

namespace Browscap\Generator;

use Psr\Log\LoggerInterface;

/**
 * Class CollectionParser
 *
 * @package Browscap\Generator
 */
class CollectionParser
{
    const TYPE_STRING = 'string';
    const TYPE_GENERIC = 'generic';
    const TYPE_NUMBER = 'number';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_IN_ARRAY = 'in_array';

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
     * @return \Psr\Log\LoggerInterface $logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Generate and return the formatted browscap data
     *
     * @throws \UnexpectedValueException
     * @return array
     */
    public function parse()
    {
        $allDivisions = array();

        foreach ($this->getDataCollection()->getDivisions() as $division) {
            if ($division['division'] == 'Browscap Version') {
                continue;
            }

            if (isset($division['userAgents'][0]['userAgent'])) {
                $this->getLogger()->debug(
                    'parse data collection "' . $division['division'] . '" into an array for division '
                    . '"' . $division['userAgents'][0]['userAgent'] . '"'
                );
            } else {
                $this->getLogger()->debug('parse data collection "' . $division['division'] . '" into an array');
            }

            if (isset($division['lite'])) {
                $lite = $division['lite'];
            } else {
                $lite = false;
            }

            $sortIndex = $division['sortIndex'];

            if (isset($division['versions']) && is_array($division['versions'])) {
                foreach ($division['versions'] as $version) {
                    $dots = explode('.', $version, 2);

                    $majorVer = $dots[0];
                    $minorVer = (isset($dots[1]) ? $dots[1] : 0);

                    $userAgents = json_encode($division['userAgents']);
                    $userAgents = str_replace(
                        array('#MAJORVER#', '#MINORVER#'),
                        array($majorVer, $minorVer),
                        $userAgents
                    );

                    $divisionName = str_replace(
                        array('#MAJORVER#', '#MINORVER#'),
                        array($majorVer, $minorVer),
                        $division['division']
                    );

                    $userAgents = json_decode($userAgents, true);

                    $divisions = $this->parseDivision(
                        $userAgents,
                        $majorVer,
                        $minorVer,
                        $lite,
                        $sortIndex,
                        $divisionName
                    );

                    foreach ($divisions as $key => $divisionData) {
                        if (isset($allDivisions[$key])) {
                            throw new \UnexpectedValueException('Division "' . $key . '" is defined twice');
                        }

                        $allDivisions[$key] = $divisionData;
                    }

                    unset($userAgents, $divisionName, $majorVer, $minorVer);
                }
            } else {
                $divisions = $this->parseDivision(
                    $division['userAgents'],
                    0,
                    0,
                    $lite,
                    $sortIndex,
                    $division['division']
                );

                foreach ($divisions as $key => $divisionData) {
                    if (isset($allDivisions[$key])) {
                        throw new \UnexpectedValueException('Division "' . $key . '" is defined twice');
                    }

                    $allDivisions[$key] = $divisionData;
                }
            }

            unset($sortIndex, $lite);
        }

        // full expand of all data
        return $this->expandProperties($allDivisions);
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
     * @throws \UnexpectedValueException
     * @return array
     */
    private function parseDivision(array $userAgents, $majorVer, $minorVer, $lite, $sortIndex, $divisionName)
    {
        $output = array();

        foreach ($userAgents as $uaData) {
            $parsedAgents = $this->parseUserAgent($uaData, $majorVer, $minorVer, $lite, $sortIndex, $divisionName);

            foreach ($parsedAgents as $key => $parsedAgentData) {
                if (isset($allDivisions[$key])) {
                    throw new \UnexpectedValueException('UserAgent "' . $key . '" is defined twice');
                }

                $output[$key] = $parsedAgentData;
            }
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

        if (!in_array($uaData['userAgent'], array('DefaultProperties', '*'))
            && (array_key_exists('Platform', $uaProperties)
            || array_key_exists('Platform_Description', $uaProperties)
            || array_key_exists('Platform_Maker', $uaProperties)
            || array_key_exists('Platform_Bits', $uaProperties)
            || array_key_exists('Platform_Version', $uaProperties))
        ) {
            throw new \LogicException(
                'the properties array contains platform data for key "' . $uaData['userAgent']
                . '", please use the "platform" keyword'
            );
        }

        if (array_key_exists('platform', $uaData)) {
            $platform     = $this->getDataCollection()->getPlatform($uaData['platform']);
            $platformData = $platform['properties'];
        } else {
            $platformData = array();
        }

        $output = array(
            $uaData['userAgent'] => array_merge(
                array(
                    'lite' => $lite,
                    'sortIndex' => $sortIndex,
                    'division' => $divisionName
                ),
                $platformData,
                $uaProperties
            )
        );

        if (isset($uaData['children']) && is_array($uaData['children'])) {
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

                $properties = array_merge(
                    $this->parseProperties(['Parent' => $ua], $majorVer, $minorVer),
                    $this->parseProperties($platformData['properties'], $majorVer, $minorVer)
                );

                if (isset($uaDataChild['properties'])
                    && is_array($uaDataChild['properties'])
                ) {
                    $childProperties = $this->parseProperties($uaDataChild['properties'], $majorVer, $minorVer);

                    if (array_key_exists('Platform', $childProperties)
                        || array_key_exists('Platform_Description', $childProperties)
                        || array_key_exists('Platform_Maker', $childProperties)
                        || array_key_exists('Platform_Bits', $childProperties)
                        || array_key_exists('Platform_Version', $childProperties)
                    ) {
                        throw new \LogicException(
                            'the properties array contains platform data for key "' . $uaBase
                            . '", please use the "platforms" keyword'
                        );
                    }

                    $properties = array_merge($properties, $childProperties);
                }

                $output[$uaBase] = $properties;
            }
        } else {
            $properties = $this->parseProperties(['Parent' => $ua], $majorVer, $minorVer);

            if (isset($uaDataChild['properties'])
                && is_array($uaDataChild['properties'])
            ) {
                $childProperties = $this->parseProperties($uaDataChild['properties'], $majorVer, $minorVer);

                if (array_key_exists('Platform', $childProperties)
                    || array_key_exists('Platform_Description', $childProperties)
                    || array_key_exists('Platform_Maker', $childProperties)
                    || array_key_exists('Platform_Bits', $childProperties)
                    || array_key_exists('Platform_Version', $childProperties)
                ) {
                    throw new \LogicException(
                        'the properties array contains platform data for key "' . $ua
                        . '", please use the "platforms" keyword'
                    );
                }

                $properties = array_merge($properties, $childProperties);
            }

            $output[$uaDataChild['match']] = $properties;
        }

        return $output;
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
    private function parseProperties(array $properties, $majorVer, $minorVer)
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

        foreach ($allInputDivisions as $key => $properties) {
            $this->getLogger()->debug('expand all properties for key "' . $key . '"');

            if (!isset($properties['Parent'])
                && !in_array($key, array('DefaultProperties', '*'))
            ) {
                throw new \UnexpectedValueException('Parent property is missing for key "' . $key . '"');
            }

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
            $browserData = array();

            foreach ($parents as $parent) {
                if (!isset($allInputDivisions[$parent])) {
                    throw new \UnexpectedValueException('Parent "' . $parent . '" not found for key "' . $key . '"');
                }

                if (!is_array($allInputDivisions[$parent])) {
                    throw new \UnexpectedValueException('Parent "' . $parent . '" is not an array key "' . $key . '"');
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
     * Get the type of a property
     *
     * @param string $propertyName
     * @throws \Exception
     * @return string
     */
    public static function getPropertyType($propertyName)
    {
        switch ($propertyName) {
            case 'Comment':
            case 'Browser':
            case 'Browser_Maker':
            case 'Browser_Modus':
            case 'Platform':
            case 'Platform_Name':
            case 'Platform_Description':
            case 'Device_Name':
            case 'Platform_Maker':
            case 'Device_Code_Name':
            case 'Device_Maker':
            case 'Device_Brand_Name':
            case 'RenderingEngine_Name':
            case 'RenderingEngine_Description':
            case 'RenderingEngine_Maker':
            case 'Parent':
                return self::TYPE_STRING;
            case 'Browser_Type':
            case 'Device_Type':
            case 'Device_Pointing_Method':
            case 'Browser_Bits':
            case 'Platform_Bits':
                return self::TYPE_IN_ARRAY;
            case 'Platform_Version':
            case 'RenderingEngine_Version':
                return self::TYPE_GENERIC;
            case 'Version':
            case 'CssVersion':
            case 'AolVersion':
            case 'MajorVer':
            case 'MinorVer':
                return self::TYPE_NUMBER;
            case 'Alpha':
            case 'Beta':
            case 'Win16':
            case 'Win32':
            case 'Win64':
            case 'Frames':
            case 'IFrames':
            case 'Tables':
            case 'Cookies':
            case 'BackgroundSounds':
            case 'JavaScript':
            case 'VBScript':
            case 'JavaApplets':
            case 'ActiveXControls':
            case 'isMobileDevice':
            case 'isTablet':
            case 'isSyndicationReader':
            case 'Crawler':
                return self::TYPE_BOOLEAN;
            default:
                // do nothing here
        }

        throw new \InvalidArgumentException("Property {$propertyName} did not have a defined property type");
    }

    /**
     * Determine if the specified property is an "extra" property (that should
     * be included in the "full" versions of the files)
     *
     * @param string $propertyName
     * @return boolean
     */
    public static function isExtraProperty($propertyName)
    {
        switch ($propertyName) {
            case 'Browser_Type':
            case 'Browser_Bits':
            case 'Browser_Maker':
            case 'Browser_Modus':
            case 'Platform_Name':
            case 'Platform_Bits':
            case 'Platform_Maker':
            case 'Device_Code_Name':
            case 'Device_Brand_Name':
            case 'Device_Name':
            case 'Device_Maker':
            case 'Device_Type':
            case 'Device_Pointing_Method':
            case 'Platform_Description':
            case 'RenderingEngine_Name':
            case 'RenderingEngine_Version':
            case 'RenderingEngine_Description':
            case 'RenderingEngine_Maker':
                return true;
            default:
                // do nothing here
        }

        return false;
    }

    /**
     * Determine if the specified property is an "extra" property (that should
     * be included in the "full" versions of the files)
     *
     * @param string $propertyName
     * @return boolean
     */
    public static function isOutputProperty($propertyName)
    {
        switch ($propertyName) {
            case 'lite':
            case 'sortIndex':
            case 'Parents':
            case 'division':
                return false;
            default:
                // do nothing here
        }

        return true;
    }

    /**
     * @param string $property
     * @param string $value
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function checkValueInArray($property, $value)
    {
        switch ($property) {
            case 'Browser_Type':
                $allowedValues = array(
                    'Useragent Anonymizer',
                    'Browser',
                    'Offline Browser',
                    'Multimedia Player',
                    'Library',
                    'Feed Reader',
                    'Email Client',
                    'Bot/Crawler',
                    'Application',
                    'unknown',
                );
                break;
            case 'Device_Type':
                $allowedValues = array(
                    'Console',
                    'TV Device',
                    'Tablet',
                    'Mobile Phone',
                    'Mobile Device',
                    'FonePad', // Tablet sized device with the capability to make phone calls
                    'Desktop',
                    'Ebook Reader',
                    'unknown',
                );
                break;
            case 'Device_Pointing_Method':
                // This property is taken from http://www.scientiamobile.com/wurflCapability
                $allowedValues = array(
                    'joystick', 'stylus', 'touchscreen', 'clickwheel', 'trackpad', 'trackball', 'mouse', 'unknown'
                );
                break;
            case 'Browser_Bits':
            case 'Platform_Bits':
                $allowedValues = array(
                    '0', '8', '16', '32', '64'
                );
                break;
            default:
                throw new \InvalidArgumentException('Property "' . $property . '" is not defined to be validated');
                break;
        }

        if (in_array($value, $allowedValues)) {
            return $value;
        }

        throw new \InvalidArgumentException(
            'invalid value given for Property "' . $property . '": given value "' . (string) $value . '", allowed: '
            . json_encode($allowedValues)
        );
    }
}
