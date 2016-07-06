<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   BrowscapTest
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Data;

use Browscap\Data\Expander;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class ExpanderTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class ExpanderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

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
        $this->logger = new Logger('browscapTest', [new NullHandler()]);
        $this->object = new Expander();
    }

    /**
     * tests setter and getter for the data collection
     *
     * @group data
     * @group sourcetest
     */
    public function testGetDataCollectionReturnsSameDatacollectionAsInserted()
    {
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object->setLogger($this->logger);
        self::assertSame($this->object, $this->object->setDataCollection($collection));
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
        $result = $this->object->getVersionParts(1);

        self::assertInternalType('array', $result);
        self::assertSame(['1', 0], $result);
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

        $this->object->setLogger($this->logger);
        self::assertSame($this->object, $this->object->setDataCollection($collection));

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
                            'userAgent'  => 'abc',
                            'properties' => [
                                'Parent'   => 'Defaultproperties',
                                'Version'  => '1.0',
                                'MajorVer' => 1,
                                'Browser'  => 'xyz',
                            ],
                        ],
                    ]
                )
            );

        $this->object->setLogger($this->logger);
        self::assertSame($this->object, $this->object->setDataCollection($collection));

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
                'userAgent'  => 'abc',
                'properties' => ['Parent'   => 'Defaultproperties',
                                      'Version'  => '1.0',
                                      'MajorVer' => 1,
                                      'Browser'  => 'xyz',
                ],
                'children'   => [
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

        $this->object->setLogger($this->logger);
        self::assertSame($this->object, $this->object->setDataCollection($collection));

        $result = $this->object->expand($division, 'TestDivision');
        self::assertInternalType('array', $result);
        self::assertCount(2, $result);
    }
}
