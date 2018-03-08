<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Assert\InvalidArgumentException;
use Browscap\Data\Device;
use Browscap\Data\Factory\DeviceFactory;
use InvalidArgumentException as BaseInvalidArgumentException;
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

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testBuildWithoutStandardProperty() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the value for "standard" key is missing for device "Test"');

        $deviceData = ['abc' => 'def'];
        $deviceName = 'Test';

        $this->object->build($deviceData, $deviceName);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testBuildWithWrongDeviceType() : void
    {
        $this->expectException(BaseInvalidArgumentException::class);
        $this->expectExceptionMessage('unsupported device type given for device "Test"');

        $deviceData = ['properties' => ['abc' => 'xyz'], 'standard' => true, 'type' => 'does not exist'];
        $deviceName = 'Test';

        $this->object->build($deviceData, $deviceName);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testCreationOfDevice() : void
    {
        $deviceData = ['properties' => ['abc' => 'xyz'], 'standard' => true, 'type' => 'tablet'];
        $deviceName = 'Test';

        $device = $this->object->build($deviceData, $deviceName);
        self::assertInstanceOf(Device::class, $device);
        self::assertTrue($device->isStandard());
    }
}
