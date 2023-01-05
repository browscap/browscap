<?php

declare(strict_types=1);

namespace BrowscapTest\Data;

use Browscap\Data\Browser;
use Browscap\Data\DataCollection;
use Browscap\Data\Device;
use Browscap\Data\Division;
use Browscap\Data\DuplicateDataException;
use Browscap\Data\Engine;
use Browscap\Data\Expander;
use Browscap\Data\InvalidParentException;
use Browscap\Data\ParentNotDefinedException;
use Browscap\Data\Platform;
use Browscap\Data\UserAgent;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionException;
use ReflectionProperty;
use UnexpectedValueException;

use function assert;

class ExpanderTest extends TestCase
{
    private Expander $object;

    /** @throws void */
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
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testParseDoesNothingOnEmptyDatacollection(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();
        $collection
            ->expects(static::never())
            ->method('getDivisions')
            ->willReturn([]);

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
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
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::never())
            ->method('getDevice');
        $collection
            ->expects(static::never())
            ->method('getPlatform');
        $collection
            ->expects(static::never())
            ->method('getEngine');
        $collection
            ->expects(static::never())
            ->method('getBrowser');

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([]);

        $property = new ReflectionProperty($this->object, 'collection');
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
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testParseOnNotEmptyDatacollectionWithoutChildren(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();
        $collection
            ->expects(static::never())
            ->method('getDivisions')
            ->willReturn([]);

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
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
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::never())
            ->method('getDevice');
        $collection
            ->expects(static::never())
            ->method('getPlatform');
        $collection
            ->expects(static::never())
            ->method('getEngine');
        $collection
            ->expects(static::never())
            ->method('getBrowser');

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
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
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn(
                [0 => $useragent],
            );

        $property = new ReflectionProperty($this->object, 'collection');
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
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testParseOnNotEmptyDatacollectionWithChildren(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();
        $collection
            ->expects(static::never())
            ->method('getDivisions')
            ->willReturn([]);

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
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
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::never())
            ->method('getDevice');
        $collection
            ->expects(static::never())
            ->method('getPlatform');
        $collection
            ->expects(static::never())
            ->method('getEngine');
        $collection
            ->expects(static::never())
            ->method('getBrowser');

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties', 'getChildren', 'getPlatform'])
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
        $useragent
            ->expects(static::once())
            ->method('getPlatform')
            ->willReturn(null);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $property = new ReflectionProperty($this->object, 'collection');
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
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testParseOnNotEmptyDatacollectionWithChildrenAndDevices(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();
        $collection
            ->expects(static::never())
            ->method('getDivisions')
            ->willReturn([]);

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
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
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $device1 = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $device1
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);
        $device1
            ->expects(static::once())
            ->method('getType')
            ->willReturn('tablet');

