<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\UserAgent;

/**
 * Class UseragentTestTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class UseragentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests setter and getter for the match property
     */
    public function testGetter() : void
    {
        $userAgent  = 'TestMatchName';
        $properties = ['abc' => 'def'];
        $children   = [];
        $platform   = 'TestPlatform';
        $engine     = 'TestEngine';
        $device     = 'TestDevice';

        $object = new UserAgent($userAgent, $properties, $children, $platform, $engine, $device);

        self::assertSame($userAgent, $object->getUserAgent());
        self::assertSame($properties, $object->getProperties());
        self::assertTrue(is_iterable($object->getChildren()));
        self::assertSame($platform, $object->getPlatform());
        self::assertSame($engine, $object->getEngine());
        self::assertSame($device, $object->getDevice());
    }
}
