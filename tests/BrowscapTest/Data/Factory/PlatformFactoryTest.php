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
namespace BrowscapTest\Data\Factory;

use Browscap\Data\Factory\PlatformFactory;

/**
 * Class PlatformTest
 *
 * @category   BrowscapTest
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class PlatformFactoryTest extends \PHPUnit\Framework\TestCase
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
        $platformData = ['abc' => 'def', 'match' => 'test*', 'lite' => true, 'standard' => true];
        $json         = [];
        $platformName = 'Test';

        $deviceData = ['Device_Name' => 'TestDevice'];

        $deviceMock = $this->getMockBuilder(\Browscap\Data\Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $deviceMock->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue($deviceData));

        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDevice'])
            ->getMock();

        $collection->expects(self::any())
            ->method('getDevice')
            ->will(self::returnValue($deviceMock));

        self::assertInstanceOf(
            '\Browscap\Data\Platform',
            $this->object->build($platformData, $json, $platformName, $collection)
        );
    }
}
