<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Browscap\Data\Platform;

/**
 * Class PlatformFactory
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class PlatformFactory
{
    /**
     * Load a platforms.json file and parse it into the platforms data array
     *
     * @param array  $platformData
     * @param array  $json
     * @param string $platformName
     *
     * @throws \RuntimeException         if the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException
     *
     * @return \Browscap\Data\Platform
     */
    public function build(array $platformData, array $json, string $platformName): Platform
    {
        if (!isset($platformData['properties'])) {
            $platformData['properties'] = [];
        }

        if (!array_key_exists('lite', $platformData)) {
            throw new \UnexpectedValueException(
                'the value for "lite" key is missing for the platform with the key "' . $platformName . '"'
            );
        }

        if (!array_key_exists('standard', $platformData)) {
            throw new \UnexpectedValueException(
                'the value for "standard" key is missing for the platform with the key "' . $platformName . '"'
            );
        }

        if (array_key_exists('inherits', $platformData)) {
            $parentName = $platformData['inherits'];

            if (!isset($json['platforms'][$parentName])) {
                throw new \UnexpectedValueException(
                    'parent Platform "' . $parentName . '" is missing for platform "' . $platformName . '"'
                );
            }

            $parentPlatform     = $this->build($json['platforms'][$parentName], $json, $parentName);
            $parentPlatformData = $parentPlatform->getProperties();

            $platformProperties = $platformData['properties'];

            foreach ($platformProperties as $name => $value) {
                if (isset($parentPlatformData[$name])
                    && $parentPlatformData[$name] === $value
                ) {
                    throw new \UnexpectedValueException(
                        'the value for property "' . $name . '" has the same value in the keys "' . $platformName
                        . '" and its parent "' . $platformData['inherits'] . '"'
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
