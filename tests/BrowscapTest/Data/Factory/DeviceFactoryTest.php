<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Assert\InvalidArgumentException;
use Browscap\Data\Device;
use Browscap\Data\Factory\DeviceFactory;
use PHPUnit\Framework\TestCase;

class DeviceFactoryTest extends TestCase
{
    /**
     * @var DeviceFactory
     */
    private $object;

    public function setUp() : void
    {
        $this->object = new DeviceFactory();
    }

    public function testBuildWithoutStandardProperty() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the value for "standard" key is missing for device "Test"');

        $deviceData = ['abc' => 'def'];
        $deviceName = 'Test';

        $this->object->build($deviceData, $deviceName);
    }

    /**
     * tests the creating of an engine factory
     */
    public function testBuildOk() : void
    {
        $deviceData = ['properties' => ['abc' => 'xyz'], 'standard' => true];
        $deviceName = 'Test';

        $device = $this->object->build($deviceData, $deviceName);
        self::assertInstanceOf(Device::class, $device);
        self::assertTrue($device->isStandard());
    }
}
