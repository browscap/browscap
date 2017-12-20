<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   Browscap
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Data\Factory;

use Browscap\Data\Browser;

/**
 * Class BrowserFactory
 *
 * @category   Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class BrowserFactory
{
    /**
     * Load a engines.json file and parse it into the platforms data array
     *
     * @param array  $browserData
     * @param string $browserName
     *
     * @throws \RuntimeException     if the file does not exist or has invalid JSON
     * @return \Browscap\Data\Browser
     */
    public function build(array $browserData, $browserName)
    {
        if (!isset($browserData['properties'])) {
            $browserData['properties'] = [];
        }

        if (!array_key_exists('standard', $browserData)) {
            throw new \UnexpectedValueException(
                'the value for "standard" key is missing for device "' . $browserName . '"'
            );
        }

        if (!array_key_exists('lite', $browserData)) {
            throw new \UnexpectedValueException(
                'the value for "lite" key is missing for device "' . $browserName . '"'
            );
        }

        return new Browser($browserData['properties'], $browserData['lite'], $browserData['standard']);
    }
}
