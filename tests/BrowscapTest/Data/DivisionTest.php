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
namespace BrowscapTest\Data;

use Browscap\Data\Division;

/**
 * Class DivisionTest
 *
 * @category   BrowscapTest
 *
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
    public function testGetter() : void
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
