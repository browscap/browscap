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
namespace BrowscapTest\Data\Helper;

use Browscap\Data\Helper\CheckDeviceData;

/**
 * Class DataCollectionTest
 *
 * @category   BrowscapTest
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class CheckDeviceDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Helper\CheckDeviceData
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new CheckDeviceData();
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithDeviceProperties() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('error message');

        $properties = ['Device_Name' => 'test'];
        $this->object->check($properties, 'error message');
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutDeviceProperties() : void
    {
        $properties = [];
        $this->object->check($properties, 'error message');
        self::assertTrue(true);
    }
}
