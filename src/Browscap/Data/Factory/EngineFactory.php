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

use Browscap\Data\Engine;

/**
 * Class EngineFactory
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
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
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return \Browscap\Data\Engine
     */
    public function build(array $engineData, array $json, string $engineName) : Engine
    {
        if (!isset($engineData['properties'])) {
            $engineData['properties'] = [];
        }

        if (array_key_exists('inherits', $engineData)) {
            $parentName = $engineData['inherits'];

            if (!isset($json['engines'][$parentName])) {
                throw new \UnexpectedValueException(
                    'parent Engine "' . $parentName . '" is missing for engine "' . $engineName . '"'
                );
            }

            $parentEngine     = $this->build($json['engines'][$parentName], $json, $parentName);
            $parentEngineData = $parentEngine->getProperties();

            $engineProperties = $engineData['properties'];

            foreach ($engineProperties as $name => $value) {
                if (isset($parentEngineData[$name])
                    && $parentEngineData[$name] === $value
                ) {
                    throw new \UnexpectedValueException(
                        'the value for property "' . $name . '" has the same value in the keys "' . $engineName
                        . '" and its parent "' . $parentName . '"'
                    );
                }
            }

            $engineData['properties'] = array_merge(
                $parentEngineData,
                $engineProperties
            );
        }

        return new Engine($engineData['properties']);
    }
}
