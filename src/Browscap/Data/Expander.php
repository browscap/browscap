<?php

namespace Browscap\Data;

use Psr\Log\LoggerInterface;

/**
 * Class Expander
 *
 * @package Browscap\Data
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
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Data\Expander
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
    
    public function getVersionParts($version)
    {
        $dots = explode('.', $version, 2);

        $majorVer = $dots[0];
        $minorVer = (isset($dots[1]) ? $dots[1] : 0);
        
        return array($majorVer, $minorVer);
    }

    /**
     * @param array   $allDivisions
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
    public function expand(\Browscap\Data\Division $division, $majorVer, $minorVer, $divisionName)
    {
        $allInputDivisions = $this->parseDivision(
            $division->getUserAgents(),
            $majorVer,
            $minorVer,
            $division->getLite(),
            $division->getSortIndex(),
            $divisionName
        );
        
        return $this->expandProperties($allInputDivisions);
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



        // if (!in_array($useragent['properties']['Parent'], $this->allDivision)) {
            // throw new \UnexpectedValueException(
                // 'the parent element "' . $useragent['properties']['Parent']
                // . '" for key "' . $useragent['userAgent'] . '" is not added before the element, '
                // . 'please change the SortIndex'
            // );
        // }

        if (!isset($uaProperties['Parent'])) {
            throw new \LogicException('the "parent" property is missing for key "' . $uaData['userAgent'] . '"');
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

        foreach ($uaData['children'] as $child) {
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
     * @param array  $properties
     * @param string $message
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
     * @return string[]
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
                    'Car Entertainment System',
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
