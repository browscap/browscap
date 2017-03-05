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

use Browscap\Data\Division;

/**
 * Class DivisionTest
 *
 * @category   BrowscapTest
 * @author     James Titcumb <james@asgrim.com>
 */
class DivisionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests setter and getter
     *
     * @group data
     * @group sourcetest
     */
    public function testGetter()
    {
        $name       = 'TestName';
        $sortIndex  = 42;
        $userAgents = ['abc' => 'def'];
        $versions   = [1, 2, 3];

        $object = new Division($name, $sortIndex, $userAgents, true, false, $versions);

        self::assertSame($name, $object->getName());
        self::assertSame($sortIndex, $object->getSortIndex());
        self::assertSame($userAgents, $object->getUserAgents());
        self::assertTrue($object->isLite());
        self::assertFalse($object->isStandard());
        self::assertSame($versions, $object->getVersions());
    }
}
