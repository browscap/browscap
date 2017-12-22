<?php
declare(strict_types = 1);
namespace Browscap\Data\Validator;

use Assert\Assertion;

class DivisionDataValidator implements ValidatorInterface
{
    /**
     * valdates the structure of a division
     *
     * @param array  $divisionData Data to validate
     * @param string $filename
     * @param array  $allDivisions
     * @param bool   $isCore
     *
     * @throws \Assert\AssertionFailedException
     * @throws \LogicException
     */
    public function validate(
        array $divisionData,
        string $filename,
        array &$allDivisions = [],
        bool $isCore = false
    ) : void {
        Assertion::keyExists($divisionData, 'division', 'required attibute "division" is missing in File ' . $filename);
        Assertion::string($divisionData['division'], 'required attibute "division" has to be a string in File ' . $filename);

        Assertion::keyExists($divisionData, 'sortIndex', 'required attibute "sortIndex" is missing in File ' . $filename);
        Assertion::integer($divisionData['sortIndex'], 'required attibute "sortIndex" has to be a integer in File ' . $filename);

        if (!$isCore) {
            Assertion::greaterThan($divisionData['sortIndex'], 0, 'required attibute "sortIndex" has to be a positive integer in File ' . $filename);
        }

        Assertion::keyExists($divisionData, 'lite', 'required attibute "lite" is missing in File ' . $filename);
        Assertion::boolean($divisionData['lite'], 'required attibute "lite" has to be an boolean in File ' . $filename);

        Assertion::keyExists($divisionData, 'standard', 'required attibute "standard" is missing in File ' . $filename);
        Assertion::boolean($divisionData['standard'], 'required attibute "standard" has to be an boolean in File ' . $filename);

        Assertion::keyExists($divisionData, 'userAgents', 'required attibute "userAgents" is missing in File ' . $filename);
        Assertion::isArray($divisionData['userAgents'], 'required attibute "userAgents" should be an array in File ' . $filename);
        Assertion::notEmpty($divisionData['userAgents'], 'required attibute "userAgents" should be an non-empty array in File ' . $filename);

        if (isset($divisionData['versions']) && is_array($divisionData['versions'])) {
            $versions = $divisionData['versions'];
        } else {
            $versions = ['0.0'];
        }

        foreach ($divisionData['userAgents'] as $index => $useragentData) {
            $this->validateUserAgentSection($useragentData, $versions, $allDivisions, $isCore, $filename, $index);

            $allDivisions[] = $useragentData['userAgent'];
        }
    }

