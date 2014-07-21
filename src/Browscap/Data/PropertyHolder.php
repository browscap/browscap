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

/**
 * Class PropertyHolder
 *
 * @category   Browscap
 * @package    Data
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
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
    public function getPropertyType($propertyName)
    {
        $stringProperties = array(
            'Comment',
            'Browser',
            'Browser_Maker',
            'Browser_Modus',
            'Platform',
            'Platform_Name',
            'Platform_Description',
            'Device_Name',
            'Platform_Maker',
            'Device_Code_Name',
            'Device_Maker',
            'Device_Brand_Name',
            'RenderingEngine_Name',
            'RenderingEngine_Description',
            'RenderingEngine_Maker',
            'Parent',
            'PropertyName',
        );

        if (in_array($propertyName, $stringProperties)) {
            return self::TYPE_STRING;
        }

        $arrayProperties = array(
            'Browser_Type',
            'Device_Type',
            'Device_Pointing_Method',
            'Browser_Bits',
            'Platform_Bits',
        );

        if (in_array($propertyName, $arrayProperties)) {
            return self::TYPE_IN_ARRAY;
        }

        $genericProperties = array(
            'Platform_Version',
            'RenderingEngine_Version',
        );

        if (in_array($propertyName, $genericProperties)) {
            return self::TYPE_GENERIC;
        }

        $numericProperties = array(
            'Version',
            'CssVersion',
            'AolVersion',
            'MajorVer',
            'MinorVer',
        );

        if (in_array($propertyName, $numericProperties)) {
            return self::TYPE_NUMBER;
        }

        $booleanProperties = array(
            'Alpha',
            'Beta',
            'Win16',
            'Win32',
            'Win64',
            'Frames',
            'IFrames',
            'Tables',
            'Cookies',
            'BackgroundSounds',
            'JavaScript',
            'VBScript',
            'JavaApplets',
            'ActiveXControls',
            'isMobileDevice',
            'isTablet',
            'isSyndicationReader',
            'Crawler',
            'MasterParent',
            'LiteMode',
        );

        if (in_array($propertyName, $booleanProperties)) {
            return self::TYPE_BOOLEAN;
        }

        throw new \InvalidArgumentException("Property {$propertyName} did not have a defined property type");
    }

    /**
     * Determine if the specified property is an "extra" property (that should
     * be included in the "full" versions of the files)
     *
     * @param string $propertyName
     * @param \Browscap\Writer\WriterInterface $writer
     * @return boolean
     */
    public function isExtraProperty($propertyName, \Browscap\Writer\WriterInterface $writer = null)
    {
        if (null !== $writer && in_array($writer->getType(), array('csv', 'xml'))) {
            $additionalProperties = array('PropertyName', 'MasterParent', 'LiteMode');

            if (in_array($propertyName, $additionalProperties)) {
                return false;
            }
        }

        $extraProperties = array(
            'Browser_Type',
            'Browser_Bits',
            'Browser_Maker',
            'Browser_Modus',
            'Platform_Name',
            'Platform_Bits',
            'Platform_Maker',
            'Device_Code_Name',
            'Device_Brand_Name',
            'Device_Name',
            'Device_Maker',
            'Device_Type',
            'Device_Pointing_Method',
            'Platform_Description',
            'RenderingEngine_Name',
            'RenderingEngine_Version',
            'RenderingEngine_Description',
            'RenderingEngine_Maker',
        );

        if (in_array($propertyName, $extraProperties)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the specified property is an "extra" property (that should
     * be included in the "full" versions of the files)
     *
     * @param string $propertyName
     * @param \Browscap\Writer\WriterInterface $writer
     * @return boolean
     */
    public function isOutputProperty($propertyName, \Browscap\Writer\WriterInterface $writer = null)
    {
        $outputProperties = array(
            'Comment',
            'Browser',
            'Browser_Maker',
            'Browser_Modus',
            'Platform',
            'Platform_Name',
            'Platform_Description',
            'Device_Name',
            'Platform_Maker',
            'Device_Code_Name',
            'Device_Maker',
            'Device_Brand_Name',
            'RenderingEngine_Name',
            'RenderingEngine_Description',
            'RenderingEngine_Maker',
            'Parent',
            'Browser_Type',
            'Device_Type',
            'Device_Pointing_Method',
            'Browser_Bits',
            'Platform_Bits',
            'Platform_Version',
            'RenderingEngine_Version',
            'Version',
            'CssVersion',
            'AolVersion',
            'MajorVer',
            'MinorVer',
            'Alpha',
            'Beta',
            'Win16',
            'Win32',
            'Win64',
            'Frames',
            'IFrames',
            'Tables',
            'Cookies',
            'BackgroundSounds',
            'JavaScript',
            'VBScript',
            'JavaApplets',
            'ActiveXControls',
            'isMobileDevice',
            'isTablet',
            'isSyndicationReader',
            'Crawler',
        );

        if (in_array($propertyName, $outputProperties)) {
            return true;
        }
        
        if (null !== $writer && in_array($writer->getType(), array('csv', 'xml'))) {
            $additionalProperties = array('PropertyName', 'MasterParent', 'LiteMode');

            if (in_array($propertyName, $additionalProperties)) {
                return true;
            }
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
    public function checkValueInArray($property, $value)
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
