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

use Browscap\Writer\WriterInterface;

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
            'Comment'                     => 1,
            'Browser'                     => 1,
            'Browser_Maker'               => 1,
            'Browser_Modus'               => 1,
            'Platform'                    => 1,
            'Platform_Name'               => 1,
            'Platform_Description'        => 1,
            'Device_Name'                 => 1,
            'Platform_Maker'              => 1,
            'Device_Code_Name'            => 1,
            'Device_Maker'                => 1,
            'Device_Brand_Name'           => 1,
            'RenderingEngine_Name'        => 1,
            'RenderingEngine_Description' => 1,
            'RenderingEngine_Maker'       => 1,
            'Parent'                      => 1,
            'PropertyName'                => 1,
        );

        if (isset($stringProperties[$propertyName])) {
            return self::TYPE_STRING;
        }

        $arrayProperties = array(
            'Browser_Type'           => 1,
            'Device_Type'            => 1,
            'Device_Pointing_Method' => 1,
            'Browser_Bits'           => 1,
            'Platform_Bits'          => 1,
        );

        if (isset($arrayProperties[$propertyName])) {
            return self::TYPE_IN_ARRAY;
        }

        $genericProperties = array(
            'Platform_Version'        => 1,
            'RenderingEngine_Version' => 1,
        );

        if (isset($genericProperties[$propertyName])) {
            return self::TYPE_GENERIC;
        }

        $numericProperties = array(
            'Version'    => 1,
            'CssVersion' => 1,
            'AolVersion' => 1,
            'MajorVer'   => 1,
            'MinorVer'   => 1,
        );

        if (isset($numericProperties[$propertyName])) {
            return self::TYPE_NUMBER;
        }

        $booleanProperties = array(
            'Alpha'               => 1,
            'Beta'                => 1,
            'Win16'               => 1,
            'Win32'               => 1,
            'Win64'               => 1,
            'Frames'              => 1,
            'IFrames'             => 1,
            'Tables'              => 1,
            'Cookies'             => 1,
            'BackgroundSounds'    => 1,
            'JavaScript'          => 1,
            'VBScript'            => 1,
            'JavaApplets'         => 1,
            'ActiveXControls'     => 1,
            'isMobileDevice'      => 1,
            'isTablet'            => 1,
            'isSyndicationReader' => 1,
            'Crawler'             => 1,
            'MasterParent'        => 1,
            'LiteMode'            => 1,
        );

        if (isset($booleanProperties[$propertyName])) {
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
    public function isExtraProperty($propertyName, WriterInterface $writer = null)
    {
        if (null !== $writer && in_array($writer->getType(), array('csv', 'xml'))) {
            $additionalProperties = array('PropertyName', 'MasterParent', 'LiteMode');

            if (in_array($propertyName, $additionalProperties)) {
                return false;
            }
        }

        $extraProperties = array(
            'Browser_Type'                => 1,
            'Browser_Bits'                => 1,
            'Browser_Maker'               => 1,
            'Browser_Modus'               => 1,
            'Platform_Name'               => 1,
            'Platform_Bits'               => 1,
            'Platform_Maker'              => 1,
            'Device_Code_Name'            => 1,
            'Device_Brand_Name'           => 1,
            'Device_Name'                 => 1,
            'Device_Maker'                => 1,
            'Device_Type'                 => 1,
            'Device_Pointing_Method'      => 1,
            'Platform_Description'        => 1,
            'RenderingEngine_Name'        => 1,
            'RenderingEngine_Version'     => 1,
            'RenderingEngine_Description' => 1,
            'RenderingEngine_Maker'       => 1,
        );

        if (isset($extraProperties[$propertyName])) {
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
    public function isOutputProperty($propertyName, WriterInterface $writer = null)
    {
        $outputProperties = array(
            'Comment'                     => 1,
            'Browser'                     => 1,
            'Browser_Maker'               => 1,
            'Browser_Modus'               => 1,
            'Platform'                    => 1,
            'Platform_Name'               => 1,
            'Platform_Description'        => 1,
            'Device_Name'                 => 1,
            'Platform_Maker'              => 1,
            'Device_Code_Name'            => 1,
            'Device_Maker'                => 1,
            'Device_Brand_Name'           => 1,
            'RenderingEngine_Name'        => 1,
            'RenderingEngine_Description' => 1,
            'RenderingEngine_Maker'       => 1,
            'Parent'                      => 1,
            'Browser_Type'                => 1,
            'Device_Type'                 => 1,
            'Device_Pointing_Method'      => 1,
            'Browser_Bits'                => 1,
            'Platform_Bits'               => 1,
            'Platform_Version'            => 1,
            'RenderingEngine_Version'     => 1,
            'Version'                     => 1,
            'CssVersion'                  => 1,
            'AolVersion'                  => 1,
            'MajorVer'                    => 1,
            'MinorVer'                    => 1,
            'Alpha'                       => 1,
            'Beta'                        => 1,
            'Win16'                       => 1,
            'Win32'                       => 1,
            'Win64'                       => 1,
            'Frames'                      => 1,
            'IFrames'                     => 1,
            'Tables'                      => 1,
            'Cookies'                     => 1,
            'BackgroundSounds'            => 1,
            'JavaScript'                  => 1,
            'VBScript'                    => 1,
            'JavaApplets'                 => 1,
            'ActiveXControls'             => 1,
            'isMobileDevice'              => 1,
            'isTablet'                    => 1,
            'isSyndicationReader'         => 1,
            'Crawler'                     => 1,
        );

        if (isset($outputProperties[$propertyName])) {
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
