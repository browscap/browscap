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
namespace Browscap\Data\Helper;

/**
 * Class DataCollection
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class CheckDeviceData
{
    /**
     * checks if device properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function check(array $properties, string $message) : void
    {
        if (array_key_exists('Device_Name', $properties)
            || array_key_exists('Device_Maker', $properties)
            || array_key_exists('Device_Type', $properties)
            || array_key_exists('Device_Pointing_Method', $properties)
            || array_key_exists('Device_Code_Name', $properties)
            || array_key_exists('Device_Brand_Name', $properties)
            || array_key_exists('isMobileDevice', $properties)
            || array_key_exists('isTablet', $properties)
        ) {
            throw new \LogicException($message);
        }
    }
}
