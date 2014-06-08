<?php

namespace Browscap\Generator;

use Browscap\CollectionParser\ChildrenParserInterface;
use Browscap\CollectionParser\DefaultParser;
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
    private $collection = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @var \Browscap\CollectionParser\ChildrenParserInterface
     */
    private $childrenParser = null;

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
     * @param \Browscap\CollectionParser\ChildrenParserInterface $childrenParser
     *
     * @return \Browscap\Generator\CollectionParser
     */
    public function setChildrenParser(ChildrenParserInterface $childrenParser)
    {
        $this->childrenParser = $childrenParser;

        return $this;
    }

    /**
     * @return \Browscap\CollectionParser\ChildrenParserInterface
     */
    public function getChildrenParser()
    {
        if (null === $this->childrenParser) {
            $this->childrenParser = new DefaultParser();
        }

        return $this->childrenParser;
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

        $this->getChildrenParser()
            ->setDataCollection($this->getDataCollection())
            ->setLogger($this->logger)
        ;

        foreach ($this->getDataCollection()->getDivisions() as $filename => $division) {
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
            $versions  = $this->getChildrenParser()->getDivisions($division);

            foreach ($versions as $version) {
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

                $allDivisions = $this->getChildrenParser()->handleSingleDivision(
                    $allDivisions,
                    $userAgents,
                    $majorVer,
                    $minorVer,
                    $lite,
                    $sortIndex,
                    $divisionName,
                    $filename
                );

                unset($userAgents, $divisionName, $majorVer, $minorVer);
            }

            unset($sortIndex, $lite);
        }

        // full expand of all data
        return $this->expandProperties($allDivisions);
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
                    throw new \UnexpectedValueException(
                        'Parent "' . $parent . '" is not an array for key "' . $key . '"'
                    );
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
