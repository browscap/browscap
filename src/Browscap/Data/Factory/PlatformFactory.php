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
