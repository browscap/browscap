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
class CheckEngineData
{
    /**
     * checks if platform properties are set inside a properties array
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
        if (array_key_exists('RenderingEngine_Name', $properties)
            || array_key_exists('RenderingEngine_Version', $properties)
            || array_key_exists('RenderingEngine_Description', $properties)
            || array_key_exists('RenderingEngine_Maker', $properties)
            || array_key_exists('VBScript', $properties)
            || array_key_exists('ActiveXControls', $properties)
            || array_key_exists('BackgroundSounds', $properties)
        ) {
            throw new \LogicException($message);
        }
    }
}
