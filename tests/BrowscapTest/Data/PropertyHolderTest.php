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

namespace BrowscapTest\Data;

use Browscap\Data\PropertyHolder;

/**
 * Class PropertyHolderTest
 *
 * @category   BrowscapTest
 * @package    Data
 * @author     James Titcumb <james@asgrim.com>
 */
class PropertyHolderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Browscap\Data\PropertyHolder
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    public function setUp()
    {
        $this->object = new PropertyHolder();
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
    public function testGetPropertyType($propertyName, $expectedType)
    {
        $actualType = $this->object->getPropertyType($propertyName);
        self::assertSame($expectedType, $actualType, "Property {$propertyName} should be {$expectedType} (was {$actualType})");
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Property Foobar did not have a defined property type
     */
    public function testGetPropertyTypeThrowsExceptionIfPropertyNameNotMapped()
    {
        $this->object->getPropertyType('Foobar');
    }

    /**
     * Data Provider for the test testIsExtraProperty
     *
     * @return array[]
     */
    public function extraPropertiesDataProvider()
    {
        return [
            ['Comment', false],
            ['Browser', false],
            ['Platform', false],
            ['Platform_Description', true],
            ['Device_Name', true],
            ['Device_Maker', true],
            ['RenderingEngine_Name', true],
            ['RenderingEngine_Description', true],
            ['Parent', false],
            ['Platform_Version', false],
            ['RenderingEngine_Version', true],
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
            ['Browser_Type', true],
            ['Device_Type', true],
            ['Device_Pointing_Method', true],
        ];
    }

    /**
     * @dataProvider extraPropertiesDataProvider
     */
    public function testIsExtraProperty($propertyName, $isExtra)
    {
        $actualValue = $this->object->isExtraProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }

    /**
     * Data Provider for the test testIsOutputProperty
     *
     * @return array
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
            ['Crawler', true],
            ['lite', false],
            ['sortIndex', false],
            ['Parents', false],
            ['division', false],
            ['Browser_Type', true],
            ['Device_Type', true],
            ['Device_Pointing_Method', true],
        ];
    }

    /**
     * @dataProvider outputPropertiesDataProvider
     */
    public function testIsOutputProperty($propertyName, $isExtra)
    {
        $actualValue = $this->object->isOutputProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }

    /**
     * Data Provider for the test testCheckValueInArray
     *
     * @return array
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
     */
    public function testCheckValueInArray($propertyName, $propertyValue)
    {
        $actualValue = $this->object->checkValueInArray($propertyName, $propertyValue);
        self::assertSame($propertyValue, $actualValue);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Property "abc" is not defined to be validated
     */
    public function testCheckValueInArrayExceptionUndfinedProperty()
    {
        $this->object->checkValueInArray('abc', 'bcd');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage invalid value given for Property "Browser_Type": given value "bcd", allowed: ["Useragent Anonymizer","Browser","Offline Browser","Multimedia Player","Library","Feed Reader","Email Client","Bot\/Crawler","Application","unknown"]
     */
    public function testCheckValueInArrayExceptionWrongValue()
    {
        $this->object->checkValueInArray('Browser_Type', 'bcd');
    }
}
