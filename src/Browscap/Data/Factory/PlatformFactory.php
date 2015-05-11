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

namespace Browscap\Data\Factory;

use Browscap\Data\Platform;
use Browscap\Data\DataCollection;

/**
 * Class PlatformFactory
 *
 * @category   Browscap
 * @package    Data
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class PlatformFactory
{
    /**
     * Load a platforms.json file and parse it into the platforms data array
     *
     * @param array          $platformData
     * @param array          $json
     * @param string         $platformName
     * @param DataCollection $datacollection
     *
     * @return \Browscap\Data\Platform
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException
     */
    public function build(array $platformData, array $json, $platformName, DataCollection $datacollection)
    {
        if (!isset($platformData['properties'])) {
            $platformData['properties'] = array();
        }

        if (!isset($platformData['lite'])) {
            $platformData['lite'] = true;
        }

        if (array_key_exists('inherits', $platformData)) {
            $parentName = $platformData['inherits'];

            if (!isset($json['platforms'][$parentName])) {
                throw new \UnexpectedValueException(
                    'parent Platform "' . $parentName . '" is missing for platform "' . $platformName . '"'
                );
            }

            $parentPlatform     = $this->build($json['platforms'][$parentName], $json, $parentName, $datacollection);
            $parentPlatformData = $parentPlatform->getProperties();

            $platformProperties = $platformData['properties'];

            foreach ($platformProperties as $name => $value) {
                if (isset($parentPlatformData[$name])
                    && $parentPlatformData[$name] == $value
                ) {
                    throw new \UnexpectedValueException(
                        'the value for property "' . $name .'" has the same value in the keys "' . $platformName
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
        }

        if (array_key_exists('device', $platformData)) {
            $deviceName = $platformData['device'];

            $deviceData = $datacollection->getDevice($deviceName);

            $platformData['properties'] = array_merge(
                $deviceData->getProperties(),
                $platformData['properties']
            );
        }

        $platform = new Platform();
        $platform
            ->setMatch($platformData['match'])
            ->setProperties($platformData['properties'])
            ->setIsLite($platformData['lite'])
        ;

        return $platform;
    }
}
