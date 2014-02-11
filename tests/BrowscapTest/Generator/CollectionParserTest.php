<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\CollectionParser;

/**
 * Class CollectionParserTest
 *
 * @package BrowscapTest\Generator
 */
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
        ];
    }

    /**
     * @dataProvider outputPropertiesDataProvider
     */
    public function testIsOutputProperty($propertyName, $isExtra)
    {
        $actualValue = CollectionParser::isOutputProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }

    public function testGetDataCollectionReturnsSameDatacollectionAsInserted()
    {
        $mock = $this->getMock('\\Browscap\\Generator\\DataCollection', array(), array(), '', false);

        $parser = new CollectionParser();
        self::assertSame($parser, $parser->setDataCollection($mock));
        self::assertSame($mock, $parser->getDataCollection());
    }

    public function testParseDoesNothingOnEmptyDatacollection()
    {
        $mock = $this->getMock('\\Browscap\\Generator\\DataCollection', array('getDivisions'), array(), '', false);
        $mock->expects($this->once())
            ->method('getDivisions')
            ->will(self::returnValue(array()))
        ;

        $parser = new CollectionParser();
        self::assertSame($parser, $parser->setDataCollection($mock));

        $result = $parser->parse();
        self::assertInternalType('array', $result);
        self::assertCount(0, $result);
    }

    public function testParseSkipsEmptyOrInvalidDivisions()
    {
        $divisions = array(
            array('division' => 'Browscap Version'),
            array(
                'division' => 'DefaultProperties',
                'sortIndex' => 1,
                'lite' => true,
                'userAgents' => array(array('userAgent' => 'DefaultProperties', 'properties' => array('Browser' => 'test')))
            ),
            array(
                'division' => 'abc',
                'sortIndex' => 2,
                'lite' => false,
                'userAgents' => array(array('userAgent' => 'test', 'properties' => array('Parent' => 'DefaultProperties')))
            ),
            array(
                'division' => 'abc #MAJORVER#.#MINORVER#',
                'versions' => array('1.0'),
                'sortIndex' => 3,
                'userAgents' => array(array('userAgent' => 'test/1.*', 'properties' => array('Parent' => 'abc', 'Version' => '#MAJORVER#.#MINORVER#')))
            ),
            array(
                'division' => 'abc #MAJORVER#.#MINORVER#',
                'versions' => array('2.0'),
                'sortIndex' => 4,
                'userAgents' => array(array('userAgent' => 'test/1.*', 'properties' => array('Version' => '#MAJORVER#.#MINORVER#')))
            ),
        );

        $mock = $this->getMock('\\Browscap\\Generator\\DataCollection', array('getDivisions'), array(), '', false);
        $mock->expects($this->once())
            ->method('getDivisions')
            ->will(self::returnValue($divisions))
        ;

        $parser = new CollectionParser();
        self::assertSame($parser, $parser->setDataCollection($mock));

        $result = $parser->parse();
        self::assertInternalType('array', $result);

        $expected = array (
            'DefaultProperties' => array (
                'lite' => '1',
                'sortIndex' => '1',
                'division' => 'DefaultProperties',
                'Browser' => 'test',
                'Parents' => '',
            ),
            'test' => array (
                'lite' => '',
                'sortIndex' => '2',
                'division' => 'abc',
                'Parent' => 'DefaultProperties',
                'Browser' => 'test',
                'Parents' => 'DefaultProperties',
            ),
            'test/1.*' => array (
                'lite' => '',
                'sortIndex' => '3',
                'division' => 'abc 1.0',
                'Parent' => 'abc',
                'Version' => '1.0',
                'Parents' => 'abc',
                'MajorVer' => '1',
                'MinorVer' => '0',
            )
        );
        self::assertSame($expected, $result);
    }

    public function testParseParsesChildren()
    {
        $divisions = array(
            array('division' => 'Browscap Version'),
            array(
                'division' => 'DefaultProperties',
                'sortIndex' => 1,
                'lite' => true,
                'userAgents' => array(array('userAgent' => 'DefaultProperties', 'properties' => array('Browser' => 'test')))
            ),
            array(
                'division' => 'abc',
                'sortIndex' => 2,
                'lite' => false,
                'userAgents' => array(
                    array(
                        'userAgent' => 'test',
                        'properties' => array('Parent' => 'DefaultProperties'),
                        'children' => array(
                            array(
                                'match' => 'abc/#PLATFORM#*',
                                'platforms' => array('testOS')
                            ),
                            array(
                                'platforms' => array()
                            ),
                            array(
                                'match' => 'abc/1.0* (#PLATFORM#)',
                            )
                        )
                    )
                )
            ),
        );

        $platform = array(
            'match' => '*TestOS*',
            'properties' => array(
                'Platform' => 'TestOS'
            )
        );

        $mock = $this->getMock('\\Browscap\\Generator\\DataCollection', array('getDivisions', 'getPlatform'), array(), '', false);
        $mock->expects($this->once())
            ->method('getDivisions')
            ->will(self::returnValue($divisions))
        ;
        $mock->expects($this->once())
            ->method('getPlatform')
            ->will(self::returnValue($platform))
        ;

        $parser = new CollectionParser();
        self::assertSame($parser, $parser->setDataCollection($mock));

        $result = $parser->parse();
        self::assertInternalType('array', $result);

        $expected = array (
            'DefaultProperties' => array (
                'lite' => '1',
                'sortIndex' => '1',
                'division' => 'DefaultProperties',
                'Browser' => 'test',
                'Parents' => '',
            ),
            'test' => array (
                'lite' => '',
                'sortIndex' => '2',
                'division' => 'abc',
                'Parent' => 'DefaultProperties',
                'Browser' => 'test',
                'Parents' => 'DefaultProperties',
            ),
            'abc/*TestOS**' => array (
                'Parent' => 'test',
                'Platform' => 'TestOS',
                'lite' => '',
                'sortIndex' => '2',
                'division' => 'abc',
                'Browser' => 'test',
                'Parents' => 'DefaultProperties,test',
            ),
            'abc/1.0* (#PLATFORM#)' => array (
                'Parent' => 'test',
                'lite' => '',
                'sortIndex' => '2',
                'division' => 'abc',
                'Browser' => 'test',
                'Parents' => 'DefaultProperties,test',
            )
        );
        self::assertSame($expected, $result);
    }
}