        $device2 = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $device2
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn(['avd' => 'abc']);
        $device2
            ->expects(static::once())
            ->method('getType')
            ->willReturn('mobile');

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::exactly(2))
            ->method('getDevice')
            ->willReturnMap([['ABC', $device1], ['DEF', $device2]]);
        $collection
            ->expects(static::never())
            ->method('getPlatform');
        $collection
            ->expects(static::never())
            ->method('getEngine');
        $collection
            ->expects(static::never())
            ->method('getBrowser');

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties', 'getChildren', 'getPlatform'])
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
        $useragent
            ->expects(static::once())
            ->method('getPlatform')
            ->willReturn(null);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $property = new ReflectionProperty($this->object, 'collection');
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
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildren(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
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
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::never())
            ->method('getDevice');
        $collection
            ->expects(static::never())
            ->method('getPlatform');
        $collection
            ->expects(static::never())
            ->method('getEngine');
        $collection
            ->expects(static::never())
            ->method('getBrowser');

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties', 'getChildren', 'getPlatform'])
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
        $useragent
            ->expects(static::once())
            ->method('getPlatform')
            ->willReturn(null);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents', 'getFileName'])
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
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndPlatforms(): void
    {
        $collection        = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();
        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
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
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $platform1 = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getMatch'])
            ->getMock();
        $platform1
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn(['avd' => 'cde']);
        $platform1
            ->expects(static::once())
            ->method('getMatch')
            ->willReturn('');

        $platform2 = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getMatch', 'isLite', 'isStandard'])
            ->getMock();
        $platform2
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn(['Platform' => 'abc']);
        $platform2
            ->expects(static::never())
            ->method('getMatch');
        $platform2
            ->expects(static::once())
            ->method('isLite')
            ->willReturn(false);
        $platform2
            ->expects(static::once())
            ->method('isStandard')
            ->willReturn(false);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::never())
            ->method('getDevice');
        $collection
            ->expects(static::exactly(2))
            ->method('getPlatform')
            ->willReturnMap(
                [['Platform_2', $platform2], ['Platform_1', $platform1]],
            );
        $collection
            ->expects(static::never())
            ->method('getEngine');
        $collection
            ->expects(static::never())
            ->method('getBrowser');

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties', 'getChildren', 'getPlatform'])
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
        $useragent
            ->expects(static::exactly(3))
            ->method('getPlatform')
            ->willReturn('Platform_2');

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents', 'getFileName', 'isLite', 'isStandard', 'getSortIndex'])
            ->getMock();
        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);
        $division
            ->expects(static::once())
            ->method('getFileName')
            ->willReturn('tests/test.json');
        $division
            ->expects(static::once())
            ->method('isLite')
            ->willReturn(true);
        $division
            ->expects(static::once())
            ->method('isStandard')
            ->willReturn(true);
        $division
            ->expects(static::once())
            ->method('getSortIndex')
            ->willReturn(42);

        $property = new ReflectionProperty($this->object, 'collection');
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
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndDevices(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
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
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $device1 = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $device1
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([]);
        $device1
            ->expects(static::once())
            ->method('getType')
            ->willReturn('tablet');

        $device2 = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $device2
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn(['avd' => 'fgh']);
        $device2
            ->expects(static::once())
            ->method('getType')
            ->willReturn('mobile');

        $platform = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getMatch'])
            ->getMock();
        $platform
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn(['avd' => 'mno']);
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
            ->willReturnMap([['ABC', $device1], ['DEF', $device2]]);
        $collection
            ->expects(static::exactly(2))
            ->method('getPlatform')
            ->with('Platform_1')
            ->willReturn($platform);
        $collection
            ->expects(static::never())
            ->method('getEngine');
        $collection
            ->expects(static::never())
            ->method('getBrowser');

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties', 'getChildren', 'getPlatform'])
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
        $useragent
            ->expects(static::once())
            ->method('getPlatform')
            ->willReturn(null);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents', 'getFileName'])
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
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $result = $this->object->expand($division, 'TestDivision');

        static::assertArrayHasKey('PatternId', $result['abc*abc']);
        static::assertSame('tests/test.json::u0::c0::dabc::pPlatform_1', $result['abc*abc']['PatternId']);

        static::assertArrayHasKey('PatternId', $result['abc*def']);
        static::assertSame('tests/test.json::u0::c0::ddef::pPlatform_1', $result['abc*def']['PatternId']);
    }

    /**
     * tests pattern id generation on a not empty data collection with children and devices, no platforms
     *
     * @throws ReflectionException
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndDevices2(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
            ->getMock();
        $defaultProperties
            ->expects(static::exactly(6))
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);
        $defaultProperties
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $device1 = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $device1
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn(['Device_Name' => 'D1']);
        $device1
            ->expects(static::exactly(2))
            ->method('getType')
            ->willReturn('tablet');

        $device2 = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $device2
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn(['Device_Name' => 'D2']);
        $device2
            ->expects(static::exactly(2))
            ->method('getType')
            ->willReturn('console');

        $platform1 = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getMatch'])
            ->getMock();
        $platform1
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn(['Platform' => 'P1']);
        $platform1
            ->expects(static::exactly(2))
            ->method('getMatch')
            ->willReturn('*P1*');

        $platform2 = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getMatch'])
            ->getMock();
        $platform2
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn(['Platform' => 'P2']);
        $platform2
            ->expects(static::exactly(2))
            ->method('getMatch')
            ->willReturn('*P2*');

        $engine = $this->getMockBuilder(Engine::class)
            ->disableOriginalConstructor()
            ->getMock();
        $engine
            ->expects(static::exactly(4))
            ->method('getProperties')
            ->willReturn(['avd' => 'rtz']);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::exactly(4))
            ->method('getDevice')
            ->willReturnMap([['ABC', $device1], ['DEF', $device2]]);
        $collection
            ->expects(static::exactly(4))
            ->method('getPlatform')
            ->willReturnMap([['Platform_1', $platform1], ['Platform_2', $platform2]]);
        $collection
            ->expects(static::exactly(4))
            ->method('getEngine')
            ->with('Engine_1')
            ->willReturn($engine);
        $collection
            ->expects(static::never())
            ->method('getBrowser');

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties', 'getChildren', 'getPlatform'])
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
                    'match' => 'abc*#DEVICE##PLATFORM#',
                    'devices' => [
                        'abc' => 'ABC',
                        'def' => 'DEF',
                    ],
                    'engine' => 'Engine_1',
                    'platforms' => ['Platform_1', 'Platform_2'],
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]);
        $useragent
            ->expects(static::once())
            ->method('getPlatform')
            ->willReturn(null);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents', 'getFileName', 'getName'])
            ->getMock();
        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);
        $division
            ->expects(static::once())
            ->method('getFileName')
            ->willReturn('tests/test.json');
        $division
            ->expects(static::never())
            ->method('getName');

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $result = $this->object->expand($division, 'TestDivision');

        static::assertIsArray($result);
        static::assertCount(5, $result);
        static::assertArrayHasKey('PatternId', $result['abc*abc*P1*']);
        static::assertSame('tests/test.json::u0::c0::dabc::pPlatform_1', $result['abc*abc*P1*']['PatternId']);

        static::assertArrayHasKey('PatternId', $result['abc*abc*P2*']);
        static::assertSame('tests/test.json::u0::c0::dabc::pPlatform_2', $result['abc*abc*P2*']['PatternId']);

        static::assertArrayHasKey('PatternId', $result['abc*def*P1*']);
        static::assertSame('tests/test.json::u0::c0::ddef::pPlatform_1', $result['abc*def*P1*']['PatternId']);

        static::assertArrayHasKey('PatternId', $result['abc*def*P2*']);
        static::assertSame('tests/test.json::u0::c0::ddef::pPlatform_2', $result['abc*def*P2*']['PatternId']);
    }

    /**
     * tests pattern id generation on a not empty data collection with children and devices, no platforms
     *
     * @throws ReflectionException
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndDevices3(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
            ->getMock();
        $defaultProperties
            ->expects(static::exactly(6))
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);
        $defaultProperties
            ->expects(static::once())
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $device1 = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $device1
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn(['Device_Name' => 'D1']);
        $device1
            ->expects(static::exactly(2))
            ->method('getType')
            ->willReturn('tv');

        $device2 = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $device2
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn(['Device_Name' => 'D2']);
        $device2
            ->expects(static::exactly(2))
            ->method('getType')
            ->willReturn('car-entertainment-system');

        $platform1 = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getMatch'])
            ->getMock();
        $platform1
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn(['Platform' => 'P1']);
        $platform1
            ->expects(static::exactly(2))
            ->method('getMatch')
            ->willReturn('*P1*');

        $platform2 = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getMatch'])
            ->getMock();
        $platform2
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn(['Platform' => 'P2']);
        $platform2
            ->expects(static::exactly(2))
            ->method('getMatch')
            ->willReturn('*P2*');

        $engine = $this->getMockBuilder(Engine::class)
            ->disableOriginalConstructor()
            ->getMock();
        $engine
            ->expects(static::exactly(4))
            ->method('getProperties')
            ->willReturn([]);

        $browser = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $browser
            ->expects(static::exactly(4))
            ->method('getProperties')
            ->willReturn(['avd' => 'xyz']);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::exactly(4))
            ->method('getDevice')
            ->willReturnMap([['ABC', $device1], ['DEF', $device2]]);
        $collection
            ->expects(static::exactly(4))
            ->method('getPlatform')
            ->willReturnMap([['Platform_1', $platform1], ['Platform_2', $platform2]]);
        $collection
            ->expects(static::exactly(4))
            ->method('getEngine')
            ->with('Engine_1')
            ->willReturn($engine);
        $collection
            ->expects(static::exactly(4))
            ->method('getBrowser')
            ->with('Browser_1')
            ->willReturn($browser);

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties', 'getChildren', 'getPlatform'])
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
                    'match' => 'abc*#DEVICE##PLATFORM#',
                    'devices' => [
                        'abc' => 'ABC',
                        'def' => 'DEF',
                    ],
                    'engine' => 'Engine_1',
                    'browser' => 'Browser_1',
                    'platforms' => ['Platform_1', 'Platform_2'],
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]);
        $useragent
            ->expects(static::once())
            ->method('getPlatform')
            ->willReturn(null);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents', 'getFileName', 'getName'])
            ->getMock();
        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);
        $division
            ->expects(static::once())
            ->method('getFileName')
            ->willReturn('tests/test.json');
        $division
            ->expects(static::never())
            ->method('getName');

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $result = $this->object->expand($division, 'TestDivision');

        static::assertIsArray($result);
        static::assertCount(5, $result);
        static::assertArrayHasKey('PatternId', $result['abc*abc*P1*']);
        static::assertSame('tests/test.json::u0::c0::dabc::pPlatform_1', $result['abc*abc*P1*']['PatternId']);

        static::assertArrayHasKey('PatternId', $result['abc*abc*P2*']);
        static::assertSame('tests/test.json::u0::c0::dabc::pPlatform_2', $result['abc*abc*P2*']['PatternId']);

        static::assertArrayHasKey('PatternId', $result['abc*def*P1*']);
        static::assertSame('tests/test.json::u0::c0::ddef::pPlatform_1', $result['abc*def*P1*']['PatternId']);

        static::assertArrayHasKey('PatternId', $result['abc*def*P2*']);
        static::assertSame('tests/test.json::u0::c0::ddef::pPlatform_2', $result['abc*def*P2*']['PatternId']);
    }

    /**
     * tests pattern id generation on a not empty data collection with children, platforms and devices
     *
     * @throws ReflectionException
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenPlatformsAndDevices(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
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
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $device1 = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $device1
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn(['avd' => 'abc']);
        $device1
            ->expects(static::once())
            ->method('getType')
            ->willReturn('tablet');

        $device2 = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $device2
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn(['avd' => 'def']);
        $device2
            ->expects(static::once())
            ->method('getType')
            ->willReturn('mobile');

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::exactly(2))
            ->method('getDevice')
            ->willReturnMap([['ABC', $device1], ['DEF', $device2]]);
        $collection
            ->expects(static::never())
            ->method('getPlatform');
        $collection
            ->expects(static::never())
            ->method('getEngine');
        $collection
            ->expects(static::never())
            ->method('getBrowser');

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties', 'getChildren', 'getPlatform'])
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
        $useragent
            ->expects(static::once())
            ->method('getPlatform')
            ->willReturn(null);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents', 'getFileName'])
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
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenPlatformsAndBrowsers(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
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
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $browser = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $browser
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn(['avd' => 'fgh']);
        $browser
            ->expects(static::once())
            ->method('getType')
            ->willReturn('browser');

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::never())
            ->method('getDevice');
        $collection
            ->expects(static::never())
            ->method('getPlatform');
        $collection
            ->expects(static::never())
            ->method('getEngine');
        $collection
            ->expects(static::once())
            ->method('getBrowser')
            ->with('def')
            ->willReturn($browser);

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties', 'getChildren', 'getPlatform'])
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
        $useragent
            ->expects(static::once())
            ->method('getPlatform')
            ->willReturn(null);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents', 'getFileName'])
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
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $this->object->expand($division, 'TestDivision');
    }

    /**
     * tests pattern id generation on a not empty data collection with children, platforms and devices
     *
     * @throws ReflectionException
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws ParentNotDefinedException
     * @throws InvalidParentException
     * @throws DuplicateDataException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenPlatformsAndBrowsers2(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform', 'getEngine', 'getBrowser'])
            ->getMock();

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties'])
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
            ->onlyMethods(['getUserAgents'])
            ->getMock();
        $coreDivision
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $defaultProperties]);

        $browser = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties', 'getType'])
            ->getMock();
        $browser
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn(['avd' => 'hjk']);
        $browser
            ->expects(static::once())
            ->method('getType')
            ->willReturn('browser');

        $engine = $this->getMockBuilder(Engine::class)
            ->disableOriginalConstructor()
            ->getMock();
        $engine
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn(['RenderingEngine_Name' => 'unknown engine']);

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::never())
            ->method('getDevice');
        $collection
            ->expects(static::never())
            ->method('getPlatform');
        $collection
            ->expects(static::once())
            ->method('getEngine')
            ->with('Engine_1')
            ->willReturn($engine);
        $collection
            ->expects(static::once())
            ->method('getBrowser')
            ->with('def')
            ->willReturn($browser);

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgent', 'getProperties', 'getChildren', 'getPlatform'])
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
                    'engine' => 'Engine_1',
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]);
        $useragent
            ->expects(static::once())
            ->method('getPlatform')
            ->willReturn(null);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents', 'getFileName'])
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
        $property->setValue($this->object, $collection);

        assert($division instanceof Division);
        $this->object->expand($division, 'TestDivision');
    }
}
