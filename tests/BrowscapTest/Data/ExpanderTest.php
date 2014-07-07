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
     *
     */
    public function setUp()
    {
        $this->logger = new Logger('browscapTest', array(new NullHandler()));
        $this->object = new Expander();
    }

    public function testGetDataCollectionReturnsSameDatacollectionAsInserted()
    {
        $mockCollection = $this->getMock('\Browscap\Data\DataCollection', array(), array(), '', false);

        $this->object->setLogger($this->logger);
        self::assertSame($this->object, $this->object->setDataCollection($mockCollection));
        self::assertSame($mockCollection, $this->object->getDataCollection());
    }

    public function testParseDoesNothingOnEmptyDatacollection()
    {
        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            array('getDivisions', 'getDefaultProperties'),
            array(),
            '',
            false
        );
        $mockCollection
            ->expects(self::never())
            ->method('getDivisions')
            ->will(self::returnValue(array()))
        ;

        $mockDivision = $this->getMock('\Browscap\Data\Division', array('getUserAgents'), array(), '', false);
        $mockDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue(array(0 => array('properties' => array('avd' => 'xyz')))))
        ;

        $mockCollection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($mockDivision))
        ;

        $mockDivision = $this->getMock('\Browscap\Data\Division', array('getUserAgents'), array(), '', false);
        $mockDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue(array()))
        ;

        $this->object->setLogger($this->logger);
        self::assertSame($this->object, $this->object->setDataCollection($mockCollection));

        $result = $this->object->expand($mockDivision, 0, 0, 'TestDivision');
        self::assertInternalType('array', $result);
        self::assertCount(0, $result);
    }
}
