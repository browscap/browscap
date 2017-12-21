<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Assert\Assertion;
use Browscap\Data\Platform;

final class PlatformFactory
{
    /**
     * validates the $platformData array and creates Platform objects from it
     *
     * @param array  $platformData     The Platform data for the current object
     * @param array  $dataAllPlatforms The Platform data for all platforms
     * @param string $platformName     The name for the current platform
     *
     * @throws \RuntimeException                if the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException
     * @throws \Assert\AssertionFailedException
     *
     * @return Platform
     */
    public function build(array $platformData, array $dataAllPlatforms, string $platformName) : Platform
    {
        Assertion::isArray($platformData, 'each entry inside the "platforms" structure has to be an array');
        Assertion::keyExists($platformData, 'lite', 'the value for "lite" key is missing for the platform with the key "' . $platformName . '"');
        Assertion::keyExists($platformData, 'standard', 'the value for "standard" key is missing for the platform with the key "' . $platformName . '"');
        Assertion::keyExists($platformData, 'match', 'the value for the "match" key is missing for the platform with the key "' . $platformName . '"');

        if (!array_key_exists('properties', $platformData) && !array_key_exists('inherits', $platformData)) {
            throw new \UnexpectedValueException('required attibute "properties" is missing');
        }

        if (!array_key_exists('properties', $platformData)) {
            $platformData['properties'] = [];
        }

        if (array_key_exists('inherits', $platformData)) {
            Assertion::string($platformData['inherits'], 'parent Platform key has to be a string for platform "' . $platformName . '"');

            $parentName = $platformData['inherits'];

            Assertion::keyExists($dataAllPlatforms, $parentName, 'parent Platform "' . $parentName . '" is missing for platform "' . $platformName . '"');

            $parentPlatform     = $this->build($dataAllPlatforms[$parentName], $dataAllPlatforms, $parentName);
            $parentPlatformData = $parentPlatform->getProperties();

            $platformProperties = $platformData['properties'];

            foreach ($platformProperties as $name => $value) {
                if (isset($parentPlatformData[$name]) && $parentPlatformData[$name] === $value) {
                    throw new \UnexpectedValueException(
                        'the value for property "' . $name . '" has the same value in the keys "' . $platformName
                        . '" and its parent "' . $parentName . '"'
                    );
                }
            }

            $platformData['properties'] = array_merge(
                $parentPlatformData,
                $platformProperties
            );

            if (!$parentPlatform->isLite()) {
                $platformData['lite'] = false;
            }

            if (!$parentPlatform->isStandard()) {
                $platformData['standard'] = false;
            }
        }

        return new Platform(
            $platformData['match'],
            $platformData['properties'],
            $platformData['lite'],
            $platformData['standard']
        );
    }
}
