<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Assert\InvalidArgumentException;
use Browscap\Data\Device;
use Browscap\Data\Factory\DeviceFactory;

/**
 * Class DeviceFactoryTestTest
 */
class DeviceFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Factory\DeviceFactory
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new DeviceFactory();
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildWithoutStandardProperty() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the value for "standard" key is missing for device "Test"');

        $deviceData = ['abc' => 'def'];
        $json       = [];
        $deviceName = 'Test';

        $this->object->build($deviceData, $deviceName);
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
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
