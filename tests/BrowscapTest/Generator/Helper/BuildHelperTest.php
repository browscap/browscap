<?php
declare(strict_types = 1);
namespace BrowscapTest\Generator\Helper;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Data\DuplicateDataException;
use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Data\UserAgent;
use Browscap\Generator\Helper\BuildHelper;
use Browscap\Writer\WriterCollection;
use DateTimeImmutable;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class BuildHelperTest extends TestCase
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @throws \ReflectionException
     */
    public function setUp() : void
    {
        $this->logger = $this->createMock(Logger::class);
    }

    /**
     * tests running a build
     *
     * @throws \Exception
     * @throws \Assert\AssertionFailedException
     */
    public function testRun() : void
    {
        $writerCollection = $this->getMockBuilder(WriterCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([
                    'fileStart',
                    'renderHeader',
                    'renderAllDivisionsHeader',
                    'renderDivisionFooter',
                    'renderSectionHeader',
                    'renderSectionBody',
                    'fileEnd',
                ])
            ->getMock();

        $writerCollection->expects(self::once())
            ->method('fileStart')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('fileEnd')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderAllDivisionsHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderDivisionFooter')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionBody')
            ->with(self::callback(static function (array $props) {
                // Be sure that PatternId key is removed
                return !array_key_exists('PatternId', $props);
            }))
            ->will(self::returnSelf());

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(self::exactly(2))
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::exactly(2))
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Comment' => 'Default Browser',
                'Browser' => 'Default Browser',
                'Browser_Type' => 'unknown',
                'Browser_Bits' => 0,
                'Browser_Maker' => 'unknown',
                'Browser_Modus' => 'unknown',
                'Version' => '0.0',
                'MajorVer' => '0',
                'MinorVer' => '0',
                'Platform' => 'unknown',
                'Platform_Version' => 'unknown',
                'Platform_Description' => 'unknown',
                'Platform_Bits' => 0,
                'Platform_Maker' => 'unknown',
                'Alpha' => false,
                'Beta' => false,
                'Win16' => false,
                'Win32' => false,
                'Win64' => false,
                'Frames' => false,
                'IFrames' => false,
                'Tables' => false,
                'Cookies' => false,
                'BackgroundSounds' => false,
                'JavaScript' => false,
                'VBScript' => false,
                'JavaApplets' => false,
                'ActiveXControls' => false,
                'isMobileDevice' => false,
                'isTablet' => false,
                'isSyndicationReader' => false,
                'Crawler' => false,
                'isFake' => false,
                'isAnonymized' => false,
                'isModified' => false,
                'CssVersion' => 0,
                'AolVersion' => 0,
                'Device_Name' => 'unknown',
                'Device_Maker' => 'unknown',
                'Device_Type' => 'unknown',
                'Device_Pointing_Method' => 'unknown',
                'Device_Code_Name' => 'unknown',
                'Device_Brand_Name' => 'unknown',
                'RenderingEngine_Name' => 'unknown',
                'RenderingEngine_Version' => 'unknown',
                'RenderingEngine_Description' => 'unknown',
                'RenderingEngine_Maker' => 'unknown',
                'PatternId' => 'resources/core/default-browser.json::u0',
            ]));

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(self::exactly(2))
            ->method('getUserAgent')
            ->will(self::returnValue('Defaultproperties'));

        $defaultProperties
            ->expects(self::exactly(3))
            ->method('getProperties')
            ->will(self::returnValue(
                [
                     'Comment' => 'Defaultproperties',
                     'Browser' => 'Defaultproperties',
                     'Browser_Type' => 'unknown',
                     'Browser_Bits' => 0,
                     'Browser_Maker' => 'unknown',
                     'Browser_Modus' => 'unknown',
                     'Version' => '0.0',
                     'MajorVer' => '0',
                     'MinorVer' => '0',
                     'Platform' => 'unknown',
                     'Platform_Version' => 'unknown',
                     'Platform_Description' => 'unknown',
                     'Platform_Bits' => 0,
                     'Platform_Maker' => 'unknown',
                     'Alpha' => false,
                     'Beta' => false,
                     'Win16' => false,
                     'Win32' => false,
                     'Win64' => false,
                     'Frames' => false,
                     'IFrames' => false,
                     'Tables' => false,
                     'Cookies' => false,
                     'BackgroundSounds' => false,
                     'JavaScript' => false,
                     'VBScript' => false,
                     'JavaApplets' => false,
                     'ActiveXControls' => false,
                     'isMobileDevice' => false,
                     'isTablet' => false,
                     'isSyndicationReader' => false,
                     'Crawler' => false,
                     'isFake' => false,
                     'isAnonymized' => false,
                     'isModified' => false,
                     'CssVersion' => 0,
                     'AolVersion' => 0,
                     'Device_Name' => 'unknown',
                     'Device_Maker' => 'unknown',
                     'Device_Type' => 'unknown',
                     'Device_Pointing_Method' => 'unknown',
                     'Device_Code_Name' => 'unknown',
                     'Device_Brand_Name' => 'unknown',
                     'RenderingEngine_Name' => 'unknown',
                     'RenderingEngine_Version' => 'unknown',
                     'RenderingEngine_Description' => 'unknown',
                     'RenderingEngine_Maker' => 'unknown',
                     'PatternId' => 'resources/core/default-browser.json::u0',
                 ]
            ));

        $division
            ->expects(self::exactly(2))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => $useragent,
                    ]
                )
            );
        $division
            ->expects(self::once())
            ->method('getVersions')
            ->will(self::returnValue(['2']));

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(self::exactly(2))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => $defaultProperties,
                    ]
                )
            );

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties', 'getDefaultBrowser', 'getDivisions'])
            ->getMock();

        $collection
            ->expects(self::exactly(2))
            ->method('getDefaultProperties')
            ->will(self::returnValue($coreDivision));
        $collection
            ->expects(self::once())
            ->method('getDefaultBrowser')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDivisions')
            ->will(self::returnValue([$division]));

        $collectionCreator = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $collectionCreator->expects(self::once())
            ->method('createDataCollection')
            ->will(self::returnValue($collection));

        BuildHelper::run('test', new DateTimeImmutable(), '.', $this->logger, $writerCollection, $collectionCreator);
    }

    /**
     * tests running a build
     *
     * @throws \Exception
     * @throws \Assert\AssertionFailedException
     */
    public function testRunDuplicateDivision() : void
    {
        $writerCollection = $this->getMockBuilder(WriterCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'fileStart',
                'renderHeader',
                'renderAllDivisionsHeader',
                'renderDivisionFooter',
                'renderSectionHeader',
                'renderSectionBody',
                'fileEnd',
            ])
            ->getMock();

        $writerCollection->expects(self::once())
            ->method('fileStart')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('fileEnd')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderAllDivisionsHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderDivisionFooter')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionBody')
            ->with(self::callback(static function (array $props) {
                // Be sure that PatternId key is removed
                return !array_key_exists('PatternId', $props);
            }))
            ->will(self::returnSelf());

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(self::exactly(3))
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::exactly(3))
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Comment' => 'Default Browser',
                'Browser' => 'Default Browser',
                'Browser_Type' => 'unknown',
                'Browser_Bits' => 0,
                'Browser_Maker' => 'unknown',
                'Browser_Modus' => 'unknown',
                'Version' => '0.0',
                'MajorVer' => '0',
                'MinorVer' => '0',
                'Platform' => 'unknown',
                'Platform_Version' => 'unknown',
                'Platform_Description' => 'unknown',
                'Platform_Bits' => 0,
                'Platform_Maker' => 'unknown',
                'Alpha' => false,
                'Beta' => false,
                'Win16' => false,
                'Win32' => false,
                'Win64' => false,
                'Frames' => false,
                'IFrames' => false,
                'Tables' => false,
                'Cookies' => false,
                'BackgroundSounds' => false,
                'JavaScript' => false,
                'VBScript' => false,
                'JavaApplets' => false,
                'ActiveXControls' => false,
                'isMobileDevice' => false,
                'isTablet' => false,
                'isSyndicationReader' => false,
                'Crawler' => false,
                'isFake' => false,
                'isAnonymized' => false,
                'isModified' => false,
                'CssVersion' => 0,
                'AolVersion' => 0,
                'Device_Name' => 'unknown',
                'Device_Maker' => 'unknown',
                'Device_Type' => 'unknown',
                'Device_Pointing_Method' => 'unknown',
                'Device_Code_Name' => 'unknown',
                'Device_Brand_Name' => 'unknown',
                'RenderingEngine_Name' => 'unknown',
                'RenderingEngine_Version' => 'unknown',
                'RenderingEngine_Description' => 'unknown',
                'RenderingEngine_Maker' => 'unknown',
                'PatternId' => 'resources/core/default-browser.json::u0',
            ]));

        $division
            ->expects(self::exactly(3))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => $useragent,
                    ]
                )
            );
        $division
            ->expects(self::exactly(2))
            ->method('getVersions')
            ->will(self::returnValue(['2']));

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(self::exactly(3))
            ->method('getUserAgent')
            ->will(self::returnValue('Defaultproperties'));

        $defaultProperties
            ->expects(self::exactly(5))
            ->method('getProperties')
            ->will(
                self::returnValue(
                    [
                     'Comment' => 'Defaultproperties',
                     'Browser' => 'Defaultproperties',
                     'Browser_Type' => 'unknown',
                     'Browser_Bits' => 0,
                     'Browser_Maker' => 'unknown',
                     'Browser_Modus' => 'unknown',
                     'Version' => '0.0',
                     'MajorVer' => '0',
                     'MinorVer' => '0',
                     'Platform' => 'unknown',
                     'Platform_Version' => 'unknown',
                     'Platform_Description' => 'unknown',
                     'Platform_Bits' => 0,
                     'Platform_Maker' => 'unknown',
                     'Alpha' => false,
                     'Beta' => false,
                     'Win16' => false,
                     'Win32' => false,
                     'Win64' => false,
                     'Frames' => false,
                     'IFrames' => false,
                     'Tables' => false,
                     'Cookies' => false,
                     'BackgroundSounds' => false,
                     'JavaScript' => false,
                     'VBScript' => false,
                     'JavaApplets' => false,
                     'ActiveXControls' => false,
                     'isMobileDevice' => false,
                     'isTablet' => false,
                     'isSyndicationReader' => false,
                     'Crawler' => false,
                     'isFake' => false,
                     'isAnonymized' => false,
                     'isModified' => false,
                     'CssVersion' => 0,
                     'AolVersion' => 0,
                     'Device_Name' => 'unknown',
                     'Device_Maker' => 'unknown',
                     'Device_Type' => 'unknown',
                     'Device_Pointing_Method' => 'unknown',
                     'Device_Code_Name' => 'unknown',
                     'Device_Brand_Name' => 'unknown',
                     'RenderingEngine_Name' => 'unknown',
                     'RenderingEngine_Version' => 'unknown',
                     'RenderingEngine_Description' => 'unknown',
                     'RenderingEngine_Maker' => 'unknown',
                     'PatternId' => 'resources/core/default-browser.json::u0',
                 ]
            )
            );

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(self::exactly(3))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => $defaultProperties,
                    ]
                )
            );

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties', 'getDefaultBrowser', 'getDivisions'])
            ->getMock();

        $collection
            ->expects(self::exactly(3))
            ->method('getDefaultProperties')
            ->will(self::returnValue($coreDivision));
        $collection
            ->expects(self::once())
            ->method('getDefaultBrowser')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDivisions')
            ->will(self::returnValue([$division, $division]));

        $collectionCreator = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $collectionCreator->expects(self::once())
            ->method('createDataCollection')
            ->will(self::returnValue($collection));

        BuildHelper::run('test', new DateTimeImmutable(), '.', $this->logger, $writerCollection, $collectionCreator);
    }

    /**
     * tests running a build with pattern id collection enabled
     *
     * @throws \Exception
     * @throws \Assert\AssertionFailedException
     */
    public function testRunWithPatternIdCollectionEnabled() : void
    {
        $writerCollection = $this->getMockBuilder(WriterCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([
                    'fileStart',
                    'renderHeader',
                    'renderAllDivisionsHeader',
                    'renderDivisionFooter',
                    'renderSectionHeader',
                    'renderSectionBody',
                    'fileEnd',
                ])
            ->getMock();

        $writerCollection->expects(self::once())
            ->method('fileStart')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('fileEnd')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderAllDivisionsHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderDivisionFooter')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionBody')
            ->with(self::callback(static function (array $props) {
                // Be sure that PatternId key is present
                return array_key_exists('PatternId', $props);
            }))
            ->will(self::returnSelf());

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(self::exactly(2))
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::exactly(2))
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Comment' => 'Default Browser',
                'Browser' => 'Default Browser',
                'Browser_Type' => 'unknown',
                'Browser_Bits' => 0,
                'Browser_Maker' => 'unknown',
                'Browser_Modus' => 'unknown',
                'Version' => '0.0',
                'MajorVer' => '0',
                'MinorVer' => '0',
                'Platform' => 'unknown',
                'Platform_Version' => 'unknown',
                'Platform_Description' => 'unknown',
                'Platform_Bits' => 0,
                'Platform_Maker' => 'unknown',
                'Alpha' => false,
                'Beta' => false,
                'Win16' => false,
                'Win32' => false,
                'Win64' => false,
                'Frames' => false,
                'IFrames' => false,
                'Tables' => false,
                'Cookies' => false,
                'BackgroundSounds' => false,
                'JavaScript' => false,
                'VBScript' => false,
                'JavaApplets' => false,
                'ActiveXControls' => false,
                'isMobileDevice' => false,
                'isTablet' => false,
                'isSyndicationReader' => false,
                'Crawler' => false,
                'isFake' => false,
                'isAnonymized' => false,
                'isModified' => false,
                'CssVersion' => 0,
                'AolVersion' => 0,
                'Device_Name' => 'unknown',
                'Device_Maker' => 'unknown',
                'Device_Type' => 'unknown',
                'Device_Pointing_Method' => 'unknown',
                'Device_Code_Name' => 'unknown',
                'Device_Brand_Name' => 'unknown',
                'RenderingEngine_Name' => 'unknown',
                'RenderingEngine_Version' => 'unknown',
                'RenderingEngine_Description' => 'unknown',
                'RenderingEngine_Maker' => 'unknown',
                'PatternId' => 'resources/core/default-browser.json::u0',
            ]));

        $division
            ->expects(self::exactly(2))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => $useragent,
                    ]
                )
            );
        $division
            ->expects(self::once())
            ->method('getVersions')
            ->will(self::returnValue(['2']));

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(self::exactly(2))
            ->method('getUserAgent')
            ->will(self::returnValue('Defaultproperties'));

        $defaultProperties
            ->expects(self::exactly(3))
            ->method('getProperties')
            ->will(self::returnValue(
                [
                     'Comment' => 'Defaultproperties',
                     'Browser' => 'Defaultproperties',
                     'Browser_Type' => 'unknown',
                     'Browser_Bits' => 0,
                     'Browser_Maker' => 'unknown',
                     'Browser_Modus' => 'unknown',
                     'Version' => '0.0',
                     'MajorVer' => '0',
                     'MinorVer' => '0',
                     'Platform' => 'unknown',
                     'Platform_Version' => 'unknown',
                     'Platform_Description' => 'unknown',
                     'Platform_Bits' => 0,
                     'Platform_Maker' => 'unknown',
                     'Alpha' => false,
                     'Beta' => false,
                     'Win16' => false,
                     'Win32' => false,
                     'Win64' => false,
                     'Frames' => false,
                     'IFrames' => false,
                     'Tables' => false,
                     'Cookies' => false,
                     'BackgroundSounds' => false,
                     'JavaScript' => false,
                     'VBScript' => false,
                     'JavaApplets' => false,
                     'ActiveXControls' => false,
                     'isMobileDevice' => false,
                     'isTablet' => false,
                     'isSyndicationReader' => false,
                     'Crawler' => false,
                     'isFake' => false,
                     'isAnonymized' => false,
                     'isModified' => false,
                     'CssVersion' => 0,
                     'AolVersion' => 0,
                     'Device_Name' => 'unknown',
                     'Device_Maker' => 'unknown',
                     'Device_Type' => 'unknown',
                     'Device_Pointing_Method' => 'unknown',
                     'Device_Code_Name' => 'unknown',
                     'Device_Brand_Name' => 'unknown',
                     'RenderingEngine_Name' => 'unknown',
                     'RenderingEngine_Version' => 'unknown',
                     'RenderingEngine_Description' => 'unknown',
                     'RenderingEngine_Maker' => 'unknown',
                     'PatternId' => 'resources/core/default-browser.json::u0',
                 ]
            ));

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(self::exactly(2))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => $defaultProperties,
                    ]
                )
            );

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties', 'getDefaultBrowser', 'getDivisions'])
            ->getMock();

        $collection
            ->expects(self::exactly(2))
            ->method('getDefaultProperties')
            ->will(self::returnValue($coreDivision));
        $collection
            ->expects(self::once())
            ->method('getDefaultBrowser')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDivisions')
            ->will(self::returnValue([$division]));

        $collectionCreator = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $collectionCreator->expects(self::once())
            ->method('createDataCollection')
            ->will(self::returnValue($collection));

        BuildHelper::run('test', new DateTimeImmutable(), '.', $this->logger, $writerCollection, $collectionCreator, true);
    }

    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function testDuplicateUseragents() : void
    {
        $writerCollection = $this->getMockBuilder(WriterCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                 'fileStart',
                 'renderHeader',
                 'renderAllDivisionsHeader',
                 'renderDivisionFooter',
                 'renderSectionHeader',
                 'renderSectionBody',
                 'fileEnd',
                ]
            )
            ->getMock();

        $writerCollection->expects(self::once())
            ->method('fileStart')
            ->will(self::returnSelf());
        $writerCollection->expects(self::never())
            ->method('fileEnd')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderAllDivisionsHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderDivisionFooter')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionBody')
            ->will(self::returnSelf());

        $dataCollectionFactory = new DataCollectionFactory($this->logger, new DateTimeImmutable());

        $resourceFolder = realpath(__DIR__ . '/../../../fixtures/duplicate-useragent-entries');

        $this->expectException(DuplicateDataException::class);
        $this->expectExceptionMessage(sprintf('tried to add section "Mozilla/5.0 (Build/*) applewebkit* (*khtml*like*gecko*) Version/* Chrome/* Safari/* Mb2345Browser/#MAJORVER#.#MINORVER#*" for division "2345 Browser #MAJORVER#.#MINORVER#" in file "%s/user-agents/test1.json", but this was already added before', $resourceFolder));

        BuildHelper::run(
            'test',
            new DateTimeImmutable(),
            (string) $resourceFolder,
            $this->logger,
            $writerCollection,
            $dataCollectionFactory,
            false
        );
    }
}
