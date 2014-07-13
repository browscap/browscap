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

use Browscap\Data\Engine;

/**
 * Class DataCollection
 *
 * @category   Browscap
 * @package    Data
 * @author     James Titcumb <james@asgrim.com>
 */
class EngineFactory
{
    /**
     * Load a engines.json file and parse it into the platforms data array
     *
     * @param array  $engineData
     * @param array  $json
     * @param string $engineName
     *
     * @return \Browscap\Data\Engine
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     */
    public function build(array $engineData, array $json, $engineName)
    {
        if (!isset($engineData['properties'])) {
            $engineData['properties'] = array();
        }

        if (array_key_exists('inherits', $engineData)) {
            $parentName = $engineData['inherits'];

            if (!isset($json['engines'][$parentName])) {
                throw new \UnexpectedValueException(
                    'parent Engine "' . $parentName . '" is missing for engine "' . $engineName . '"'
                );
            }

            $parentEngineData = $json['engines'][$parentName];

            if (!isset($parentEngineData['properties'])) {
                throw new \UnexpectedValueException(
                    'properties missing for parent Engine "' . $parentName . '"'
                );
            }

            $inheritedPlatformProperties = $engineData['properties'];

            foreach ($inheritedPlatformProperties as $name => $value) {
                if (isset($parentEngineData['properties'][$name])
                    && $parentEngineData['properties'][$name] == $value
                ) {
                    throw new \UnexpectedValueException(
                        'the value for property "' . $name .'" has the same value in the keys "' . $engineName
                        . '" and its parent "' . $engineData['inherits'] . '"'
                    );
                }
            }

            $engineData['properties'] = array_merge(
                $parentEngineData['properties'],
                $inheritedPlatformProperties
            );
        }

        $engine = new Engine();
        $engine->setProperties($engineData['properties']);

        return $engine;
    }
}
