<?php

declare(strict_types=1);

namespace BrowscapTest\Data;

use Browscap\Data\Browser;
use Browscap\Data\DataCollection;
use Browscap\Data\Device;
use Browscap\Data\Division;
use Browscap\Data\Expander;
use Browscap\Data\Platform;
use Browscap\Data\UserAgent;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionException;
use ReflectionProperty;

use function assert;

class ExpanderTest extends TestCase
{
    private Expander $object;

    protected function setUp(): void
    {
        $logger     = $this->createMock(LoggerInterface::class);
        $collection = $this->createMock(DataCollection::class);

        assert($logger instanceof LoggerInterface);
        assert($collection instanceof DataCollection);
        $this->object = new Expander($logger, $collection);
    }

    /**
     * tests parsing an empty data collection
     *
     * @throws ReflectionException
     */
    public function testParseDoesNothingOnEmptyDatacollection(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::never())
            ->method('getDivisions')
            ->willReturn([]);

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);

        $defaultProperties
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([]);

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $result = $this->object->expand($division, 'TestDivision');
        static::assertIsArray($result);
        static::assertCount(0, $result);
    }

    /**
     * tests parsing a not empty data collection without children
     *
     * @throws ReflectionException
     */
    public function testParseOnNotEmptyDatacollectionWithoutChildren(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::never())
            ->method('getDivisions')
            ->willReturn([]);

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);

        $defaultProperties
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn(
                [0 => $useragent]
            );

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $result = $this->object->expand($division, 'TestDivision');
        static::assertIsArray($result);
        static::assertCount(1, $result);
    }

    /**
     * tests parsing an not empty data collection with children
     *
     * @throws ReflectionException
     */
    public function testParseOnNotEmptyDatacollectionWithChildren(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::never())
            ->method('getDivisions')
            ->willReturn([]);

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::exactly(3))
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);

        $defaultProperties
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]);

        $useragent
            ->expects(static::once())
            ->method('getChildren')
            ->willReturn([
                0 => [
                    'match' => 'abc*',
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $result = $this->object->expand($division, 'TestDivision');
        static::assertIsArray($result);
        static::assertCount(2, $result);
    }

    /**
     * tests parsing a non empty data collection with children and devices
     *
     * @throws ReflectionException
     */
    public function testParseOnNotEmptyDatacollectionWithChildrenAndDevices(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getDevice'])
            ->getMock();

        $collection
            ->expects(static::never())
            ->method('getDivisions')
            ->willReturn([]);

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::exactly(4))
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);

        $defaultProperties
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $device = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties', 'getType'])
            ->getMock();

        $device
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn([]);

        $device
            ->expects(static::exactly(2))
            ->method('getType')
            ->willReturn('tablet');

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);

        $collection
            ->expects(static::exactly(2))
            ->method('getDevice')
            ->willReturn($device);

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]);

        $useragent
            ->expects(static::once())
            ->method('getChildren')
            ->willReturn([
                0 => [
                    'match' => 'abc*#DEVICE#',
                    'devices' => [
                        'abc' => 'ABC',
                        'def' => 'DEF',
                    ],
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $result = $this->object->expand($division, 'TestDivision');
        static::assertIsArray($result);
        static::assertCount(3, $result);
    }

    /**
     * tests pattern id generation on a not empty data collection with children, no devices or platforms
     *
     * @throws ReflectionException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildren(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::exactly(3))
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);

        $defaultProperties
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]);

        $useragent
            ->expects(static::once())
            ->method('getChildren')
            ->willReturn([
                0 => [
                    'match' => 'abc*',
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $division
            ->expects(static::once())
            ->method('getFileName')
            ->willReturn('tests/test.json');

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $result = $this->object->expand($division, 'TestDivision');

        static::assertArrayHasKey('PatternId', $result['abc*']);
        static::assertSame('tests/test.json::u0::c0::d::p', $result['abc*']['PatternId']);
    }

    /**
     * tests pattern id generation on a not empty data collection with children and platforms, no devices
     *
     * @throws ReflectionException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndPlatforms(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getPlatform'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::exactly(3))
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);

        $defaultProperties
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $platform = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties', 'getMatch'])
            ->getMock();

        $platform
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([]);

        $platform
            ->expects(static::once())
            ->method('getMatch')
            ->willReturn('');

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);

        $collection
            ->expects(static::once())
            ->method('getPlatform')
            ->willReturn($platform);

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]);

        $useragent
            ->expects(static::once())
            ->method('getChildren')
            ->willReturn([
                0 => [
                    'match' => 'abc*#PLATFORM#',
                    'properties' => ['Browser' => 'xyza'],
                    'platforms' => ['Platform_1'],
                ],
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $division
            ->expects(static::once())
            ->method('getFileName')
            ->willReturn('tests/test.json');

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $result = $this->object->expand($division, 'TestDivision');

        static::assertArrayHasKey('PatternId', $result['abc*']);
        static::assertSame('tests/test.json::u0::c0::d::pPlatform_1', $result['abc*']['PatternId']);
    }

    /**
     * tests pattern id generation on a not empty data collection with children and devices, no platforms
     *
     * @throws ReflectionException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndDevices(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::exactly(4))
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);

        $defaultProperties
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $device = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties', 'getType'])
            ->getMock();

        $device
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn([]);

        $device
            ->expects(static::exactly(2))
            ->method('getType')
            ->willReturn('tablet');

        $platform = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties', 'getMatch'])
            ->getMock();

        $platform
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn([]);

        $platform
            ->expects(static::exactly(2))
            ->method('getMatch')
            ->willReturn('');

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);

        $collection
            ->expects(static::exactly(2))
            ->method('getDevice')
            ->willReturn($device);

        $collection
            ->expects(static::exactly(2))
            ->method('getPlatform')
            ->willReturn($platform);

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]);

        $useragent
            ->expects(static::once())
            ->method('getChildren')
            ->willReturn([
                0 => [
                    'match' => 'abc*#DEVICE#',
                    'devices' => [
                        'abc' => 'ABC',
                        'def' => 'DEF',
                    ],
                    'platforms' => ['Platform_1'],
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $division
            ->expects(static::once())
            ->method('getFileName')
            ->willReturn('tests/test.json');

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $result = $this->object->expand($division, 'TestDivision');

        static::assertArrayHasKey('PatternId', $result['abc*abc']);
        static::assertSame('tests/test.json::u0::c0::dabc::pPlatform_1', $result['abc*abc']['PatternId']);

        static::assertArrayHasKey('PatternId', $result['abc*def']);
        static::assertSame('tests/test.json::u0::c0::ddef::pPlatform_1', $result['abc*def']['PatternId']);
    }

    /**
     * tests pattern id generation on a not empty data collection with children, platforms and devices
     *
     * @throws ReflectionException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenPlatformsAndDevices(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getDevice'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::exactly(4))
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);

        $defaultProperties
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $device = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties', 'getType'])
            ->getMock();

        $device
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn([]);

        $device
            ->expects(static::exactly(2))
            ->method('getType')
            ->willReturn('tablet');

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);

        $collection
            ->expects(static::exactly(2))
            ->method('getDevice')
            ->willReturn($device);

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]);

        $useragent
            ->expects(static::once())
            ->method('getChildren')
            ->willReturn([
                0 => [
                    'match' => 'abc*#DEVICE#',
                    'devices' => [
                        'abc' => 'ABC',
                        'def' => 'DEF',
                    ],
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $division
            ->expects(static::once())
            ->method('getFileName')
            ->willReturn('tests/test.json');

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $result = $this->object->expand($division, 'TestDivision');

        static::assertArrayHasKey('PatternId', $result['abc*abc']);
        static::assertSame('tests/test.json::u0::c0::dabc::p', $result['abc*abc']['PatternId']);

        static::assertArrayHasKey('PatternId', $result['abc*def']);
        static::assertSame('tests/test.json::u0::c0::ddef::p', $result['abc*def']['PatternId']);
    }

    /**
     * tests pattern id generation on a not empty data collection with children, platforms and devices
     *
     * @throws ReflectionException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenPlatformsAndBrowsers(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getBrowser'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::exactly(3))
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);

        $defaultProperties
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $browser = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties', 'getType'])
            ->getMock();

        $browser
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([]);

        $browser
            ->expects(static::once())
            ->method('getType')
            ->willReturn('browser');

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);

        $collection
            ->expects(static::once())
            ->method('getBrowser')
            ->willReturn($browser);

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]);

        $useragent
            ->expects(static::once())
            ->method('getChildren')
            ->willReturn([
                0 => [
                    'match' => 'abc*#DEVICE#',
                    'browser' => 'def',
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $division
            ->expects(static::once())
            ->method('getFileName')
            ->willReturn('tests/test.json');

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $this->object->expand($division, 'TestDivision');
    }
}
