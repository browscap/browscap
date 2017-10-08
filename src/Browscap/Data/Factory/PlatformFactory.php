<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Assert\Assertion;
use Browscap\Data\Platform;

/**
 * Class PlatformFactory
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class PlatformFactory
{
    /**
     * validates the $platformData array and creates Platform objects from it
     *
     * @param array  $platformData The Platform data for the current object
     * @param array  $json         The Platform data for all platforms
     * @param string $platformName The name for the current platform
     *
     * @throws \RuntimeException         if the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException
     *
     * @return Platform
     */
    public function build(array $platformData, array $json, string $platformName) : Platform
    {
        if (!isset($platformData['properties'])) {
            $platformData['properties'] = [];
        }

        Assertion::keyExists($platformData, 'lite', 'the value for "lite" key is missing for the platform with the key "' . $platformName . '"');
        Assertion::keyExists($platformData, 'standard', 'the value for "standard" key is missing for the platform with the key "' . $platformName . '"');
        Assertion::keyExists($platformData, 'match', 'the value for the "match" key is missing for the platform with the key "' . $platformName . '"');

        if (array_key_exists('inherits', $platformData)) {
            $parentName = $platformData['inherits'];

            Assertion::keyExists($json, $parentName, 'parent Platform "' . $parentName . '" is missing for platform "' . $platformName . '"');

            $parentPlatform     = $this->build($json[$parentName], $json, $parentName);
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
