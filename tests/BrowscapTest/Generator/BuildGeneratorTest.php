<?php
declare(strict_types = 1);
namespace BrowscapTest\Generator;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Data\UserAgent;
use Browscap\Generator\BuildGenerator;
use Browscap\Generator\DirectoryMissingException;
use Browscap\Generator\NotADirectoryException;
use Browscap\Writer\WriterCollection;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class BuildGeneratorTest extends TestCase
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
     * tests failing the build if the build dir does not exist
     *
     * @throws \ReflectionException
     */
    public function testConstructFailsIfTheDirDoesNotExsist() : void
    {
        $this->expectException(DirectoryMissingException::class);
        $this->expectExceptionMessage('The directory "/dar" does not exist, or we cannot access it');

        $writerCollection      = $this->createMock(WriterCollection::class);
        $dataCollectionFactory = $this->createMock(DataCollectionFactory::class);

        /* @var WriterCollection $writerCollection */
        /* @var DataCollectionFactory $dataCollectionFactory */
        new BuildGenerator('/dar', '', $this->logger, $writerCollection, $dataCollectionFactory);
    }

    /**
     * tests failing the build if no build dir is a file
     *
     * @throws \ReflectionException
     */
    public function testConstructFailsIfTheDirIsNotAnDirectory() : void
    {
        $this->expectException(NotADirectoryException::class);
        $this->expectExceptionMessage('The path "' . __FILE__ . '" did not resolve to a directory');

        $writerCollection      = $this->createMock(WriterCollection::class);
        $dataCollectionFactory = $this->createMock(DataCollectionFactory::class);

        /* @var WriterCollection $writerCollection */
        /* @var DataCollectionFactory $dataCollectionFactory */
        new BuildGenerator(__FILE__, '', $this->logger, $writerCollection, $dataCollectionFactory);
    }

    /**
     * tests running a build
     *
     * @throws \Exception
     * @throws \Assert\AssertionFailedException
     */
    public function testBuild() : void
    {
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
                'Parent' => 'DefaultProperties',
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

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

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
            ->willReturn('DefaultProperties');

        $defaultProperties
            ->expects(static::exactly(3))
            ->method('getProperties')
            ->willReturn([
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
            ]);

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

        $dataCollectionFactory = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $dataCollectionFactory
            ->expects(static::any())
            ->method('createDataCollection')
            ->willReturn($collection);

        $writerCollection = $this->getMockBuilder(WriterCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'fileStart',
                'renderHeader',
                'renderAllDivisionsHeader',
                'renderSectionHeader',
                'renderSectionBody',
                'fileEnd',
            ])
            ->getMock();

        $writerCollection
            ->expects(static::once())
            ->method('fileStart')
            ->willReturnSelf();
        $writerCollection
            ->expects(static::once())
            ->method('renderHeader')
            ->willReturnSelf();
        $writerCollection
            ->expects(static::once())
            ->method('renderAllDivisionsHeader')
            ->willReturnSelf();
        $writerCollection
            ->expects(static::any())
            ->method('renderSectionHeader')
            ->willReturnSelf();
        $writerCollection
            ->expects(static::any())
            ->method('renderSectionBody')
            ->willReturnSelf();
        $writerCollection
            ->expects(static::once())
            ->method('fileEnd')
            ->willReturnSelf();

        /** @var WriterCollection $writerCollection */
        /** @var DataCollectionFactory $dataCollectionFactory */
        $generator = new BuildGenerator('.', '.', $this->logger, $writerCollection, $dataCollectionFactory);
        $generator->setCollectPatternIds(false);

        $generator->run('test', new DateTimeImmutable(), false);
    }

    /**
     * tests running a build without generating a zip file
     *
     * @throws \Exception
     * @throws \Assert\AssertionFailedException
     */
    public function testBuildWithoutZip() : void
    {
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
                'Parent' => 'DefaultProperties',
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

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

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
            ->willReturn('DefaultProperties');

        $defaultProperties
            ->expects(static::exactly(3))
            ->method('getProperties')
            ->willReturn([
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
            ]);

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

        $dataCollectionFactory = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $dataCollectionFactory
            ->expects(static::any())
            ->method('createDataCollection')
            ->willReturn($collection);

        $writerCollection = $this->getMockBuilder(WriterCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'fileStart',
                'renderHeader',
                'renderAllDivisionsHeader',
                'renderSectionHeader',
                'renderSectionBody',
                'fileEnd',
            ])
            ->getMock();

        $writerCollection
            ->expects(static::once())
            ->method('fileStart')
            ->willReturnSelf();
        $writerCollection
            ->expects(static::once())
            ->method('renderHeader')
            ->willReturnSelf();
        $writerCollection
            ->expects(static::once())
            ->method('renderAllDivisionsHeader')
            ->willReturnSelf();
        $writerCollection
            ->expects(static::any())
            ->method('renderSectionHeader')
            ->willReturnSelf();
        $writerCollection
            ->expects(static::any())
            ->method('renderSectionBody')
            ->willReturnSelf();
        $writerCollection
            ->expects(static::once())
            ->method('fileEnd')
            ->willReturnSelf();

        /** @var WriterCollection $writerCollection */
        /** @var DataCollectionFactory $dataCollectionFactory */
        $generator = new BuildGenerator('.', '.', $this->logger, $writerCollection, $dataCollectionFactory);
        $generator->setCollectPatternIds(true);

        $generator->run('test', new DateTimeImmutable(), false);
    }
}
