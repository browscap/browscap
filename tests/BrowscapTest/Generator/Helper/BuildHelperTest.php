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
    protected function setUp() : void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
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

        $writerCollection->expects(static::once())
            ->method('fileStart')
            ->willReturnSelf();
        $writerCollection->expects(static::once())
            ->method('fileEnd')
            ->willReturnSelf();
        $writerCollection->expects(static::once())
            ->method('renderHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::once())
            ->method('renderAllDivisionsHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderDivisionFooter')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderSectionHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderSectionBody')
            ->with(static::callback(static function (array $props) {
                // Be sure that PatternId key is removed
                return !array_key_exists('PatternId', $props);
            }))
            ->willReturnSelf();

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(static::exactly(2))
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn([
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
            ]);

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::exactly(2))
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $defaultProperties
            ->expects(static::exactly(3))
            ->method('getProperties')
            ->willReturn(
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
            );

        $division
            ->expects(static::exactly(2))
            ->method('getUserAgents')
            ->willReturn(
                [
                    0 => $useragent,
                ]
            );
        $division
            ->expects(static::once())
            ->method('getVersions')
            ->willReturn(['2']);

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::exactly(2))
            ->method('getUserAgents')
            ->willReturn(
                [
                    0 => $defaultProperties,
                ]
            );

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties', 'getDefaultBrowser', 'getDivisions'])
            ->getMock();

        $collection
            ->expects(static::exactly(2))
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::once())
            ->method('getDefaultBrowser')
            ->willReturn($division);
        $collection
            ->expects(static::once())
            ->method('getDivisions')
            ->willReturn([$division]);

        $collectionCreator = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $collectionCreator->expects(static::once())
            ->method('createDataCollection')
            ->willReturn($collection);

        /* @var WriterCollection $writerCollection */
        /* @var DataCollectionFactory $collectionCreator */
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

        $writerCollection->expects(static::once())
            ->method('fileStart')
            ->willReturnSelf();
        $writerCollection->expects(static::once())
            ->method('fileEnd')
            ->willReturnSelf();
        $writerCollection->expects(static::once())
            ->method('renderHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::once())
            ->method('renderAllDivisionsHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderDivisionFooter')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderSectionHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderSectionBody')
            ->with(static::callback(static function (array $props) {
                // Be sure that PatternId key is removed
                return !array_key_exists('PatternId', $props);
            }))
            ->willReturnSelf();

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(static::exactly(3))
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::exactly(3))
            ->method('getProperties')
            ->willReturn([
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
            ]);

        $division
            ->expects(static::exactly(3))
            ->method('getUserAgents')
            ->willReturn(
                [
                    0 => $useragent,
                ]
            );
        $division
            ->expects(static::exactly(2))
            ->method('getVersions')
            ->willReturn(['2']);

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::exactly(3))
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $defaultProperties
            ->expects(static::exactly(5))
            ->method('getProperties')
            ->willReturn(
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
            );

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::exactly(3))
            ->method('getUserAgents')
            ->willReturn(
                [
                    0 => $defaultProperties,
                ]
            );

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties', 'getDefaultBrowser', 'getDivisions'])
            ->getMock();

        $collection
            ->expects(static::exactly(3))
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::once())
            ->method('getDefaultBrowser')
            ->willReturn($division);
        $collection
            ->expects(static::once())
            ->method('getDivisions')
            ->willReturn([$division, $division]);

        $collectionCreator = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $collectionCreator->expects(static::once())
            ->method('createDataCollection')
            ->willReturn($collection);

        /* @var WriterCollection $writerCollection */
        /* @var DataCollectionFactory $collectionCreator */
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

        $writerCollection->expects(static::once())
            ->method('fileStart')
            ->willReturnSelf();
        $writerCollection->expects(static::once())
            ->method('fileEnd')
            ->willReturnSelf();
        $writerCollection->expects(static::once())
            ->method('renderHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::once())
            ->method('renderAllDivisionsHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderDivisionFooter')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderSectionHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderSectionBody')
            ->with(static::callback(static function (array $props) {
                // Be sure that PatternId key is present
                return array_key_exists('PatternId', $props);
            }))
            ->willReturnSelf();

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(static::exactly(2))
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn([
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
            ]);

        $division
            ->expects(static::exactly(2))
            ->method('getUserAgents')
            ->willReturn(
                [
                    0 => $useragent,
                ]
            );
        $division
            ->expects(static::once())
            ->method('getVersions')
            ->willReturn(['2']);

        $defaultProperties = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $defaultProperties
            ->expects(static::exactly(2))
            ->method('getUserAgent')
            ->willReturn('Defaultproperties');

        $defaultProperties
            ->expects(static::exactly(3))
            ->method('getProperties')
            ->willReturn(
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
            );

        $coreDivision = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $coreDivision
            ->expects(static::exactly(2))
            ->method('getUserAgents')
            ->willReturn(
                [
                    0 => $defaultProperties,
                ]
            );

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties', 'getDefaultBrowser', 'getDivisions'])
            ->getMock();

        $collection
            ->expects(static::exactly(2))
            ->method('getDefaultProperties')
            ->willReturn($coreDivision);
        $collection
            ->expects(static::once())
            ->method('getDefaultBrowser')
            ->willReturn($division);
        $collection
            ->expects(static::once())
            ->method('getDivisions')
            ->willReturn([$division]);

        $collectionCreator = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $collectionCreator->expects(static::once())
            ->method('createDataCollection')
            ->willReturn($collection);

        /* @var WriterCollection $writerCollection */
        /* @var DataCollectionFactory $collectionCreator */
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

        $writerCollection->expects(static::once())
            ->method('fileStart')
            ->willReturnSelf();
        $writerCollection->expects(static::never())
            ->method('fileEnd')
            ->willReturnSelf();
        $writerCollection->expects(static::once())
            ->method('renderHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::once())
            ->method('renderAllDivisionsHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderDivisionFooter')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderSectionHeader')
            ->willReturnSelf();
        $writerCollection->expects(static::any())
            ->method('renderSectionBody')
            ->willReturnSelf();

        $dataCollectionFactory = new DataCollectionFactory($this->logger);

        /** @var string $resourceFolder */
        $resourceFolder = realpath(__DIR__ . '/../../../fixtures/duplicate-useragent-entries');
        $file           = realpath($resourceFolder . '/user-agents/test1.json');

        $this->expectException(DuplicateDataException::class);
        $this->expectExceptionMessage(sprintf('tried to add section "Mozilla/5.0 (Build/*) applewebkit* (*khtml*like*gecko*) Version/* Chrome/* Safari/* Mb2345Browser/#MAJORVER#.#MINORVER#*" for division "2345 Browser #MAJORVER#.#MINORVER#" in file "%s", but this was already added before', $file));

        /* @var WriterCollection $writerCollection */
        BuildHelper::run(
            'test',
            new DateTimeImmutable(),
            $resourceFolder,
            $this->logger,
            $writerCollection,
            $dataCollectionFactory,
            false
        );
    }
}
