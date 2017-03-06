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

namespace BrowscapTest\Formatter;

use Browscap\Formatter\CsvFormatter;

/**
 * Class CsvFormatterTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class CsvFormatterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Formatter\CsvFormatter
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new CsvFormatter();
    }

    /**
     * tests getter for the formatter type
     *
     * @group formatter
     * @group sourcetest
     */
    public function testGetType()
    {
        self::assertSame('csv', $this->object->getType());
    }

    /**
     * tests setter and getter for the filter
     *
     * @group formatter
     * @group sourcetest
     */
    public function testSetGetFilter()
    {
        $mockFilter = $this->createMock(\Browscap\Filter\StandardFilter::class);

        self::assertSame($this->object, $this->object->setFilter($mockFilter));
        self::assertSame($mockFilter, $this->object->getFilter());
    }

    /**
     * tests formatting a property name
     *
     * @group formatter
     * @group sourcetest
     */
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
            ['Comment', 'test', '"test"'],
            ['Browser', 'test', '"test"'],
            ['Platform', 'test', '"test"'],
            ['Platform_Description', 'test', '"test"'],
            ['Device_Name', 'test', '"test"'],
            ['Device_Maker', 'test', '"test"'],
            ['RenderingEngine_Name', 'test', '"test"'],
            ['RenderingEngine_Description', 'test', '"test"'],
            ['Parent', 'test', '"test"'],
            ['Platform_Version', 'test', '"test"'],
            ['RenderingEngine_Version', 'test', '"test"'],
            ['Version', 'test', '"test"'],
            ['MajorVer', 'test', '"test"'],
            ['MinorVer', 'test', '"test"'],
            ['CssVersion', 'test', '"test"'],
            ['AolVersion', 'test', '"test"'],
            ['Alpha', 'true', '"true"'],
            ['Beta', 'false', '"false"'],
            ['Win16', 'test', '""'],
            ['Browser_Type', 'Browser', '"Browser"'],
            ['Device_Type', 'Tablet', '"Tablet"'],
            ['Device_Pointing_Method', 'mouse', '"mouse"'],
        ];
    }

    /**
     * tests formatting a property value
     *
     * @dataProvider propertyNameTypeDataProvider
     *
     * @param string $propertyName
     * @param string $inputValue
     * @param string $expectedValue
     *
     * @group formatter
     * @group sourcetest
     */
    public function testFormatPropertyValue($propertyName, $inputValue, $expectedValue)
    {
        $actualValue = $this->object->formatPropertyValue($inputValue, $propertyName);
        self::assertSame($expectedValue, $actualValue, "Property {$propertyName} should be {$expectedValue} (was {$actualValue})");
    }
}