    /**
     * @param array  $useragentData
     * @param array  $versions
     * @param array  $allDivisions
     * @param bool   $isCore
     * @param string $filename
     * @param int    $index
     *
     * @throws \Assert\AssertionFailedException
     * @throws \LogicException
     */
    private function validateUserAgentSection(
        array $useragentData,
        array $versions,
        array $allDivisions,
        bool $isCore,
        string $filename,
        int $index
    ) : void {
        Assertion::keyExists($useragentData, 'userAgent', 'required attibute "userAgent" is missing in userAgents section ' . $index . ' in File ' . $filename);
        Assertion::string($useragentData['userAgent'], 'required attibute "userAgent" has to be a string in userAgents section ' . $index . ' in File ' . $filename);

        if (preg_match('/[\[\]]/', $useragentData['userAgent'])) {
            throw new \LogicException('required attibute "userAgent" includes invalid characters in userAgents section ' . $index . ' in File ' . $filename);
        }

        if (false === mb_strpos($useragentData['userAgent'], '#')
            && in_array($useragentData['userAgent'], $allDivisions)
        ) {
            throw new \LogicException('Division "' . $useragentData['userAgent'] . '" is defined twice in file "' . $filename . '"');
        }

        if ((false !== mb_strpos($useragentData['userAgent'], '#MAJORVER#')
                || false !== mb_strpos($useragentData['userAgent'], '#MINORVER#'))
            && ['0.0'] === $versions
        ) {
            throw new \LogicException(
                'Division "' . $useragentData['userAgent']
                . '" is defined with version placeholders, but no versions are set in file "' . $filename . '"'
            );
        }

        if (false === mb_strpos($useragentData['userAgent'], '#MAJORVER#')
            && false === mb_strpos($useragentData['userAgent'], '#MINORVER#')
            && ['0.0'] !== $versions
            && 1 < count($versions)
        ) {
            throw new \LogicException(
                'Division "' . $useragentData['userAgent']
                . '" is defined without version placeholders, but there are versions set in file "' . $filename . '"'
            );
        }

        Assertion::keyExists($useragentData, 'properties', 'required attibute "properties" is missing in userAgents section ' . $index . ' in File ' . $filename);
        Assertion::isArray($useragentData['properties'], 'required attibute "properties" should be an array in userAgents section ' . $index . ' in File ' . $filename);
        Assertion::notEmpty($useragentData['properties'], 'required attibute "properties" should be an non-empty array in userAgents section ' . $index . ' in File ' . $filename);

        if (!$isCore) {
            Assertion::keyExists($useragentData['properties'], 'Parent', 'the "Parent" property is missing for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"');
            Assertion::same($useragentData['properties']['Parent'], 'DefaultProperties', 'the "Parent" property is not linked to the "DefaultProperties" for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"');
        }

        Assertion::keyExists($useragentData['properties'], 'Comment', 'the "Comment" property is missing for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"');
        Assertion::string($useragentData['properties']['Comment'], 'the "Comment" property has to be a string for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"');

        if (!array_key_exists('Version', $useragentData['properties']) && ['0.0'] !== $versions) {
            throw new \LogicException(
                'the "Version" property is missing for key "' . $useragentData['userAgent'] . '" in file "' . $filename
                . '", but there are defined versions'
            );
        }

        if ($isCore) {
            return;
        }

        if (array_key_exists('Version', $useragentData['properties'])) {
            Assertion::string($useragentData['properties']['Version'], 'the "Version" property has to be a string for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"');

            if ((false !== mb_strpos($useragentData['properties']['Version'], '#MAJORVER#')
                    || false !== mb_strpos($useragentData['properties']['Version'], '#MINORVER#'))
                && ['0.0'] === $versions) {
                throw new \LogicException(
                    'the "Version" property has version placeholders for key "' . $useragentData['userAgent'] . '" in file "' . $filename
                    . '", but no versions are defined'
                );
            }

            if (false === mb_strpos($useragentData['properties']['Version'], '#MAJORVER#')
                && false === mb_strpos($useragentData['properties']['Version'], '#MINORVER#')
                && ['0.0'] !== $versions
                && 1 < count($versions)
            ) {
                throw new \LogicException(
                    'the "Version" property has no version placeholders for key "' . $useragentData['userAgent'] . '" in file "' . $filename
                    . '", but versions are defined'
                );
            }
        }

        $this->checkPlatformProperties(
            $useragentData['properties'],
            'the properties array contains platform data for key "' . $useragentData['userAgent']
            . '", please use the "platform" keyword'
        );

        $this->checkEngineProperties(
            $useragentData['properties'],
            'the properties array contains engine data for key "' . $useragentData['userAgent']
            . '", please use the "engine" keyword'
        );

        $this->checkDeviceProperties(
            $useragentData['properties'],
            'the properties array contains device data for key "' . $useragentData['userAgent']
            . '", please use the "device" keyword'
        );

        $this->checkBrowserProperties(
            $useragentData['properties'],
            'the properties array contains browser data for key "' . $useragentData['userAgent']
            . '", please use the "browser" keyword'
        );

        $this->checkDeprecatedProperties(
            $useragentData['properties'],
            'the properties array contains deprecated properties for key "' . $useragentData['userAgent'] . '"'
        );

        Assertion::keyExists($useragentData, 'children', 'required attibute "children" is missing in userAgents section ' . $index . ' in File ' . $filename);
        Assertion::isArray($useragentData['children'], 'required attibute "children" should be an array in userAgents section ' . $index . ' in File ' . $filename);
        Assertion::notEmpty($useragentData['children'], 'required attibute "children" should be an non-empty array in userAgents section ' . $index . ' in File ' . $filename);

        Assertion::keyNotExists($useragentData['children'], 'match', 'the children property shall not have the "match" entry for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"');

        foreach ($useragentData['children'] as $child) {
            Assertion::isArray($child, 'each entry of the children property has to be an array for key "' . $useragentData['userAgent'] . '"');

            $this->validateChildSection($child, $useragentData, $versions);
        }
    }

