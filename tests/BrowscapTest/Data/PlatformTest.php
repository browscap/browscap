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

use Browscap\Data\Platform;

/**
 * Class PlatformTest
 *
 * @category   BrowscapTest
 *
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
    public function testGetter() : void
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
