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

use Browscap\Formatter\CsvFormatter;

/**
 * Class CsvFormatterTest
 *
 * @category   BrowscapTest
 * @package    Formatter
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class CsvFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Browscap\Formatter\CsvFormatter
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    public function setUp()
    {
        $this->object = new CsvFormatter();
    }

    public function testGetType()
    {
        self::assertSame('CSV', $this->object->getType());
    }

    public function testFormatPropertyName()
    {
        self::assertSame('"text"', $this->object->formatPropertyName('text'));
    }

    /**
     * Data Provider for the test testGetPropertyType
     *
     * @return array[]
     */
    public function propertyNameTypeDataProvider()
    {
        return [
            ['Comment', '"test"'],
            ['Browser', '"test"'],
            ['Platform', '"test"'],
            ['Platform_Description', '"test"'],
            ['Device_Name', '"test"'],
            ['Device_Maker', '"test"'],
            ['RenderingEngine_Name', '"test"'],
            ['RenderingEngine_Description', '"test"'],
            ['Parent', '"test"'],
            ['Platform_Version', '"test"'],
            ['RenderingEngine_Version', '"test"'],
            ['Version', '"test"'],
            ['MajorVer', '"test"'],
            ['MinorVer', '"test"'],
            ['CssVersion', '"test"'],
            ['AolVersion', '"test"'],
            ['Alpha', '"test"'],
            ['Beta', '"test"'],
            ['Win16', '"test"'],
            ['Win32', '"test"'],
            ['Win64', '"test"'],
            ['Frames', '"test"'],
            ['IFrames', '"test"'],
            ['Tables', '"test"'],
            ['Cookies', '"test"'],
            ['BackgroundSounds', '"test"'],
            ['JavaScript', '"test"'],
            ['VBScript', '"test"'],
            ['JavaApplets', '"test"'],
            ['ActiveXControls', '"test"'],
            ['isMobileDevice', '"test"'],
            ['isSyndicationReader', '"test"'],
            ['Crawler', '"test"'],
            ['Browser_Type', '"test"'],
            ['Device_Type', '"test"'],
            ['Device_Pointing_Method', '"test"'],
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
