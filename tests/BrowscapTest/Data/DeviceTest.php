<?php

declare(strict_types=1);

namespace BrowscapTest\Data;

use Browscap\Data\Device;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class DeviceTest extends TestCase
{
    /**
     * tests setter and getter for the match property
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testGetter(): void
    {
        $properties = ['abc' => 'def'];
        $standard   = false;
        $type       = 'tablet';

        $object = new Device($properties, $type, $standard);

        static::assertSame($properties, $object->getProperties());
        static::assertFalse($object->isStandard());
        static::assertSame($type, $object->getType());
    }
}
