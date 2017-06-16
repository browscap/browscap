<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   BrowscapTest
 * @copyright  1998-2017 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Formatter;

use Browscap\Formatter\PhpFormatter;

/**
 * Class PhpFormatterTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class PhpFormatterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Formatter\PhpFormatter
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new PhpFormatter();
    }

    /**
     * tests getter for the formatter type
     *
     * @group formatter
     * @group sourcetest
     */
    public function testGetType()
    {
        self::assertSame('php', $this->object->getType());
    }

    /**
     * tests setter and getter for the filter
     *
     * @group formatter
     * @group sourcetest
     */
    public function testSetGetFilter()
    {
        $mockFilter = $this->createMock(\Browscap\Filter\FullFilter::class);

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
            ['Comment', 'test', '"test"'],
            ['Browser', 'test', '"test"'],
            ['Platform', 'test', '"test"'],
            ['Platform_Description', 'test', '"test"'],
            ['Device_Name', 'test', '"test"'],
            ['Device_Maker', 'test', '"test"'],
            ['RenderingEngine_Name', 'test', '"test"'],
            ['RenderingEngine_Description', 'test', '"test"'],
            ['Parent', 'test', '"test"'],
            ['Platform_Version', 'test', 'test'],
            ['RenderingEngine_Version', 'test', 'test'],
            ['Version', 'test', 'test'],
            ['MajorVer', 'test', 'test'],
            ['MinorVer', 'test', 'test'],
            ['CssVersion', 'test', 'test'],
            ['AolVersion', 'test', 'test'],
            ['Alpha', 'true', '"true"'],
            ['Beta', 'false', '"false"'],
            ['Win16', 'test', ''],
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
