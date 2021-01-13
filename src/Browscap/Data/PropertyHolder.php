<?php

declare(strict_types=1);

namespace Browscap\Data;

use Browscap\Writer\WriterInterface;
use InvalidArgumentException;

use function in_array;
use function json_encode;
use function sprintf;

class PropertyHolder
{
    public const TYPE_STRING   = 'string';
    public const TYPE_GENERIC  = 'generic';
    public const TYPE_NUMBER   = 'number';
    public const TYPE_BOOLEAN  = 'boolean';
    public const TYPE_IN_ARRAY = 'in_array';

    /**
     * Get the type of a property
     *
     * @throws InvalidArgumentException
     */
    public function getPropertyType(string $propertyName): string
    {
        $stringProperties = [
            'Comment' => 1,
            'Browser' => 1,
            'Browser_Maker' => 1,
            'Browser_Modus' => 1,
            'Browser_Type' => 1,
            'Platform' => 1,
            'Platform_Name' => 1,
            'Platform_Description' => 1,
            'Device_Name' => 1,
            'Platform_Maker' => 1,
            'Device_Code_Name' => 1,
            'Device_Maker' => 1,
            'Device_Brand_Name' => 1,
            'Device_Type' => 1,
            'RenderingEngine_Name' => 1,
            'RenderingEngine_Description' => 1,
            'RenderingEngine_Maker' => 1,
            'Parent' => 1,
            'PropertyName' => 1,
            'PatternId' => 1,
        ];

        if (isset($stringProperties[$propertyName])) {
            return self::TYPE_STRING;
        }

        $arrayProperties = [
            'Device_Pointing_Method' => 1,
            'Browser_Bits' => 1,
            'Platform_Bits' => 1,
        ];

        if (isset($arrayProperties[$propertyName])) {
            return self::TYPE_IN_ARRAY;
        }

        $genericProperties = [
            'Platform_Version' => 1,
            'RenderingEngine_Version' => 1,
        ];

        if (isset($genericProperties[$propertyName])) {
            return self::TYPE_GENERIC;
        }

        $numericProperties = [
            'Version' => 1,
            'CssVersion' => 1,
            'AolVersion' => 1,
            'MajorVer' => 1,
            'MinorVer' => 1,
        ];

        if (isset($numericProperties[$propertyName])) {
            return self::TYPE_NUMBER;
        }

        $booleanProperties = [
            'Alpha' => 1,
            'Beta' => 1,
            'Win16' => 1,
            'Win32' => 1,
            'Win64' => 1,
            'Frames' => 1,
            'IFrames' => 1,
            'Tables' => 1,
            'Cookies' => 1,
            'BackgroundSounds' => 1,
            'JavaScript' => 1,
            'VBScript' => 1,
            'JavaApplets' => 1,
            'ActiveXControls' => 1,
            'isMobileDevice' => 1,
            'isTablet' => 1,
            'isSyndicationReader' => 1,
            'Crawler' => 1,
            'MasterParent' => 1,
            'LiteMode' => 1,
            'isFake' => 1,
            'isAnonymized' => 1,
            'isModified' => 1,
        ];

        if (isset($booleanProperties[$propertyName])) {
            return self::TYPE_BOOLEAN;
        }

        throw new InvalidArgumentException(sprintf('Property %s did not have a defined property type', $propertyName));
    }

