<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\Browser;
use Browscap\Data\DataCollection;
use Browscap\Data\Device;
use Browscap\Data\Division;
use Browscap\Data\Engine;
use Browscap\Data\Platform;
use DateTimeImmutable;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class DataCollectionTest extends TestCase
{
    /**
     * @var DataCollection
     */
    private $object;

    public function setUp() : void
    {
        $logger = new Logger('browscap');
        $logger->pushHandler(new NullHandler(Logger::DEBUG));
        $this->object = new DataCollection($logger);
    }

    public function testGetPlatformThrowsExceptionIfPlatformDoesNotExist() : void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Platform "NotExists" does not exist in data');

        $this->object->getPlatform('NotExists');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetPlatform() : void
    {
        $expectedPlatform = $this->createMock(Platform::class);

        $this->object->addPlatform('Platform1', $expectedPlatform);

        $platform = $this->object->getPlatform('Platform1');

        self::assertSame($expectedPlatform, $platform);
    }

    public function testGetEngineThrowsExceptionIfEngineDoesNotExist() : void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Rendering Engine "NotExists" does not exist in data');

        $this->object->getEngine('NotExists');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetEngine() : void
    {
        $expectedEngine = $this->createMock(Engine::class);

        $this->object->addEngine('Foobar', $expectedEngine);

        $engine = $this->object->getEngine('Foobar');

        self::assertSame($expectedEngine, $engine);
    }

    public function testGetDeviceThrowsExceptionIfDeviceDoesNotExist() : void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Device "NotExists" does not exist in data');

        $this->object->getDevice('NotExists');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetDevice() : void
    {
        $expectedDevice = $this->createMock(Device::class);

        $this->object->addDevice('Foobar', $expectedDevice);

        $device = $this->object->getDevice('Foobar');

        self::assertSame($expectedDevice, $device);
    }

    public function testGetBrowserThrowsExceptionIfBrowserDoesNotExist() : void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Browser "NotExists" does not exist in data');

        $this->object->getBrowser('NotExists');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetBrowser() : void
    {
        $expectedBrowser = $this->createMock(Browser::class);

        $this->object->addBrowser('Foobar', $expectedBrowser);

        $browser = $this->object->getBrowser('Foobar');

        self::assertSame($expectedBrowser, $browser);
    }

    public function testGetDivisions() : void
    {
        $divisionName     = 'test-division';
        $expectedDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $expectedDivision
            ->expects(self::never())
            ->method('getName')
            ->will(self::returnValue($divisionName));

        $this->object->addDivision($expectedDivision);

        $divisions = $this->object->getDivisions();

        self::assertInternalType('array', $divisions);
        self::assertArrayHasKey(0, $divisions);
        self::assertSame($expectedDivision, $divisions[0]);

        $divisionsSecond = $this->object->getDivisions();

        self::assertInternalType('array', $divisionsSecond);
        self::assertArrayHasKey(0, $divisionsSecond);
        self::assertSame($expectedDivision, $divisionsSecond[0]);
        self::assertSame($divisions, $divisionsSecond);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetDefaultProperties() : void
    {
        $defaultProperties = $this->createMock(Division::class);

        $this->object->setDefaultProperties($defaultProperties);

        self::assertSame($defaultProperties, $this->object->getDefaultProperties());
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetDefaultBrowser() : void
    {
        $defaultBrowser = $this->createMock(Division::class);

        $this->object->setDefaultBrowser($defaultBrowser);

        self::assertSame($defaultBrowser, $this->object->getDefaultBrowser());
    }
}
