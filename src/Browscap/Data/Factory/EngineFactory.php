<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   Browscap
 * @copyright  1998-2017 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Data\Factory;

use Browscap\Data\Engine;

/**
 * Class EngineFactory
 *
 * @category   Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
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
     * @throws \RuntimeException     if the file does not exist or has invalid JSON
     * @return \Browscap\Data\Engine
     */
    public function build(array $engineData, array $json, $engineName)
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

            $inhEngineProperties = $engineData['properties'];

            foreach ($inhEngineProperties as $name => $value) {
                if (isset($parentEngineData[$name])
                    && $parentEngineData[$name] === $value
                ) {
                    throw new \UnexpectedValueException(
                        'the value for property "' . $name . '" has the same value in the keys "' . $engineName
                        . '" and its parent "' . $engineData['inherits'] . '"'
                    );
                }
            }

            $engineData['properties'] = array_merge(
                $parentEngineData,
                $inhEngineProperties
            );
        }

        return new Engine($engineData['properties']);
    }
}
