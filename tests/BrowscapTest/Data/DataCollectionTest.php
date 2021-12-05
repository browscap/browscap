<?php

declare(strict_types=1);

namespace BrowscapTest\Data;

use Browscap\Data\Browser;
use Browscap\Data\DataCollection;
use Browscap\Data\Device;
use Browscap\Data\Division;
use Browscap\Data\Engine;
use Browscap\Data\Platform;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use ReflectionException;

use function assert;

class DataCollectionTest extends TestCase
{
    /** @var DataCollection */
    private $object;

    protected function setUp(): void
    {
        $this->object = new DataCollection();
    }

    public function testGetPlatformThrowsExceptionIfPlatformDoesNotExist(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Platform "NotExists" does not exist in data');

        $this->object->getPlatform('NotExists');
    }

    /**
     * @throws ReflectionException
     */
    public function testGetPlatform(): void
    {
        $expectedPlatform = $this->createMock(Platform::class);

        assert($expectedPlatform instanceof Platform);
        $this->object->addPlatform('Platform1', $expectedPlatform);

        $platform = $this->object->getPlatform('Platform1');

        static::assertSame($expectedPlatform, $platform);
    }

    public function testGetEngineThrowsExceptionIfEngineDoesNotExist(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Rendering Engine "NotExists" does not exist in data');

        $this->object->getEngine('NotExists');
    }

    /**
     * @throws ReflectionException
     */
    public function testGetEngine(): void
    {
        $expectedEngine = $this->createMock(Engine::class);

        assert($expectedEngine instanceof Engine);
        $this->object->addEngine('Foobar', $expectedEngine);

        $engine = $this->object->getEngine('Foobar');

        static::assertSame($expectedEngine, $engine);
    }

    public function testGetDeviceThrowsExceptionIfDeviceDoesNotExist(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Device "NotExists" does not exist in data');

        $this->object->getDevice('NotExists');
    }

    /**
     * @throws ReflectionException
     */
    public function testGetDevice(): void
    {
        $expectedDevice = $this->createMock(Device::class);

        assert($expectedDevice instanceof Device);
        $this->object->addDevice('Foobar', $expectedDevice);

        $device = $this->object->getDevice('Foobar');

        static::assertSame($expectedDevice, $device);
    }

    public function testGetBrowserThrowsExceptionIfBrowserDoesNotExist(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Browser "NotExists" does not exist in data');

        $this->object->getBrowser('NotExists');
    }

    /**
     * @throws ReflectionException
     */
    public function testGetBrowser(): void
    {
        $expectedBrowser = $this->createMock(Browser::class);

        assert($expectedBrowser instanceof Browser);
        $this->object->addBrowser('Foobar', $expectedBrowser);

        $browser = $this->object->getBrowser('Foobar');

        static::assertSame($expectedBrowser, $browser);
    }

    public function testGetDivisions(): void
    {
        $divisionName     = 'test-division';
        $expectedDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $expectedDivision
            ->expects(static::never())
            ->method('getName')
            ->willReturn($divisionName);

        assert($expectedDivision instanceof Division);
        $this->object->addDivision($expectedDivision);

        $divisions = $this->object->getDivisions();

        static::assertIsArray($divisions);
        static::assertArrayHasKey(0, $divisions);
        static::assertSame($expectedDivision, $divisions[0]);

        $divisionsSecond = $this->object->getDivisions();

        static::assertIsArray($divisionsSecond);
        static::assertArrayHasKey(0, $divisionsSecond);
        static::assertSame($expectedDivision, $divisionsSecond[0]);
        static::assertSame($divisions, $divisionsSecond);
    }

    /**
     * @throws ReflectionException
     */
    public function testSetDefaultProperties(): void
    {
        $defaultProperties = $this->createMock(Division::class);

        assert($defaultProperties instanceof Division);
        $this->object->setDefaultProperties($defaultProperties);

        static::assertSame($defaultProperties, $this->object->getDefaultProperties());
    }

    /**
     * @throws ReflectionException
     */
    public function testSetDefaultBrowser(): void
    {
        $defaultBrowser = $this->createMock(Division::class);

        assert($defaultBrowser instanceof Division);
        $this->object->setDefaultBrowser($defaultBrowser);

        static::assertSame($defaultBrowser, $this->object->getDefaultBrowser());
    }
}
