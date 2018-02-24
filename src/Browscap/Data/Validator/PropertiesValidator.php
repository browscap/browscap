<?php
declare(strict_types = 1);
namespace Browscap\Data\Validator;

use Assert\Assertion;

final class PropertiesValidator implements ValidatorInterface
{
    /**
     * validates the fully expanded properties
     *
     * @param array  $properties Data to validate
     * @param string $key
     *
     * @throws \LogicException
     * @throws \Assert\AssertionFailedException
     */
    public function validate(array $properties, string $key) : void
    {
        if (!in_array($key, ['DefaultProperties', '*'])) {
            Assertion::keyExists($properties, 'Parent', 'property "Parent" is missing for key "' . $key . '"');
            Assertion::string($properties['Parent'], 'property "Parent" must be a String for key "' . $key . '", got "%s"');
        }

        Assertion::keyExists($properties, 'Comment', 'property "Comment" not found for key "' . $key . '"');
        Assertion::string($properties['Comment'], 'property "Comment" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Browser', 'property "Browser" not found for key "' . $key . '"');
        Assertion::string($properties['Browser'], 'property "Browser" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Browser_Type', 'property "Browser_Type" not found for key "' . $key . '"');
        Assertion::string($properties['Browser_Type'], 'property "Browser_Type" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Browser_Bits', 'property "Browser_Bits" not found for key "' . $key . '"');
        Assertion::integer($properties['Browser_Bits'], 'property "Browser_Bits" must be a Integer for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Browser_Maker', 'property "Browser_Maker" not found for key "' . $key . '"');
        Assertion::string($properties['Browser_Maker'], 'property "Browser_Maker" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Browser_Modus', 'property "Browser_Modus" not found for key "' . $key . '"');
        Assertion::string($properties['Browser_Modus'], 'property "Browser_Modus" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Version', 'property "Version" not found for key "' . $key . '"');
        Assertion::string($properties['Version'], 'property "Version" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'MajorVer', 'property "MajorVer" not found for key "' . $key . '"');
        Assertion::string($properties['MajorVer'], 'property "MajorVer" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'MinorVer', 'property "MinorVer" not found for key "' . $key . '"');
        Assertion::string($properties['MinorVer'], 'property "MinorVer" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Platform', 'property "Platform" not found for key "' . $key . '"');
        Assertion::string($properties['Platform'], 'property "Platform" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Platform_Version', 'property "Platform_Version" not found for key "' . $key . '"');
        Assertion::string($properties['Platform_Version'], 'property "Platform_Version" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Platform_Description', 'property "Platform_Description" not found for key "' . $key . '"');
        Assertion::string($properties['Platform_Description'], 'property "Platform_Description" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Platform_Bits', 'property "Platform_Bits" not found for key "' . $key . '"');
        Assertion::integer($properties['Platform_Bits'], 'property "Platform_Bits" must be a Integer for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Platform_Maker', 'property "Platform_Maker" not found for key "' . $key . '"');
        Assertion::string($properties['Platform_Maker'], 'property "Platform_Maker" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Alpha', 'property "Alpha" not found for key "' . $key . '"');
        Assertion::boolean($properties['Alpha'], 'property "Alpha" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Beta', 'property "Beta" not found for key "' . $key . '"');
        Assertion::boolean($properties['Beta'], 'property "Beta" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Win16', 'property "Win16" not found for key "' . $key . '"');
        Assertion::boolean($properties['Win16'], 'property "Win16" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Win32', 'property "Win32" not found for key "' . $key . '"');
        Assertion::boolean($properties['Win32'], 'property "Win32" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Win64', 'property "Win64" not found for key "' . $key . '"');
        Assertion::boolean($properties['Win64'], 'property "Win64" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Frames', 'property "Frames" not found for key "' . $key . '"');
        Assertion::boolean($properties['Frames'], 'property "Frames" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'IFrames', 'property "IFrames" not found for key "' . $key . '"');
        Assertion::boolean($properties['IFrames'], 'property "IFrames" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Tables', 'property "Tables" not found for key "' . $key . '"');
        Assertion::boolean($properties['Tables'], 'property "Tables" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Cookies', 'property "Cookies" not found for key "' . $key . '"');
        Assertion::boolean($properties['Cookies'], 'property "Cookies" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'BackgroundSounds', 'property "BackgroundSounds" not found for key "' . $key . '"');
        Assertion::boolean($properties['BackgroundSounds'], 'property "BackgroundSounds" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'JavaScript', 'property "JavaScript" not found for key "' . $key . '"');
        Assertion::boolean($properties['JavaScript'], 'property "JavaScript" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'VBScript', 'property "VBScript" not found for key "' . $key . '"');
        Assertion::boolean($properties['VBScript'], 'property "VBScript" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'JavaApplets', 'property "JavaApplets" not found for key "' . $key . '"');
        Assertion::boolean($properties['JavaApplets'], 'property "JavaApplets" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'ActiveXControls', 'property "ActiveXControls" not found for key "' . $key . '"');
        Assertion::boolean($properties['ActiveXControls'], 'property "ActiveXControls" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'isMobileDevice', 'property "isMobileDevice" is missing for key "' . $key . '"');
        Assertion::boolean($properties['isMobileDevice'], 'property "isMobileDevice" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'isTablet', 'property "isTablet" is missing for key "' . $key . '"');
        Assertion::boolean($properties['isTablet'], 'property "isTablet" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'isSyndicationReader', 'property "isSyndicationReader" not found for key "' . $key . '"');
        Assertion::boolean($properties['isSyndicationReader'], 'property "isSyndicationReader" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Crawler', 'property "Crawler" not found for key "' . $key . '"');
        Assertion::boolean($properties['Crawler'], 'property "Crawler" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'isFake', 'property "isFake" not found for key "' . $key . '"');
        Assertion::boolean($properties['isFake'], 'property "isFake" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'isAnonymized', 'property "isAnonymized" not found for key "' . $key . '"');
        Assertion::boolean($properties['isAnonymized'], 'property "isAnonymized" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'isModified', 'property "isModified" not found for key "' . $key . '"');
        Assertion::boolean($properties['isModified'], 'property "isModified" must be a Boolean for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'CssVersion', 'property "CssVersion" not found for key "' . $key . '"');
        Assertion::integer($properties['CssVersion'], 'property "CssVersion" must be a Integer for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'AolVersion', 'property "AolVersion" not found for key "' . $key . '"');
        Assertion::integer($properties['AolVersion'], 'property "AolVersion" must be a Integer for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Device_Name', 'property "Device_Name" not found for key "' . $key . '"');
        Assertion::string($properties['Device_Name'], 'property "Device_Name" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Device_Maker', 'property "Device_Maker" not found for key "' . $key . '"');
        Assertion::string($properties['Device_Maker'], 'property "Device_Maker" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Device_Type', 'property "Device_Type" is missing for key "' . $key . '"');
        Assertion::string($properties['Device_Type'], 'property "Device_Type" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Device_Pointing_Method', 'property "Device_Pointing_Method" not found for key "' . $key . '"');
        Assertion::string($properties['Device_Pointing_Method'], 'property "Device_Pointing_Method" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Device_Code_Name', 'property "Device_Code_Name" not found for key "' . $key . '"');
        Assertion::string($properties['Device_Code_Name'], 'property "Device_Code_Name" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'Device_Brand_Name', 'property "Device_Brand_Name" not found for key "' . $key . '"');
        Assertion::string($properties['Device_Brand_Name'], 'property "Device_Brand_Name" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'RenderingEngine_Name', 'property "RenderingEngine_Name" not found for key "' . $key . '"');
        Assertion::string($properties['RenderingEngine_Name'], 'property "RenderingEngine_Name" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'RenderingEngine_Version', 'property "RenderingEngine_Version" not found for key "' . $key . '"');
        Assertion::string($properties['RenderingEngine_Version'], 'property "RenderingEngine_Version" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'RenderingEngine_Description', 'property "RenderingEngine_Description" not found for key "' . $key . '"');
        Assertion::string($properties['RenderingEngine_Description'], 'property "RenderingEngine_Description" must be a String for key "' . $key . '", got "%s"');

        Assertion::keyExists($properties, 'RenderingEngine_Maker', 'property "RenderingEngine_Maker" not found for key "' . $key . '"');
        Assertion::string($properties['RenderingEngine_Maker'], 'property "RenderingEngine_Maker" must be a String for key "' . $key . '", got "%s"');
    }
}
