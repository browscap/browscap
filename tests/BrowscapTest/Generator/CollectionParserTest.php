<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\CollectionParser;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class CollectionParserTest
 *
 * @package BrowscapTest\Generator
 */
class CollectionParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    public function setUp()
    {
        $this->logger = new Logger('browscapTest', array(new NullHandler()));
    }

    public function propertyNameTypeDataProvider()
    {
        return [
            ['Comment', CollectionParser::TYPE_STRING],
            ['Browser', CollectionParser::TYPE_STRING],
            ['Platform', CollectionParser::TYPE_STRING],
            ['Platform_Description', CollectionParser::TYPE_STRING],
            ['Device_Name', CollectionParser::TYPE_STRING],
            ['Device_Maker', CollectionParser::TYPE_STRING],
            ['RenderingEngine_Name', CollectionParser::TYPE_STRING],
            ['RenderingEngine_Description', CollectionParser::TYPE_STRING],
            ['Parent', CollectionParser::TYPE_STRING],
            ['Platform_Version', CollectionParser::TYPE_GENERIC],
            ['RenderingEngine_Version', CollectionParser::TYPE_GENERIC],
            ['Version', CollectionParser::TYPE_NUMBER],
            ['MajorVer', CollectionParser::TYPE_NUMBER],
            ['MinorVer', CollectionParser::TYPE_NUMBER],
            ['CssVersion', CollectionParser::TYPE_NUMBER],
            ['AolVersion', CollectionParser::TYPE_NUMBER],
            ['Alpha', CollectionParser::TYPE_BOOLEAN],
            ['Beta', CollectionParser::TYPE_BOOLEAN],
            ['Win16', CollectionParser::TYPE_BOOLEAN],
            ['Win32', CollectionParser::TYPE_BOOLEAN],
            ['Win64', CollectionParser::TYPE_BOOLEAN],
            ['Frames', CollectionParser::TYPE_BOOLEAN],
            ['IFrames', CollectionParser::TYPE_BOOLEAN],
            ['Tables', CollectionParser::TYPE_BOOLEAN],
            ['Cookies', CollectionParser::TYPE_BOOLEAN],
            ['BackgroundSounds', CollectionParser::TYPE_BOOLEAN],
            ['JavaScript', CollectionParser::TYPE_BOOLEAN],
            ['VBScript', CollectionParser::TYPE_BOOLEAN],
            ['JavaApplets', CollectionParser::TYPE_BOOLEAN],
            ['ActiveXControls', CollectionParser::TYPE_BOOLEAN],
            ['isMobileDevice', CollectionParser::TYPE_BOOLEAN],
            ['isSyndicationReader', CollectionParser::TYPE_BOOLEAN],
            ['Crawler', CollectionParser::TYPE_BOOLEAN],
            ['Browser_Type', CollectionParser::TYPE_IN_ARRAY],
            ['Device_Type', CollectionParser::TYPE_IN_ARRAY],
            ['Device_Pointing_Method', CollectionParser::TYPE_IN_ARRAY],
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
        $actualValue = CollectionParser::isOutputProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }

    public function checkValueInArrayProvider()
    {
        return [
            ['Browser_Type', 'Browser'],
            ['Device_Type', 'Tablet'],
            ['Device_Pointing_Method', 'touchscreen'],
        ];
    }

    /**
     * @dataProvider checkValueInArrayProvider
     */
    public function testCheckValueInArray($propertyName, $propertyValue)
    {
        $actualValue = CollectionParser::checkValueInArray($propertyName, $propertyValue);
        self::assertSame($propertyValue, $actualValue);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Property "abc" is not defined to be validated
     */
    public function testCheckValueInArrayExceptionUndfinedProperty()
    {
        CollectionParser::checkValueInArray('abc', 'bcd');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage invalid value given for Property "Browser_Type": given value "bcd", allowed: ["Useragent Anonymizer","Browser","Offline Browser","Multimedia Player","Library","Feed Reader","Email Client","Bot\/Crawler","Application","unknown"]
     */
    public function testCheckValueInArrayExceptionWrongValue()
    {
        CollectionParser::checkValueInArray('Browser_Type', 'bcd');
    }

    public function testGetDataCollectionReturnsSameDatacollectionAsInserted()
    {
        $mock = $this->getMock('\\Browscap\\Generator\\DataCollection', array(), array(), '', false);

        $parser = new CollectionParser();
        $parser->setLogger($this->logger);
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
        $parser->setLogger($this->logger);
        self::assertSame($parser, $parser->setDataCollection($mock));

        $result = $parser->parse();
        self::assertInternalType('array', $result);
        self::assertCount(0, $result);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Parent "abc" not found for key "test/1.*"
     */
    public function testParseSkipsEmptyOrInvalidDivisions()
    {
        $divisions = array(
            array('division' => 'Browscap Version'),
            array(
                'division' => 'DefaultProperties',
                'sortIndex' => 1,
                'lite' => true,
                'userAgents' => array(array('userAgent' => 'DefaultProperties', 'properties' => array('Browser' => 'test', 'Version' => '0')))
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
                'userAgents' => array(array('userAgent' => 'test/2.*', 'properties' => array('Version' => '#MAJORVER#.#MINORVER#')))
            ),
        );

        $mock = $this->getMock('\\Browscap\\Generator\\DataCollection', array('getDivisions'), array(), '', false);
        $mock->expects($this->once())
            ->method('getDivisions')
            ->will(self::returnValue($divisions))
        ;

        $parser = new CollectionParser();
        $parser->setLogger($this->logger);
        self::assertSame($parser, $parser->setDataCollection($mock));

        $parser->parse();
    }

    public function testParseParsesChildren()
    {
        $divisions = array(
            array('division' => 'Browscap Version'),
            array(
                'division' => 'DefaultProperties',
                'sortIndex' => 1,
                'lite' => true,
                'userAgents' => array(
                    array(
                        'userAgent' => 'DefaultProperties',
                        'properties' => array('Browser' => 'test', 'Version' => '1.0')
                    )
                )
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
                                'match' => 'abc/* (#PLATFORM#)',
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

        $mock = $this->getMock(
            '\\Browscap\\Generator\\DataCollection', array('getDivisions', 'getPlatform'), array(), '', false
        );
        $mock->expects($this->once())
            ->method('getDivisions')
            ->will(self::returnValue($divisions))
        ;
        $mock->expects($this->once())
            ->method('getPlatform')
            ->will(self::returnValue($platform))
        ;

        $parser = new CollectionParser();
        $parser->setLogger($this->logger);
        self::assertSame($parser, $parser->setDataCollection($mock));

        $result = $parser->parse();
        self::assertInternalType('array', $result);

        $expected = array (
            'DefaultProperties' => array (
                'lite' => '1',
                'sortIndex' => '1',
                'division' => 'DefaultProperties',
                'Browser' => 'test',
                'Version' => '1.0',
                'Parents' => '',
                'MajorVer' => '1',
                'MinorVer' => '0',
            ),
            'test' => array (
                'lite' => '',
                'sortIndex' => '2',
                'division' => 'abc',
                'Parent' => 'DefaultProperties',
                'Browser' => 'test',
                'Version' => '1.0',
                'Parents' => 'DefaultProperties',
                'MajorVer' => '1',
                'MinorVer' => '0',
            ),
            'abc/*TestOS**' => array (
                'Parent' => 'test',
                'Platform' => 'TestOS',
                'lite' => '',
                'sortIndex' => '2',
                'division' => 'abc',
                'Browser' => 'test',
                'Version' => '1.0',
                'Parents' => 'DefaultProperties,test',
                'MajorVer' => '1',
                'MinorVer' => '0',
            ),
            'abc/1.0* (#PLATFORM#)' => array (
                'Parent' => 'test',
                'lite' => '',
                'sortIndex' => '2',
                'division' => 'abc',
                'Browser' => 'test',
                'Version' => '1.0',
                'Parents' => 'DefaultProperties,test',
                'MajorVer' => '1',
                'MinorVer' => '0',
            )
        );
        self::assertSame($expected, $result);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage each entry of the children property requires an "match" entry for key "test"
     */
    public function testParseInvalidChildren()
    {
        $divisions = array(
            array('division' => 'Browscap Version'),
            array(
                'division' => 'DefaultProperties',
                'sortIndex' => 1,
                'lite' => true,
                'userAgents' => array(
                    array(
                        'userAgent' => 'DefaultProperties',
                        'properties' => array('Browser' => 'test', 'Version' => '1.0')
                    )
                )
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

        $mock = $this->getMock(
            '\\Browscap\\Generator\\DataCollection', array('getDivisions', 'getPlatform'), array(), '', false
        );
        $mock->expects($this->once())
            ->method('getDivisions')
            ->will(self::returnValue($divisions))
        ;
        $mock->expects($this->once())
            ->method('getPlatform')
            ->will(self::returnValue($platform))
        ;

        $parser = new CollectionParser();
        $parser->setLogger($this->logger);
        self::assertSame($parser, $parser->setDataCollection($mock));

        $parser->parse();
    }
}
