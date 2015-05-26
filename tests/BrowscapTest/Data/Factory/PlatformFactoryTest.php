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

namespace BrowscapTest\Data\Factory;

use Browscap\Data\Factory\PlatformFactory;

/**
 * Class PlatformTest
 *
 * @category   BrowscapTest
 * @package    Data
 * @author     James Titcumb <james@asgrim.com>
 */
class PlatformFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Browscap\Data\Factory\PlatformFactory
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new PlatformFactory();
    }

    /**
     * tests the creating of an platform factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuild()
    {
        $platformData = array('abc' => 'def', 'match' => 'test*');
        $json         = array();
        $platformName = 'Test';

        $deviceData = array('Device_Name' => 'TestDevice');

        $deviceMock = $this->getMock('\Browscap\Data\Device', array('getProperties'), array(), '', false);
        $deviceMock->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue($deviceData))
        ;

        $datacollection = $this->getMock('\Browscap\Data\DataCollection', array('getDevice'), array(), '', false);
        $datacollection->expects(self::any())
            ->method('getDevice')
            ->will(self::returnValue($deviceMock))
        ;

        self::assertInstanceOf(
            '\Browscap\Data\Platform',
            $this->object->build($platformData, $json, $platformName, $datacollection)
        );
    }
}
