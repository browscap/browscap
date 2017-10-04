<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\DataCollection;
use Browscap\Data\Device;
use Browscap\Data\Division;
use Browscap\Data\Expander;
use Browscap\Data\Platform;
use Browscap\Data\Useragent;
use Monolog\Logger;

/**
 * Class ExpanderTest
 *
 * @category   BrowscapTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class ExpanderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Expander
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
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
     * @group data
     * @group sourcetest
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

        $useragent = $this->getMockBuilder(Useragent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

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
     * @group data
     * @group sourcetest
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

        $useragent = $this->getMockBuilder(Useragent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $useragent = $this->getMockBuilder(Useragent::class)
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
     * @group data
     * @group sourcetest
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

        $useragent = $this->getMockBuilder(Useragent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $useragent = $this->getMockBuilder(Useragent::class)
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
     * @group data
     * @group sourcetest
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

        $useragent = $this->getMockBuilder(Useragent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $device = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $device
            ->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue([]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $collection
            ->expects(self::exactly(2))
            ->method('getDevice')
            ->will(self::returnValue($device));

        $useragent = $this->getMockBuilder(Useragent::class)
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
     * @group data
     * @group sourcetest
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildren() : void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $useragent = $this->getMockBuilder(Useragent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $useragent = $this->getMockBuilder(Useragent::class)
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
     * @group data
     * @group sourcetest
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndPlatforms() : void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getPlatform'])
            ->getMock();

        $useragent = $this->getMockBuilder(Useragent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $platform = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $platform
            ->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue([]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $collection
            ->expects(self::any())
            ->method('getPlatform')
            ->will(self::returnValue($platform));

        $useragent = $this->getMockBuilder(Useragent::class)
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
     * @group data
     * @group sourcetest
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndDevices() : void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform'])
            ->getMock();

        $useragent = $this->getMockBuilder(Useragent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $device = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $platform = $this->getMockBuilder(Platform::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $platform
            ->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue([]));

        $device
            ->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue([]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $collection
            ->expects(self::exactly(2))
            ->method('getDevice')
            ->will(self::returnValue($device));

        $collection
            ->expects(self::any())
            ->method('getPlatform')
            ->will(self::returnValue($platform));

        $useragent = $this->getMockBuilder(Useragent::class)
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
     * @group data
     * @group sourcetest
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenPlatformsAndDevices() : void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getDevice'])
            ->getMock();

        $useragent = $this->getMockBuilder(Useragent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue(['avd' => 'xyz']));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $device = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $device
            ->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue([]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $collection
            ->expects(self::exactly(2))
            ->method('getDevice')
            ->will(self::returnValue($device));

        $useragent = $this->getMockBuilder(Useragent::class)
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
