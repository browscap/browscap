<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\CollectionParser;

class CollectionParserTest extends \PHPUnit_Framework_TestCase
{
    public function propertyNameTypeDataProvider()
    {
        return [
            ['Comment', 'string'],
            ['Browser', 'string'],
            ['Platform', 'string'],
            ['Platform_Description', 'string'],
            ['Device_Name', 'string'],
            ['Device_Maker', 'string'],
            ['RenderingEngine_Name', 'string'],
            ['RenderingEngine_Description', 'string'],
            ['Parent', 'string'],
            ['Platform_Version', 'generic'],
            ['RenderingEngine_Version', 'generic'],
            ['Version', 'number'],
            ['MajorVer', 'number'],
            ['MinorVer', 'number'],
            ['CssVersion', 'number'],
            ['AolVersion', 'number'],
            ['Alpha', 'boolean'],
            ['Beta', 'boolean'],
            ['Win16', 'boolean'],
            ['Win32', 'boolean'],
            ['Win64', 'boolean'],
            ['Frames', 'boolean'],
            ['IFrames', 'boolean'],
            ['Tables', 'boolean'],
            ['Cookies', 'boolean'],
            ['BackgroundSounds', 'boolean'],
            ['JavaScript', 'boolean'],
            ['VBScript', 'boolean'],
            ['JavaApplets', 'boolean'],
            ['ActiveXControls', 'boolean'],
            ['isMobileDevice', 'boolean'],
            ['isSyndicationReader', 'boolean'],
            ['Crawler', 'boolean'],
        ];
    }

    /**
     * @dataProvider propertyNameTypeDataProvider
     */
    public function testGetPropertyType($propertyName, $expectedType)
    {
        $actualType = CollectionParser::getPropertyType($propertyName);
        self::assertSame($expectedType, $actualType, "Property {$propertyName} should be {$expectedType} (was {$actualType})");
    }

    public function testGetPropertyTypeThrowsExceptionIfPropertyNameNotMapped()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Property Foobar did not have a defined property type');
        CollectionParser::getPropertyType('Foobar');
    }

    public function testGetDataCollectionThrowsExceptionIfCollectionIsNotSet()
    {
        $this->setExpectedException('\LogicException', 'Data collection has not been set yet - call setDataCollection');
        $parser = new CollectionParser();
        $parser->getDataCollection();
    }

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
        ];
    }

    /**
     * @dataProvider extraPropertiesDataProvider
     */
    public function testIsExtraProperty($propertyName, $isExtra)
    {
        $actualValue = CollectionParser::isExtraProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }
}
