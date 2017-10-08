<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\Device;

/**
 * Class DeviceTestTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class DeviceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests setter and getter for the match property
     */
    public function testGetter() : void
    {
        $properties = ['abc' => 'def'];
        $standard   = false;

        $object = new Device($properties, $standard);

        self::assertSame($properties, $object->getProperties());
        self::assertFalse($object->isStandard());
    }
}
