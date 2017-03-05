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

use Browscap\Data\Platform;

/**
 * Class PlatformTest
 *
 * @category   BrowscapTest
 * @author     James Titcumb <james@asgrim.com>
 */
class PlatformTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests setter and getter for the match property
     *
     * @group data
     * @group sourcetest
     */
    public function testGetter()
    {
        $match      = 'TestMatchName';
        $properties = ['abc' => 'def'];

        $object = new Platform($match, $properties, true, false);

        self::assertSame($match, $object->getMatch());
        self::assertSame($properties, $object->getProperties());
        $this->assertTrue($object->isLite());
        $this->assertFalse($object->isStandard());
    }
}
