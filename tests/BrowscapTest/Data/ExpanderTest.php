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
 * @package    Data
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
 * @package    Data
 * @author     James Titcumb <james@asgrim.com>
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
     *
     */
    public function setUp()
    {
        $this->logger = new Logger('browscapTest', array(new NullHandler()));
        $this->object = new Expander();
    }

    public function testGetDataCollectionReturnsSameDatacollectionAsInserted()
    {
        $mock = $this->getMock('\\Browscap\\Generator\\DataCollection', array(), array(), '', false);

        $this->object->setLogger($this->logger);
        self::assertSame($this->object, $this->object->setDataCollection($mock));
    }

    public function testParseDoesNothingOnEmptyDatacollection()
    {
        $mock = $this->getMock('\\Browscap\\Generator\\DataCollection', array('getDivisions'), array(), '', false);
        $mock->expects($this->once())
            ->method('getDivisions')
            ->will(self::returnValue(array()))
        ;

        $this->object->setLogger($this->logger);
        self::assertSame($this->object, $this->object->setDataCollection($mock));

        $result = $this->object->parse();
        self::assertInternalType('array', $result);
        self::assertCount(0, $result);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage the parent element "abc" for key "test/1.*" is not added before the element, please change the SortIndex
     */
    public function testParseSkipsEmptyOrInvalidDivisions()
    {
        $divisions = array(
            array('division' => 'Browscap Version'),
            array(
                'division' => 'DefaultProperties',
                'sortIndex' => 1,
                'lite' => true,
                'userAgents' => array(array('userAgent' => 'DefaultProperties', 'properties' => array('Browser' => 'test', 'Version' => '0')))
            ),
            array(
                'division' => 'abc',
                'sortIndex' => 2,
                'lite' => false,
                'userAgents' => array(array('userAgent' => 'test', 'properties' => array('Parent' => 'DefaultProperties')))
            ),
            array(
                'division' => 'abc #MAJORVER#.#MINORVER#',
                'versions' => array('1.0'),
                'sortIndex' => 3,
                'userAgents' => array(array('userAgent' => 'test/1.*', 'properties' => array('Parent' => 'abc', 'Version' => '#MAJORVER#.#MINORVER#')))
            ),
            array(
                'division' => 'abc #MAJORVER#.#MINORVER#',
                'versions' => array('2.0'),
                'sortIndex' => 4,
                'userAgents' => array(array('userAgent' => 'test/2.*', 'properties' => array('Version' => '#MAJORVER#.#MINORVER#')))
            ),
        );

        $mock = $this->getMock('\\Browscap\\Generator\\DataCollection', array('getDivisions'), array(), '', false);
        $mock->expects($this->once())
            ->method('getDivisions')
            ->will(self::returnValue($divisions))
        ;

        $this->object->setLogger($this->logger);
        self::assertSame($this->object, $this->object->setDataCollection($mock));

        $this->object->parse();
    }

    public function testParseParsesChildren()
    {
        $divisions = array(
            array('division' => 'Browscap Version'),
            array(
                'division' => 'DefaultProperties',
                'sortIndex' => 1,
                'lite' => true,
                'userAgents' => array(
                    array(
                        'userAgent' => 'DefaultProperties',
                        'properties' => array('Browser' => 'test', 'Version' => '1.0')
                    )
                )
            ),
            array(
                'division' => 'abc',
                'sortIndex' => 2,
                'lite' => false,
                'userAgents' => array(
                    array(
                        'userAgent' => 'test',
                        'properties' => array('Parent' => 'DefaultProperties'),
                        'children' => array(
                            array(
                                'match' => 'abc/#PLATFORM#*',
                                'platforms' => array('testOS')
                            ),
                            array(
                                'match' => 'abc/* (#PLATFORM#)',
                                'platforms' => array()
                            ),
                            array(
                                'match' => 'abc/1.0* (#PLATFORM#)',
                            )
                        )
                    )
                )
            ),
        );

        $platform = array(
            'match' => '*TestOS*',
            'properties' => array(
                'Platform' => 'TestOS'
            )
        );

        $mock = $this->getMock(
            '\\Browscap\\Generator\\DataCollection', array('getDivisions', 'getPlatform'), array(), '', false
        );
        $mock->expects($this->once())
            ->method('getDivisions')
            ->will(self::returnValue($divisions))
        ;
        $mock->expects($this->once())
            ->method('getPlatform')
            ->will(self::returnValue($platform))
        ;

        $this->object->setLogger($this->logger);
        self::assertSame($this->object, $this->object->setDataCollection($mock));

        $result = $this->object->parse();
        self::assertInternalType('array', $result);

        $expected = array (
            'DefaultProperties' => array (
                'lite' => '1',
                'sortIndex' => '1',
                'division' => 'DefaultProperties',
                'Browser' => 'test',
                'Version' => '1.0',
                'Parents' => '',
                'MajorVer' => '1',
                'MinorVer' => '0',
            ),
            'test' => array (
                'lite' => '',
                'sortIndex' => '2',
                'division' => 'abc',
                'Parent' => 'DefaultProperties',
                'Browser' => 'test',
                'Version' => '1.0',
                'Parents' => 'DefaultProperties',
                'MajorVer' => '1',
                'MinorVer' => '0',
            ),
            'abc/*TestOS**' => array (
                'Parent' => 'test',
                'Platform' => 'TestOS',
                'lite' => '',
                'sortIndex' => '2',
                'division' => 'abc',
                'Browser' => 'test',
                'Version' => '1.0',
                'Parents' => 'DefaultProperties,test',
                'MajorVer' => '1',
                'MinorVer' => '0',
            ),
            'abc/1.0* (#PLATFORM#)' => array (
                'Parent' => 'test',
                'lite' => '',
                'sortIndex' => '2',
                'division' => 'abc',
                'Browser' => 'test',
                'Version' => '1.0',
                'Parents' => 'DefaultProperties,test',
                'MajorVer' => '1',
                'MinorVer' => '0',
            )
        );
        self::assertSame($expected, $result);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage each entry of the children property requires an "match" entry for key "test"
     */
    public function testParseInvalidChildren()
    {
        $divisions = array(
            array('division' => 'Browscap Version'),
            array(
                'division' => 'DefaultProperties',
                'sortIndex' => 1,
                'lite' => true,
                'userAgents' => array(
                    array(
                        'userAgent' => 'DefaultProperties',
                        'properties' => array('Browser' => 'test', 'Version' => '1.0')
                    )
                )
            ),
            array(
                'division' => 'abc',
                'sortIndex' => 2,
                'lite' => false,
                'userAgents' => array(
                    array(
                        'userAgent' => 'test',
                        'properties' => array('Parent' => 'DefaultProperties'),
                        'children' => array(
                            array(
                                'match' => 'abc/#PLATFORM#*',
                                'platforms' => array('testOS')
                            ),
                            array(
                                'platforms' => array()
                            ),
                            array(
                                'match' => 'abc/1.0* (#PLATFORM#)',
                            )
                        )
                    )
                )
            ),
        );

        $platform = array(
            'match' => '*TestOS*',
            'properties' => array(
                'Platform' => 'TestOS'
            )
        );

        $mock = $this->getMock(
            '\\Browscap\\Generator\\DataCollection', array('getDivisions', 'getPlatform'), array(), '', false
        );
        $mock->expects($this->once())
            ->method('getDivisions')
            ->will(self::returnValue($divisions))
        ;
        $mock->expects($this->once())
            ->method('getPlatform')
            ->will(self::returnValue($platform))
        ;

        $this->object->setLogger($this->logger);
        self::assertSame($this->object, $this->object->setDataCollection($mock));

        $this->object->parse();
    }
}