    /**
     * Determine if the specified property is an property that should
     * be included in the all versions of the files
     */
    public function isLiteModeProperty(string $propertyName, WriterInterface $writer): bool
    {
        $outputProperties = [
            'Parent' => 1,
            'Comment' => 1,
            'Browser' => 1,
            'Platform' => 1,
            'Version' => 1,
            'isMobileDevice' => 1,
            'isTablet' => 1,
            'Device_Type' => 1,
        ];

        if (isset($outputProperties[$propertyName])) {
            return true;
        }

        if ($writer->getType() === WriterInterface::TYPE_INI) {
            $additionalProperties = ['PatternId'];

            if (in_array($propertyName, $additionalProperties)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the specified property is an property that should
     * be included in the "standard" or "full" versions of the files only
     */
    public function isStandardModeProperty(string $propertyName, WriterInterface $writer): bool
    {
        $outputProperties = [
            'MajorVer' => 1,
            'MinorVer' => 1,
            'Crawler' => 1,
            'Browser_Maker' => 1,
            'Device_Pointing_Method' => 1,
        ];

        if (isset($outputProperties[$propertyName])) {
            return true;
        }

        if (in_array($writer->getType(), [WriterInterface::TYPE_CSV, WriterInterface::TYPE_XML])) {
            $additionalProperties = ['PropertyName', 'MasterParent', 'LiteMode'];

            if (in_array($propertyName, $additionalProperties)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the specified property is an "extra" property (that should
     * be included in any version of the files)
     */
    public function isOutputProperty(string $propertyName, WriterInterface $writer): bool
    {
        $outputProperties = [
            'Comment' => 1,
            'Browser' => 1,
            'Browser_Maker' => 1,
            'Browser_Modus' => 1,
            'Platform' => 1,
            'Platform_Name' => 1,
            'Platform_Description' => 1,
            'Device_Name' => 1,
            'Platform_Maker' => 1,
            'Device_Code_Name' => 1,
            'Device_Maker' => 1,
            'Device_Brand_Name' => 1,
            'RenderingEngine_Name' => 1,
            'RenderingEngine_Description' => 1,
            'RenderingEngine_Maker' => 1,
            'Parent' => 1,
            'Browser_Type' => 1,
            'Device_Type' => 1,
            'Device_Pointing_Method' => 1,
            'Browser_Bits' => 1,
            'Platform_Bits' => 1,
            'Platform_Version' => 1,
            'RenderingEngine_Version' => 1,
            'Version' => 1,
            'CssVersion' => 1,
            'AolVersion' => 1,
            'MajorVer' => 1,
            'MinorVer' => 1,
            'Alpha' => 1,
            'Beta' => 1,
            'Win16' => 1,
            'Win32' => 1,
            'Win64' => 1,
            'Frames' => 1,
            'IFrames' => 1,
            'Tables' => 1,
            'Cookies' => 1,
            'BackgroundSounds' => 1,
            'JavaScript' => 1,
            'VBScript' => 1,
            'JavaApplets' => 1,
            'ActiveXControls' => 1,
            'isMobileDevice' => 1,
            'isTablet' => 1,
            'isSyndicationReader' => 1,
            'Crawler' => 1,
            'isFake' => 1,
            'isAnonymized' => 1,
            'isModified' => 1,
        ];

        if (isset($outputProperties[$propertyName])) {
            return true;
        }

        if (in_array($writer->getType(), [WriterInterface::TYPE_CSV, WriterInterface::TYPE_XML])) {
            $additionalProperties = ['PropertyName', 'MasterParent', 'LiteMode'];

            if (in_array($propertyName, $additionalProperties)) {
                return true;
            }
        }

        if ($writer->getType() === WriterInterface::TYPE_INI) {
            if ($propertyName === 'PatternId') {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the specified property is marked as "deprecated"
     */
    public function isDeprecatedProperty(string $propertyName): bool
    {
        $deprecatedProperties = [
            'Win16' => 1,
            'Win32' => 1,
            'Win64' => 1,
            'isMobileDevice' => 1,
            'isTablet' => 1,
            'Crawler' => 1,
            'AolVersion' => 1,
            'MajorVer' => 1,
            'MinorVer' => 1,
        ];

        return isset($deprecatedProperties[$propertyName]);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function checkValueInArray(string $property, string $value): string
    {
        switch ($property) {
            case 'Device_Pointing_Method':
                // This property is taken from http://www.scientiamobile.com/wurflCapability
                $allowedValues = [
                    'joystick',
                    'stylus',
                    'touchscreen',
                    'clickwheel',
                    'trackpad',
                    'trackball',
                    'mouse',
                    'unknown',
                ];

                break;
            case 'Browser_Bits':
            case 'Platform_Bits':
                $allowedValues = [
                    0,
                    8,
                    16,
                    32,
                    64,
                ];

                break;
            default:
                throw new InvalidArgumentException('Property "' . $property . '" is not defined to be validated');
        }

        if (in_array($value, $allowedValues)) {
            return $value;
        }

        throw new InvalidArgumentException(
            'invalid value given for Property "' . $property . '": given value "' . $value . '", allowed: '
            . json_encode($allowedValues)
        );
    }
}
