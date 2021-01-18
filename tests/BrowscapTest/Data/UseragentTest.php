<?php

declare(strict_types=1);

namespace BrowscapTest\Data;

use Browscap\Data\UserAgent;
use PHPUnit\Framework\TestCase;

use function is_iterable;

class UseragentTest extends TestCase
{
    /**
     * tests setter and getter for the match property
     */
    public function testGetter(): void
    {
        $userAgent  = 'TestMatchName';
        $properties = ['abc' => 'def'];
        $children   = [];
        $platform   = 'TestPlatform';
        $engine     = 'TestEngine';
        $device     = 'TestDevice';

        $object = new UserAgent($userAgent, $properties, $children, $platform, $engine, $device);

        static::assertSame($userAgent, $object->getUserAgent());
        static::assertSame($properties, $object->getProperties());
        static::assertTrue(is_iterable($object->getChildren()));
        static::assertSame($platform, $object->getPlatform());
        static::assertSame($engine, $object->getEngine());
        static::assertSame($device, $object->getDevice());
    }
}