    /**
     * @param array $childData     The children section to be validated
     * @param array $useragentData The complete UserAgent section which is the parent of the children section
     * @param array $versions      The versions from the division
     *
     * @throws \LogicException
     * @throws \Assert\AssertionFailedException
     */
    private function validateChildSection(array $childData, array $useragentData, array $versions) : void
    {
        if (array_key_exists('device', $childData) && array_key_exists('devices', $childData)) {
            throw new \LogicException(
                'a child entry may not define both the "device" and the "devices" entries for key "'
                . $useragentData['userAgent'] . '"'
            );
        }

        if (array_key_exists('devices', $childData)) {
            Assertion::isArray($childData['devices'], 'the "devices" entry for key "' . $useragentData['userAgent'] . '" has to be an array');

            if (1 < count($childData['devices'])
                && false === mb_strpos($childData['match'], '#DEVICE#')
            ) {
                throw new \LogicException(
                    'the "devices" entry contains multiple devices but there is no #DEVICE# token for key "'
                    . $useragentData['userAgent'] . '"'
                );
            }
        }

        if (array_key_exists('device', $childData)) {
            Assertion::string($childData['device'], 'the "device" entry has to be a string for key "' . $useragentData['userAgent'] . '"');
        }

        Assertion::keyExists($childData, 'match', 'each entry of the children property requires an "match" entry for key "' . $useragentData['userAgent'] . '"');
        Assertion::string($childData['match'], 'the "match" entry for key "' . $useragentData['userAgent'] . '" has to be a string');

        if (preg_match('/[\[\]]/', $childData['match'])) {
            throw new \LogicException('key "' . $childData['match'] . '" includes invalid characters');
        }

        Assertion::notSame($childData['match'], $useragentData['userAgent'], 'the "match" entry is identical to its parents "userAgent" entry');

        if (false !== mb_strpos($childData['match'], '#PLATFORM#')
            && !array_key_exists('platforms', $childData)
        ) {
            throw new \LogicException(
                'the key "' . $childData['match']
                . '" is defined with platform placeholder, but no platforms are assigned'
            );
        }

        if (array_key_exists('platforms', $childData)) {
            Assertion::isArray($childData['platforms'], 'the "platforms" entry for key "' . $useragentData['userAgent'] . '" has to be an array');

            if (1 < count($childData['platforms'])
                && false === mb_strpos($childData['match'], '#PLATFORM#')
            ) {
                throw new \LogicException(
                    'the "platforms" entry contains multiple platforms but there is no #PLATFORM# token for key "'
                    . $useragentData['userAgent'] . '"'
                );
            }
        }

        if ((false !== mb_strpos($childData['match'], '#MAJORVER#')
                || false !== mb_strpos($childData['match'], '#MINORVER#'))
            && ['0.0'] === $versions
        ) {
            throw new \LogicException(
                'the key "' . $childData['match']
                . '" is defined with version placeholders, but no versions are set'
            );
        }

        if (false === mb_strpos($childData['match'], '#MAJORVER#')
            && false === mb_strpos($childData['match'], '#MINORVER#')
            && ['0.0'] !== $versions
            && 1 < count($versions)
        ) {
            if (!array_key_exists('platforms', $childData)) {
                throw new \LogicException(
                    'the key "' . $childData['match']
                    . '" is defined without version placeholders, but there are versions set'
                );
            }

            $dynamicPlatforms = false;

            foreach ($childData['platforms'] as $platform) {
                if (false !== mb_stripos($platform, 'dynamic')) {
                    $dynamicPlatforms = true;

                    break;
                }
            }

            if (!$dynamicPlatforms) {
                throw new \LogicException(
                    'the key "' . $childData['match']
                    . '" is defined without version placeholders, but there are versions set'
                );
            }
        }

        if (false !== mb_strpos($childData['match'], '#DEVICE#')
            && !array_key_exists('devices', $childData)
        ) {
            throw new \LogicException(
                'the key "' . $childData['match']
                . '" is defined with device placeholder, but no devices are assigned'
            );
        }

        if (array_key_exists('properties', $childData)) {
            Assertion::isArray($childData['properties'], 'the "properties" entry for key "' . $childData['match'] . '" has to be an array');
            Assertion::keyNotExists($childData['properties'], 'Parent', 'the Parent property must not set inside the children array for key "' . $childData['match'] . '"');

            if (array_key_exists('Version', $childData['properties'])
                && array_key_exists('properties', $useragentData)
                && array_key_exists('Version', $useragentData['properties'])
                && $useragentData['properties']['Version'] === $childData['properties']['Version']
            ) {
                throw new \LogicException(
                    'the "Version" property is set for key "' . $childData['match']
                    . '", but was already set for its parent "' . $useragentData['userAgent'] . '" with the same value'
                );
            }

            $this->checkPlatformProperties(
                $childData['properties'],
                'the properties array contains platform data for key "' . $childData['match']
                . '", please use the "platforms" keyword'
            );

            $this->checkEngineProperties(
                $childData['properties'],
                'the properties array contains engine data for key "' . $childData['match']
                . '", please use the "engine" keyword'
            );

            $this->checkDeviceProperties(
                $childData['properties'],
                'the properties array contains device data for key "' . $childData['match']
                . '", please use the "device" or the "devices" keyword'
            );

            $this->checkBrowserProperties(
                $childData['properties'],
                'the properties array contains browser data for key "' . $childData['match']
                . '", please use the "browser" keyword'
            );

            $this->checkDeprecatedProperties(
                $childData['properties'],
                'the properties array contains deprecated properties for key "' . $childData['match'] . '"'
            );
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
    private function checkPlatformProperties(array $properties, string $message) : void
    {
        if (array_key_exists('Platform', $properties)
            || array_key_exists('Platform_Description', $properties)
            || array_key_exists('Platform_Maker', $properties)
            || array_key_exists('Platform_Bits', $properties)
            || array_key_exists('Platform_Version', $properties)
            || array_key_exists('Win16', $properties)
            || array_key_exists('Win32', $properties)
            || array_key_exists('Win64', $properties)
            || array_key_exists('Browser_Bits', $properties)
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
    public function checkEngineProperties(array $properties, string $message) : void
    {
        if (array_key_exists('RenderingEngine_Name', $properties)
            || array_key_exists('RenderingEngine_Version', $properties)
            || array_key_exists('RenderingEngine_Description', $properties)
            || array_key_exists('RenderingEngine_Maker', $properties)
            || array_key_exists('VBScript', $properties)
            || array_key_exists('ActiveXControls', $properties)
            || array_key_exists('BackgroundSounds', $properties)
        ) {
            throw new \LogicException($message);
        }
    }

    /**
     * checks if device properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     */
    public function checkDeviceProperties(array $properties, string $message) : void
    {
        if (array_key_exists('Device_Name', $properties)
            || array_key_exists('Device_Maker', $properties)
            || array_key_exists('Device_Type', $properties)
            || array_key_exists('Device_Pointing_Method', $properties)
            || array_key_exists('Device_Code_Name', $properties)
            || array_key_exists('Device_Brand_Name', $properties)
            || array_key_exists('isMobileDevice', $properties)
            || array_key_exists('isTablet', $properties)
        ) {
            throw new \LogicException($message);
        }
    }

    /**
     * checks if device properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     */
    public function checkBrowserProperties(array $properties, string $message) : void
    {
        if (array_key_exists('Browser', $properties)
            || array_key_exists('Browser_Type', $properties)
            || array_key_exists('Browser_Maker', $properties)
            || array_key_exists('isSyndicationReader', $properties)
            || array_key_exists('Crawler', $properties)
        ) {
            throw new \LogicException($message);
        }
    }

    /**
     * checks if deprecated properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     */
    public function checkDeprecatedProperties(array $properties, string $message) : void
    {
        if (array_key_exists('AolVersion', $properties)
            || array_key_exists('MinorVer', $properties)
            || array_key_exists('MajorVer', $properties)
        ) {
            throw new \LogicException($message);
        }
    }
}
