<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\DataCollection;
use Browscap\Data\Device;
use Browscap\Data\Division;
use Browscap\Data\Expander;
use Browscap\Data\Platform;
use Browscap\Data\UserAgent;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class ExpanderTest extends TestCase
{
    /**
     * @var Expander
     */
    private $object;

    /**
     * @throws \ReflectionException
     */
    public function setUp() : void
    {
        $logger     = $this->createMock(Logger::class);
        $collection = $this->createMock(DataCollection::class);

        $this->object = new Expander($logger, $collection);
    }

    /**
     * tests parsing an empty data collection
     *
     * @throws \ReflectionException
     */
    public function testParseDoesNothingOnEmptyDatacollection() : void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::never())
            ->method('getDivisions')
            ->will(self::returnValue([]));

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $defaultProperties
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('Defaultproperties'));

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $defaultProperties]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($coreDivision));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([]));

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $result = $this->object->expand($division, 'TestDivision');
        self::assertInternalType('array', $result);
        self::assertCount(0, $result);
    }

    /**
     * tests parsing a not empty data collection without children
     *
     * @throws \ReflectionException
     */
    public function testParseOnNotEmptyDatacollectionWithoutChildren() : void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::never())
            ->method('getDivisions')
            ->will(self::returnValue([]));

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(self::exactly(2))
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $defaultProperties
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('Defaultproperties'));

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $defaultProperties]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($coreDivision));

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => $useragent,
                    ]
                )
            );

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $result = $this->object->expand($division, 'TestDivision');
        self::assertInternalType('array', $result);
        self::assertCount(1, $result);
    }

    /**
     * tests parsing an not empty data collection with children
     *
     * @throws \ReflectionException
     */
    public function testParseOnNotEmptyDatacollectionWithChildren() : void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::never())
            ->method('getDivisions')
            ->will(self::returnValue([]));

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(self::exactly(3))
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $defaultProperties
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('Defaultproperties'));

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $defaultProperties]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($coreDivision));

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]));

        $useragent
            ->expects(self::once())
            ->method('getChildren')
            ->will(self::returnValue([
                0 => [
                    'match' => 'abc*',
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([
                0 => $useragent,
            ]));

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $result = $this->object->expand($division, 'TestDivision');
        self::assertInternalType('array', $result);
        self::assertCount(2, $result);
    }

    /**
     * tests parsing a non empty data collection with children and devices
     *
     * @throws \ReflectionException
     */
    public function testParseOnNotEmptyDatacollectionWithChildrenAndDevices() : void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getDevice'])
            ->getMock();

        $collection
            ->expects(self::never())
            ->method('getDivisions')
            ->will(self::returnValue([]));

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(self::exactly(4))
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $defaultProperties
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('Defaultproperties'));

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $defaultProperties]));

        $device = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties', 'getType'])
            ->getMock();

        $device
            ->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue([]));

        $device
            ->expects(self::any())
            ->method('getType')
            ->will(self::returnValue('tablet'));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($coreDivision));

        $collection
            ->expects(self::exactly(2))
            ->method('getDevice')
            ->will(self::returnValue($device));

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]));

        $useragent
            ->expects(self::once())
            ->method('getChildren')
            ->will(self::returnValue([
                0 => [
                    'match' => 'abc*#DEVICE#',
                    'devices' => [
                        'abc' => 'ABC',
                        'def' => 'DEF',
                    ],
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([
                0 => $useragent,
            ]));

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $result = $this->object->expand($division, 'TestDivision');
        self::assertInternalType('array', $result);
        self::assertCount(3, $result);
    }

    /**
     * tests pattern id generation on a not empty data collection with children, no devices or platforms
     *
     * @throws \ReflectionException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildren() : void
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
            ->expects(self::exactly(3))
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $defaultProperties
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('Defaultproperties'));

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $defaultProperties]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($coreDivision));

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]));

        $useragent
            ->expects(self::once())
            ->method('getChildren')
            ->will(self::returnValue([
                0 => [
                    'match' => 'abc*',
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([
                0 => $useragent,
            ]));

        $division
            ->expects(self::once())
            ->method('getFileName')
            ->will(self::returnValue('tests/test.json'));

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $result = $this->object->expand($division, 'TestDivision');

        self::assertArrayHasKey('PatternId', $result['abc*']);
        self::assertSame('tests/test.json::u0::c0::d::p', $result['abc*']['PatternId']);
    }

    /**
     * tests pattern id generation on a not empty data collection with children and platforms, no devices
     *
     * @throws \ReflectionException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndPlatforms() : void
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
            ->expects(self::exactly(3))
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $defaultProperties
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('Defaultproperties'));

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $defaultProperties]));

        $platform = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties', 'getMatch'])
            ->getMock();

        $platform
            ->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue([]));

        $platform
            ->expects(self::any())
            ->method('getMatch')
            ->will(self::returnValue(''));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($coreDivision));

        $collection
            ->expects(self::any())
            ->method('getPlatform')
            ->will(self::returnValue($platform));

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]));

        $useragent
            ->expects(self::once())
            ->method('getChildren')
            ->will(self::returnValue([
                0 => [
                    'match' => 'abc*#PLATFORM#',
                    'properties' => ['Browser' => 'xyza'],
                    'platforms' => [
                        'Platform_1',
                    ],
                ],
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([
                0 => $useragent,
            ]));

        $division
            ->expects(self::once())
            ->method('getFileName')
            ->will(self::returnValue('tests/test.json'));

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $result = $this->object->expand($division, 'TestDivision');

        self::assertArrayHasKey('PatternId', $result['abc*']);
        self::assertSame('tests/test.json::u0::c0::d::pPlatform_1', $result['abc*']['PatternId']);
    }

    /**
     * tests pattern id generation on a not empty data collection with children and devices, no platforms
     *
     * @throws \ReflectionException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndDevices() : void
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
            ->expects(self::exactly(4))
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $defaultProperties
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('Defaultproperties'));

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $defaultProperties]));

        $device = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties', 'getType'])
            ->getMock();

        $device
            ->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue([]));

        $device
            ->expects(self::any())
            ->method('getType')
            ->will(self::returnValue('tablet'));

        $platform = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties', 'getMatch'])
            ->getMock();

        $platform
            ->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue([]));

        $platform
            ->expects(self::any())
            ->method('getMatch')
            ->will(self::returnValue(''));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($coreDivision));

        $collection
            ->expects(self::exactly(2))
            ->method('getDevice')
            ->will(self::returnValue($device));

        $collection
            ->expects(self::any())
            ->method('getPlatform')
            ->will(self::returnValue($platform));

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]));

        $useragent
            ->expects(self::once())
            ->method('getChildren')
            ->will(self::returnValue([
                0 => [
                    'match' => 'abc*#DEVICE#',
                    'devices' => [
                        'abc' => 'ABC',
                        'def' => 'DEF',
                    ],
                    'platforms' => ['Platform_1'],
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([
                0 => $useragent,
            ]));

        $division
            ->expects(self::once())
            ->method('getFileName')
            ->will(self::returnValue('tests/test.json'));

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $result = $this->object->expand($division, 'TestDivision');

        self::assertArrayHasKey('PatternId', $result['abc*abc']);
        self::assertSame('tests/test.json::u0::c0::dabc::pPlatform_1', $result['abc*abc']['PatternId']);

        self::assertArrayHasKey('PatternId', $result['abc*def']);
        self::assertSame('tests/test.json::u0::c0::ddef::pPlatform_1', $result['abc*def']['PatternId']);
    }

    /**
     * tests pattern id generation on a not empty data collection with children, platforms and devices
     *
     * @throws \ReflectionException
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenPlatformsAndDevices() : void
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
            ->expects(self::exactly(4))
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $defaultProperties
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('Defaultproperties'));

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $defaultProperties]));

        $device = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties', 'getType'])
            ->getMock();

        $device
            ->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue([]));

        $device
            ->expects(self::any())
            ->method('getType')
            ->will(self::returnValue('tablet'));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($coreDivision));

        $collection
            ->expects(self::exactly(2))
            ->method('getDevice')
            ->will(self::returnValue($device));

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties', 'getChildren'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
            ]));

        $useragent
            ->expects(self::once())
            ->method('getChildren')
            ->will(self::returnValue([
                0 => [
                    'match' => 'abc*#DEVICE#',
                    'devices' => [
                        'abc' => 'ABC',
                        'def' => 'DEF',
                    ],
                    'properties' => ['Browser' => 'xyza'],
                ],
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([
                0 => $useragent,
            ]));

        $division
            ->expects(self::once())
            ->method('getFileName')
            ->will(self::returnValue('tests/test.json'));

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $result = $this->object->expand($division, 'TestDivision');

        self::assertArrayHasKey('PatternId', $result['abc*abc']);
        self::assertSame('tests/test.json::u0::c0::dabc::p', $result['abc*abc']['PatternId']);

        self::assertArrayHasKey('PatternId', $result['abc*def']);
        self::assertSame('tests/test.json::u0::c0::ddef::p', $result['abc*def']['PatternId']);
    }
}
