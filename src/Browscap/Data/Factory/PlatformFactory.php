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
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Data\Factory;

use Browscap\Data\Platform;

/**
 * Class PlatformFactory
 *
 * @category   Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
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
     * @return \Browscap\Data\Platform
     */
    public function build(array $platformData, array $json, $platformName)
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
