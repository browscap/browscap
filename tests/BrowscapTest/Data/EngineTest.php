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
 * @category   BrowscapTest
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Data;

use Browscap\Data\Engine;

/**
 * Class EngineTest
 *
 * @category   BrowscapTest
 * @author     James Titcumb <james@asgrim.com>
 */
class EngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests setter and getter for the engine properties
     *
     * @group data
     * @group sourcetest
     */
    public function testGetProperties()
    {
        $properties = ['abc' => 'def'];

        $object = new Engine($properties);

        self::assertSame($properties, $object->getProperties());
    }
}
