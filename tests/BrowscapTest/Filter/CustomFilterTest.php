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

namespace BrowscapTest\Filter;

use Browscap\Filter\CustomFilter;

/**
 * Class CustomFilterTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class CustomFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Filter\CustomFilter
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new CustomFilter(['Parent']);
    }

    /**
     * tests getter for the filter type
     *
     * @group filter
     * @group sourcetest
     */
    public function testGetType()
    {
        self::assertSame('CUSTOM', $this->object->getType());
    }

    /**
     * tests detecting if a divion should be in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutput()
    {
        $division = $this->createMock(\Browscap\Data\Division::class);

        self::assertTrue($this->object->isOutput($division));
    }

    /**
     * Data Provider for the test testIsOutputProperty
     *
     * @return array<string|boolean>[]
     */
    public function outputPropertiesDataProvider()
    {
        return [
            ['Comment', false],
            ['Browser', false],
            ['Platform', false],
            ['Platform_Description', false],
            ['Device_Name', false],
            ['Device_Maker', false],
            ['RenderingEngine_Name', false],
            ['RenderingEngine_Description', false],
            ['Parent', true],
            ['Platform_Version', false],
            ['RenderingEngine_Version', false],
            ['Version', false],
            ['MajorVer', false],
            ['MinorVer', false],
            ['CssVersion', false],
            ['AolVersion', false],
            ['Alpha', false],
            ['Beta', false],
            ['Win16', false],
            ['Win32', false],
            ['Win64', false],
            ['Frames', false],
            ['IFrames', false],
            ['Tables', false],
            ['Cookies', false],
            ['BackgroundSounds', false],
            ['JavaScript', false],
            ['VBScript', false],
            ['JavaApplets', false],
            ['ActiveXControls', false],
            ['isMobileDevice', false],
            ['isSyndicationReader', false],
            ['Crawler', false],
            ['lite', false],
            ['sortIndex', false],
            ['Parents', false],
            ['division', false],
            ['Browser_Type', false],
            ['Device_Type', false],
            ['Device_Pointing_Method', false],
            ['isTablet', false],
            ['Browser_Maker', false],
        ];
    }

    /**
     * @dataProvider outputPropertiesDataProvider
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutputProperty($propertyName, $isExtra)
    {
        $actualValue = $this->object->isOutputProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }

    /**
     * tests if a section is always in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutputSectionAlways()
    {
        $this->assertTrue($this->object->isOutputSection([]));
        $this->assertTrue($this->object->isOutputSection(['lite' => false]));
        $this->assertTrue($this->object->isOutputSection(['lite' => true]));
    }
}
