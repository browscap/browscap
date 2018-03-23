<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\Browser;
use Browscap\Data\DataCollection;
use Browscap\Data\Device;
use Browscap\Data\Division;
use Browscap\Data\Engine;
use Browscap\Data\Platform;
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

    /**
     * tests getting the generation date
     */
    public function testGetGenerationDate() : void
    {
        // Time isn't always exact, so allow a few seconds grace either way...
        $currentTime = time();
        $minTime     = $currentTime - 3;
        $maxTime     = $currentTime + 3;

        $testDateTime = $this->object->getGenerationDate();

        self::assertInstanceOf(\DateTimeImmutable::class, $testDateTime);

        $testTime = $testDateTime->getTimestamp();
        self::assertGreaterThanOrEqual($minTime, $testTime);
        self::assertLessThanOrEqual($maxTime, $testTime);
    }

    public function testGetPlatformThrowsExceptionIfPlatformDoesNotExist() : void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Platform "NotExists" does not exist in data');

        $this->object->getPlatform('NotExists');
    }

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

    public function testGetBrowser() : void
    {
        $expectedBrowser = $this->createMock(Browser::class);

        $this->object->addBrowser('Foobar', $expectedBrowser);

        $browser = $this->object->getBrowser('Foobar');

        self::assertSame($expectedBrowser, $browser);
    }

    public function testGetDivisions() : void
    {
        $expectedDivision = $this->createMock(Division::class);

        $this->object->addDivision($expectedDivision);

        $divisions = $this->object->getDivisions();

        self::assertInternalType('array', $divisions);
        self::assertArrayHasKey(0, $divisions);
        self::assertSame($expectedDivision, $divisions[0]);

        $divisions = $this->object->getDivisions();

        self::assertInternalType('array', $divisions);
        self::assertArrayHasKey(0, $divisions);
        self::assertSame($expectedDivision, $divisions[0]);
    }

    public function testSetDefaultProperties() : void
    {
        $defaultProperties = $this->createMock(Division::class);

        $this->object->setDefaultProperties($defaultProperties);

        self::assertSame($defaultProperties, $this->object->getDefaultProperties());
    }

    public function testSetDefaultBrowser() : void
    {
        $defaultBrowser = $this->createMock(Division::class);

        $this->object->setDefaultBrowser($defaultBrowser);

        self::assertSame($defaultBrowser, $this->object->getDefaultBrowser());
    }
}
