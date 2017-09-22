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

use Browscap\Data\Useragent;

/**
 * Class UseragentTest
 *
 * @category   BrowscapTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class UseragentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests setter and getter for the match property
     *
     * @group data
     * @group sourcetest
     */
    public function testGetter() : void
    {
        $userAgent  = 'TestMatchName';
        $properties = ['abc' => 'def'];
        $children   = [];
        $platform   = 'TestPlatform';
        $engine     = 'TestEngine';
        $device     = 'TestDevice';

        $object = new Useragent($userAgent, $properties, $children, $platform, $engine, $device);

        self::assertSame($userAgent, $object->getUserAgent());
        self::assertSame($properties, $object->getProperties());
        self::assertTrue(is_iterable($object->getChildren()));
        self::assertSame($platform, $object->getPlatform());
        self::assertSame($engine, $object->getEngine());
        self::assertSame($device, $object->getDevice());
    }
}
