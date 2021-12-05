<?php

declare(strict_types=1);

namespace Browscap\Data\Factory;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Browscap\Data\Platform;
use RuntimeException;
use UnexpectedValueException;

use function array_key_exists;
use function array_merge;
use function assert;
use function is_string;

/**
 * @phpstan-import-type PlatformProperties from Platform
 * @phpstan-import-type PlatformData from Platform
 */
final class PlatformFactory
{
    /**
     * validates the $platformData array and creates Platform objects from it
     *
     * @param mixed[]   $platformData     The Platform data for the current object
     * @param mixed[][] $dataAllPlatforms The Platform data for all platforms
     * @param string    $platformName     The name for the current platform
     * @phpstan-param PlatformData $platformData
     * @phpstan-param array<string, PlatformData> $dataAllPlatforms
     *
     * @throws RuntimeException if the file does not exist or has invalid JSON.
     * @throws UnexpectedValueException
     * @throws AssertionFailedException
     */
    public function build(array $platformData, array $dataAllPlatforms, string $platformName): Platform
    {
        Assertion::isArray($platformData, 'each entry has to be an array');
        Assertion::keyExists($platformData, 'lite', 'the value for "lite" key is missing for the platform with the key "' . $platformName . '"');
        Assertion::keyExists($platformData, 'standard', 'the value for "standard" key is missing for the platform with the key "' . $platformName . '"');
        Assertion::keyExists($platformData, 'match', 'the value for the "match" key is missing for the platform with the key "' . $platformName . '"');

        if (! array_key_exists('properties', $platformData) && ! array_key_exists('inherits', $platformData)) {
            throw new UnexpectedValueException('required attibute "properties" is missing');
        }

        if (! array_key_exists('properties', $platformData)) {
            $platformData['properties'] = [];
        }

        if (array_key_exists('inherits', $platformData)) {
            Assertion::string($platformData['inherits'], 'parent Platform key has to be a string for platform "' . $platformName . '"');

            $parentName = $platformData['inherits'];

            Assertion::keyExists($dataAllPlatforms, $parentName, 'parent Platform "' . $parentName . '" is missing for platform "' . $platformName . '"');

            $parentPlatform     = $this->build($dataAllPlatforms[$parentName], $dataAllPlatforms, $parentName);
            $parentPlatformData = $parentPlatform->getProperties();

            /** @phpstan-var PlatformProperties $platformProperties */
            $platformProperties = $platformData['properties'];

            foreach ($platformProperties as $name => $value) {
                assert(is_string($name));

                if (array_key_exists($name, $parentPlatformData) && $parentPlatformData[$name] === $value) {
                    throw new UnexpectedValueException(
                        'the value for property "' . $name . '" has the same value in the keys "' . $platformName
                        . '" and its parent "' . $parentName . '"'
                    );
                }
            }

            $platformData['properties'] = array_merge(
                $parentPlatformData,
                $platformProperties
            );

            if (! $parentPlatform->isLite()) {
                $platformData['lite'] = false;
            }

            if (! $parentPlatform->isStandard()) {
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
