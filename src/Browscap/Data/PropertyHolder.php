<?php

namespace Browscap\Data;

use Psr\Log\LoggerInterface;

/**
 * Class CollectionParser
 *
 * @package Browscap\Generator
 */
class PropertyHolder
{
    const TYPE_STRING   = 'string';
    const TYPE_GENERIC  = 'generic';
    const TYPE_NUMBER   = 'number';
    const TYPE_BOOLEAN  = 'boolean';
    const TYPE_IN_ARRAY = 'in_array';

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
            case 'Browser_Type':
            case 'Device_Type':
            case 'Device_Pointing_Method':
            case 'Browser_Bits':
            case 'Platform_Bits':
            case 'Platform_Version':
            case 'RenderingEngine_Version':
            case 'Version':
            case 'CssVersion':
            case 'AolVersion':
            case 'MajorVer':
            case 'MinorVer':
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
                return true;
            default:
                // do nothing here
        }

        return false;
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
