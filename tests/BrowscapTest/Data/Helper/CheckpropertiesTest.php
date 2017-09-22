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

use Browscap\Data\Helper\CheckProperties;

/**
 * Class DataCollectionTest
 *
 * @category   BrowscapTest
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class CheckpropertiesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Helper\CheckProperties
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new CheckProperties();
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutVersion() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('Version property not found for key "test"');

        $properties = [];
        $this->object->check('test', $properties);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutDeviceType() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('property "Device_Type" is missing for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
        ];

        $this->object->check('test', $properties);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutIsTablet() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('property "isTablet" is missing for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Desktop',
        ];

        $this->object->check('test', $properties);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutIsMobileDevice() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('property "isMobileDevice" is missing for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Desktop',
            'isTablet' => false,
        ];

        $this->object->check('test', $properties);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyOk() : void
    {
        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Desktop',
            'isTablet' => false,
            'isMobileDevice' => false,
        ];

        $this->object->check('test', $properties);

        self::assertTrue(true);
    }
}
