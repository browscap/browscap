<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\Device;
use PHPUnit\Framework\TestCase;

class DeviceTest extends TestCase
{
    /**
     * tests setter and getter for the match property
     */
    public function testGetter() : void
    {
        $properties = ['abc' => 'def'];
        $standard   = false;
        $type       = 'tablet';

        $object = new Device($properties, $type, $standard);

        self::assertSame($properties, $object->getProperties());
        self::assertFalse($object->isStandard());
        self::assertSame($type, $object->getType());
    }
}
