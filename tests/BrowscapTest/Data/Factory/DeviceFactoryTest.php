<?php

declare(strict_types=1);

namespace BrowscapTest\Data\Factory;

use Assert\AssertionFailedException;
use Assert\InvalidArgumentException;
use Browscap\Data\Device;
use Browscap\Data\Factory\DeviceFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use UnexpectedValueException;

class DeviceFactoryTest extends TestCase
{
    private DeviceFactory $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new DeviceFactory();
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function testBuildWithoutStandardProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the value for "standard" key is missing for device "Test"');

        $deviceData = ['abc' => 'def'];
        $deviceName = 'Test';

        $this->object->build($deviceData, $deviceName);
    }

    /**
     * @throws AssertionFailedException
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function testBuildWithWrongDeviceType(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('unsupported device type given for device "Test"');

        $deviceData = ['properties' => ['abc' => 'xyz'], 'standard' => true, 'type' => 'does not exist'];
        $deviceName = 'Test';

        $this->object->build($deviceData, $deviceName);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function testBuildWithUnsupportedDeviceType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value "phablet" is not an element of the valid values: car-entertainment-system, console, desktop, digital-camera, ebook-reader, feature-phone, fone-pad, mobile-console, mobile-device, mobile-phone, smartphone, tablet, tv, tv-console, unknown');

        $deviceData = ['properties' => ['abc' => 'xyz'], 'standard' => true, 'type' => 'phablet'];
        $deviceName = 'Test';

        $this->object->build($deviceData, $deviceName);
    }

    /**
     * @throws AssertionFailedException
     * @throws RuntimeException
     */
    public function testCreationOfDevice(): void
    {
        $deviceData = ['properties' => ['abc' => 'xyz'], 'standard' => true, 'type' => 'tablet'];
        $deviceName = 'Test';

        $device = $this->object->build($deviceData, $deviceName);
        static::assertInstanceOf(Device::class, $device);
        static::assertTrue($device->isStandard());
    }
}
