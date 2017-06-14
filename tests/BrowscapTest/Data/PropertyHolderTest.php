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
 *
 * @copyright  1998-2017 Browser Capabilities Project
 * @license    MIT
 */
namespace BrowscapTest\Data;

use Browscap\Data\PropertyHolder;
use Browscap\Writer\CsvWriter;
use Browscap\Writer\IniWriter;

/**
 * Class PropertyHolderTest
 *
 * @category   BrowscapTest
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class PropertyHolderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\PropertyHolder
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new PropertyHolder();
    }

    /**
     * Data Provider for the test testGetPropertyType
     *
     * @return array<string|string>[]
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
            ['isFake', PropertyHolder::TYPE_BOOLEAN],
            ['isAnonymized', PropertyHolder::TYPE_BOOLEAN],
            ['isModified', PropertyHolder::TYPE_BOOLEAN],
            ['Crawler', PropertyHolder::TYPE_BOOLEAN],
            ['Browser_Type', PropertyHolder::TYPE_IN_ARRAY],
            ['Device_Type', PropertyHolder::TYPE_IN_ARRAY],
            ['Device_Pointing_Method', PropertyHolder::TYPE_IN_ARRAY],
            ['PatternId', PropertyHolder::TYPE_STRING],
            ['PropertyName', PropertyHolder::TYPE_STRING],
        ];
    }

    /**
     * @dataProvider propertyNameTypeDataProvider
     *
     * @group data
     * @group sourcetest
     *
     * @param mixed $propertyName
     * @param mixed $expectedType
     */
    public function testGetPropertyType($propertyName, $expectedType)
    {
        $actualType = $this->object->getPropertyType($propertyName);
        self::assertSame($expectedType, $actualType, "Property {$propertyName} should be {$expectedType} (was {$actualType})");
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testGetPropertyTypeThrowsExceptionIfPropertyNameNotMapped()
    {
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('Property Foobar did not have a defined property type');

        $this->object->getPropertyType('Foobar');
    }

    /**
     * Data Provider for the test testIsLiteModeProperty
     *
     * @return array<string|boolean>[]
     */
    public function litePropertiesDataProvider()
    {
        return [
            ['Comment', true],
            ['Browser', true],
            ['Platform', true],
            ['Platform_Description', false],
            ['Device_Name', false],
            ['Device_Maker', false],
            ['RenderingEngine_Name', false],
            ['RenderingEngine_Description', false],
            ['Parent', true],
            ['Platform_Version', false],
            ['RenderingEngine_Version', false],
            ['Version', true],
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
            ['isMobileDevice', true],
            ['isSyndicationReader', false],
            ['isFake', false],
            ['isAnonymized', false],
            ['isModified', false],
            ['Crawler', false],
            ['Browser_Type', false],
            ['Device_Type', true],
            ['Device_Pointing_Method', false],
            ['Browser_Maker', false],
            ['isTablet', true],
            ['PatternId', false],
        ];
    }

    /**
     * @dataProvider litePropertiesDataProvider
     *
     * @group data
     * @group sourcetest
     *
     * @param mixed $propertyName
     * @param mixed $isExtra
     */
    public function testIsLiteModeProperty($propertyName, $isExtra)
    {
        $actualValue = $this->object->isLiteModeProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }

    public function testIsLiteModePropertyWithWriter()
    {
        $mockWriter = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue('ini'));

        self::assertTrue($this->object->isLiteModeProperty('PatternId', $mockWriter));
    }

    /**
     * Data Provider for the test testIsStandardModeProperty
     *
     * @return array<string|boolean>[]
     */
    public function standardPropertiesDataProvider()
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
            ['Parent', false],
            ['Platform_Version', false],
            ['RenderingEngine_Version', false],
            ['Version', false],
            ['MajorVer', true],
            ['MinorVer', true],
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
            ['isFake', false],
            ['isAnonymized', false],
            ['isModified', false],
            ['Crawler', true],
            ['Browser_Type', false],
            ['Device_Type', false],
            ['Device_Pointing_Method', true],
            ['Browser_Maker', true],
            ['isTablet', false],
            ['PatternId', false],
        ];
    }

    /**
     * @dataProvider standardPropertiesDataProvider
     *
     * @group data
     * @group sourcetest
     *
     * @param mixed $propertyName
     * @param mixed $isExtra
     */
    public function testIsStandardModeProperty($propertyName, $isExtra)
    {
        $actualValue = $this->object->isStandardModeProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }

    /**
     * tests detecting a standard mode property
     *
     * @group data
     * @group sourcetest
     */
    public function testIsStandardModePropertyWithWriter()
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue('csv'));

        self::assertTrue($this->object->isStandardModeProperty('PropertyName', $mockWriter));
    }

    /**
     * Data Provider for the test testIsOutputProperty
     *
     * @return array<string|boolean>[]
     */
    public function outputPropertiesDataProvider()
    {
        return [
            ['Comment', true],
            ['Browser', true],
            ['Platform', true],
            ['Platform_Description', true],
            ['Device_Name', true],
            ['Device_Maker', true],
            ['RenderingEngine_Name', true],
            ['RenderingEngine_Description', true],
            ['Parent', true],
            ['Platform_Version', true],
            ['RenderingEngine_Version', true],
            ['Version', true],
            ['MajorVer', true],
            ['MinorVer', true],
            ['CssVersion', true],
            ['AolVersion', true],
            ['Alpha', true],
            ['Beta', true],
            ['Win16', true],
            ['Win32', true],
            ['Win64', true],
            ['Frames', true],
            ['IFrames', true],
            ['Tables', true],
            ['Cookies', true],
            ['BackgroundSounds', true],
            ['JavaScript', true],
            ['VBScript', true],
            ['JavaApplets', true],
            ['ActiveXControls', true],
            ['isMobileDevice', true],
            ['isSyndicationReader', true],
            ['isFake', true],
            ['isAnonymized', true],
            ['isModified', true],
            ['Crawler', true],
            ['lite', false],
            ['sortIndex', false],
            ['Parents', false],
            ['division', false],
            ['Browser_Type', true],
            ['Device_Type', true],
            ['Device_Pointing_Method', true],
            ['Browser_Maker', true],
            ['isTablet', true],
            ['PatternId', false],
        ];
    }

    /**
     * @dataProvider outputPropertiesDataProvider
     *
     * @group data
     * @group sourcetest
     *
     * @param mixed $propertyName
     * @param mixed $isExtra
     */
    public function testIsOutputProperty($propertyName, $isExtra)
    {
        $actualValue = $this->object->isOutputProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }

    /**
     * tests detecting a output property if a writer is given
     *
     * @group data
     * @group sourcetest
     */
    public function testIsOutputPropertyWithWriter()
    {
        $mockWriterCsv = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterCsv
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue('csv'));

        self::assertTrue($this->object->isOutputProperty('PropertyName', $mockWriterCsv));

        $mockWriterIni = $this->getMockBuilder(\Browscap\Writer\IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(self::exactly(2))
            ->method('getType')
            ->will(self::returnValue('ini'));

        self::assertTrue($this->object->isOutputProperty('PatternId', $mockWriterIni));
    }

    /**
     * Data Provider for the test testCheckValueInArray
     *
     * @return array<string|string>[]
     */
    public function checkValueInArrayProvider()
    {
        return [
            ['Browser_Type', 'Browser'],
            ['Device_Type', 'Tablet'],
            ['Device_Pointing_Method', 'touchscreen'],
            ['Browser_Bits', '32'],
            ['Platform_Bits', '64'],
        ];
    }

    /**
     * @dataProvider checkValueInArrayProvider
     *
     * @group data
     * @group sourcetest
     *
     * @param mixed $propertyName
     * @param mixed $propertyValue
     */
    public function testCheckValueInArray($propertyName, $propertyValue)
    {
        $actualValue = $this->object->checkValueInArray($propertyName, $propertyValue);
        self::assertSame($propertyValue, $actualValue);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckValueInArrayExceptionUndfinedProperty()
    {
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('Property "abc" is not defined to be validated');

        $this->object->checkValueInArray('abc', 'bcd');
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckValueInArrayExceptionWrongValue()
    {
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('invalid value given for Property "Browser_Type": given value "bcd", allowed: ["Useragent Anonymizer","Browser","Offline Browser","Multimedia Player","Library","Feed Reader","Email Client","Bot\/Crawler","Application","Tool","unknown"]');

        $this->object->checkValueInArray('Browser_Type', 'bcd');
    }
}
