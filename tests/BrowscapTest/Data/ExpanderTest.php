<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\Expander;
use Monolog\Handler\NullHandler;
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
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $logger       = new Logger('browscapTest', [new NullHandler()]);
        $this->object = new Expander($logger);
    }

    /**
     * tests setter and getter for the data collection
     *
     * @group data
     * @group sourcetest
     */
    public function testGetDataCollectionReturnsSameDatacollectionAsInserted()
    {
        $collection = $this->createMock(\Browscap\Data\DataCollection::class);

        $this->object->setDataCollection($collection);
        self::assertSame($collection, $this->object->getDataCollection());
    }

    /**
     * tests setter and getter for the version parts
     *
     * @group data
     * @group sourcetest
     */
    public function testGetVersionParts()
    {
        $result = $this->object->getVersionParts('1');

        self::assertInternalType('array', $result);
        self::assertSame(['1', '0'], $result);
    }

    /**
     * tests parsing an empty data collection
     *
     * @group data
     * @group sourcetest
     */
    public function testParseDoesNothingOnEmptyDatacollection()
    {
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::never())
            ->method('getDivisions')
            ->will(self::returnValue([]));

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => ['properties' => ['avd' => 'xyz']]]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([]));

        $this->object->setDataCollection($collection);

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
    public function testParseOnNotEmptyDatacollectionWithoutChildren()
    {
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::never())
            ->method('getDivisions')
            ->will(self::returnValue([]));

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => ['properties' => ['avd' => 'xyz']]]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => [
                            'userAgent' => 'abc',
                            'properties' => [
                                'Parent' => 'Defaultproperties',
                                'Version' => '1.0',
                                'MajorVer' => 1,
                                'Browser' => 'xyz',
                            ],
                        ],
                    ]
                )
            );

        $this->object->setDataCollection($collection);

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
    public function testParseOnNotEmptyDatacollectionWithChildren()
    {
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::never())
            ->method('getDivisions')
            ->will(self::returnValue([]));

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => ['properties' => ['avd' => 'xyz']]]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $uaData = [
            0 => [
                'userAgent' => 'abc',
                'properties' => ['Parent' => 'Defaultproperties',
                                      'Version' => '1.0',
                                      'MajorVer' => 1,
                                      'Browser' => 'xyz',
                ],
                'children' => [
                    0 => [
                        'match' => 'abc*',
                        'properties' => ['Browser' => 'xyza'],
                    ],
                ],
            ],
        ];

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($uaData));

        $this->object->setDataCollection($collection);

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
    public function testParseOnNotEmptyDatacollectionWithChildrenAndDevices()
    {
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getDevice'])
            ->getMock();

        $collection
            ->expects(self::never())
            ->method('getDivisions')
            ->will(self::returnValue([]));

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => ['properties' => ['avd' => 'xyz']]]));

        $device = $this->getMockBuilder(\Browscap\Data\Device::class)
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

        $uaData = [
            0 => [
                'userAgent' => 'abc',
                'properties' => ['Parent' => 'Defaultproperties',
                                      'Version' => '1.0',
                                      'MajorVer' => 1,
                                      'Browser' => 'xyz',
                ],
                'children' => [
                    0 => [
                        'match' => 'abc*#DEVICE#',
                        'devices' => [
                            'abc' => 'ABC',
                            'def' => 'DEF',
                        ],
                        'properties' => ['Browser' => 'xyza'],
                    ],
                ],
            ],
        ];

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($uaData));

        $this->object->setDataCollection($collection);

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
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildren()
    {
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties'])
            ->getMock();

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => ['properties' => ['avd' => 'xyz']]]));

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $uaData = [
            0 => [
                'userAgent' => 'abc',
                'properties' => ['Parent' => 'Defaultproperties',
                                      'Version' => '1.0',
                                      'MajorVer' => 1,
                                      'Browser' => 'xyz',
                ],
                'children' => [
                    0 => [
                        'match' => 'abc*',
                        'properties' => ['Browser' => 'xyza'],
                    ],
                ],
            ],
        ];

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($uaData));

        $division
            ->expects(self::once())
            ->method('getFileName')
            ->will(self::returnValue('tests/test.json'));

        $this->object->setDataCollection($collection);

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
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndPlatforms()
    {
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getPlatform'])
            ->getMock();

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => ['properties' => ['avd' => 'xyz']]]));

        $platform = $this->getMockBuilder(\Browscap\Data\Platform::class)
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

        $uaData = [
            0 => [
                'userAgent' => 'abc',
                'properties' => ['Parent' => 'Defaultproperties',
                                      'Version' => '1.0',
                                      'MajorVer' => 1,
                                      'Browser' => 'xyz',
                ],
                'children' => [
                    0 => [
                        'match' => 'abc*#PLATFORM#',
                        'properties' => ['Browser' => 'xyza'],
                        'platforms' => [
                            'Platform_1',
                        ],
                    ],
                ],
            ],
        ];

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($uaData));

        $division
            ->expects(self::once())
            ->method('getFileName')
            ->will(self::returnValue('tests/test.json'));

        $this->object->setDataCollection($collection);

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
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenAndDevices()
    {
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getDevice', 'getPlatform'])
            ->getMock();

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => ['properties' => ['avd' => 'xyz']]]));

        $device = $this->getMockBuilder(\Browscap\Data\Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $platform = $this->getMockBuilder(\Browscap\Data\Platform::class)
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

        $uaData = [
            0 => [
                'userAgent' => 'abc',
                'properties' => ['Parent' => 'Defaultproperties',
                                      'Version' => '1.0',
                                      'MajorVer' => 1,
                                      'Browser' => 'xyz',
                ],
                'children' => [
                    0 => [
                        'match' => 'abc*#DEVICE#',
                        'devices' => [
                            'abc' => 'ABC',
                            'def' => 'DEF',
                        ],
                        'platforms' => ['Platform_1'],
                        'properties' => ['Browser' => 'xyza'],
                    ],
                ],
            ],
        ];

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($uaData));

        $division
            ->expects(self::once())
            ->method('getFileName')
            ->will(self::returnValue('tests/test.json'));

        $this->object->setDataCollection($collection);

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
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenPlatformsAndDevices()
    {
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDivisions', 'getDefaultProperties', 'getDevice'])
            ->getMock();

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => ['properties' => ['avd' => 'xyz']]]));

        $device = $this->getMockBuilder(\Browscap\Data\Device::class)
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

        $uaData = [
            0 => [
                'userAgent' => 'abc',
                'properties' => ['Parent' => 'Defaultproperties',
                                      'Version' => '1.0',
                                      'MajorVer' => 1,
                                      'Browser' => 'xyz',
                ],
                'children' => [
                    0 => [
                        'match' => 'abc*#DEVICE#',
                        'devices' => [
                            'abc' => 'ABC',
                            'def' => 'DEF',
                        ],
                        'properties' => ['Browser' => 'xyza'],
                    ],
                ],
            ],
        ];

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getFileName'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($uaData));

        $division
            ->expects(self::once())
            ->method('getFileName')
            ->will(self::returnValue('tests/test.json'));

        $this->object->setDataCollection($collection);

        $result = $this->object->expand($division, 'TestDivision');

        self::assertArrayHasKey('PatternId', $result['abc*abc']);
        self::assertSame('tests/test.json::u0::c0::dabc::p', $result['abc*abc']['PatternId']);

        self::assertArrayHasKey('PatternId', $result['abc*def']);
        self::assertSame('tests/test.json::u0::c0::ddef::p', $result['abc*def']['PatternId']);
    }
}
