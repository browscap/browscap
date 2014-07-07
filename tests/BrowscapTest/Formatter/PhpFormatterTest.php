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
 * @package    Formatter
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Formatter;

use Browscap\Formatter\PhpFormatter;
use Browscap\Data\PropertyHolder;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class PhpFormatterTest
 *
 * @category   BrowscapTest
 * @package    Formatter
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class PhpFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Browscap\Formatter\PhpFormatter
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    public function setUp()
    {
        $this->object = new PhpFormatter();
    }

    public function testGetType()
    {
        self::assertSame('ASP', $this->object->getType());
    }

    public function testFormatPropertyName()
    {
        self::assertSame('text', $this->object->formatPropertyName('text'));
    }

    /**
     * Data Provider for the test testGetPropertyType
     *
     * @return array[]
     */
    public function propertyNameTypeDataProvider()
    {
        return [
            ['Comment', PropertyHolder::TYPE_STRING],
            ['Browser', PropertyHolder::TYPE_STRING],
            ['Platform', PropertyHolder::TYPE_STRING],
            ['Platform_Description', PropertyHolder::TYPE_STRING],
            ['Device_Name', PropertyHolder::TYPE_STRING],
            ['Device_Maker', PropertyHolder::TYPE_STRING],
            ['RenderingEngine_Name', PropertyHolder::TYPE_STRING],
            ['RenderingEngine_Description', PropertyHolder::TYPE_STRING],
            ['Parent', PropertyHolder::TYPE_STRING],
            ['Platform_Version', PropertyHolder::TYPE_GENERIC],
            ['RenderingEngine_Version', PropertyHolder::TYPE_GENERIC],
            ['Version', PropertyHolder::TYPE_NUMBER],
            ['MajorVer', PropertyHolder::TYPE_NUMBER],
            ['MinorVer', PropertyHolder::TYPE_NUMBER],
            ['CssVersion', PropertyHolder::TYPE_NUMBER],
            ['AolVersion', PropertyHolder::TYPE_NUMBER],
            ['Alpha', PropertyHolder::TYPE_BOOLEAN],
            ['Beta', PropertyHolder::TYPE_BOOLEAN],
            ['Win16', PropertyHolder::TYPE_BOOLEAN],
            ['Win32', PropertyHolder::TYPE_BOOLEAN],
            ['Win64', PropertyHolder::TYPE_BOOLEAN],
            ['Frames', PropertyHolder::TYPE_BOOLEAN],
            ['IFrames', PropertyHolder::TYPE_BOOLEAN],
            ['Tables', PropertyHolder::TYPE_BOOLEAN],
            ['Cookies', PropertyHolder::TYPE_BOOLEAN],
            ['BackgroundSounds', PropertyHolder::TYPE_BOOLEAN],
            ['JavaScript', PropertyHolder::TYPE_BOOLEAN],
            ['VBScript', PropertyHolder::TYPE_BOOLEAN],
            ['JavaApplets', PropertyHolder::TYPE_BOOLEAN],
            ['ActiveXControls', PropertyHolder::TYPE_BOOLEAN],
            ['isMobileDevice', PropertyHolder::TYPE_BOOLEAN],
            ['isSyndicationReader', PropertyHolder::TYPE_BOOLEAN],
            ['Crawler', PropertyHolder::TYPE_BOOLEAN],
            ['Browser_Type', PropertyHolder::TYPE_IN_ARRAY],
            ['Device_Type', PropertyHolder::TYPE_IN_ARRAY],
            ['Device_Pointing_Method', PropertyHolder::TYPE_IN_ARRAY],
        ];
    }

    /**
     * @dataProvider propertyNameTypeDataProvider
     */
    public function testFormatPropertyValue($propertyName, $expectedValue)
    {
        $actualValue = $this->object->formatPropertyValue('test', $propertyName);
        self::assertSame($expectedValue, $actualValue, "Property {$propertyName} should be {$expectedValue} (was {$actualValue})");
    }
}
