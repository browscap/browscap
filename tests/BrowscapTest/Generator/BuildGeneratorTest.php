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
use Monolog\Logger;
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
    public function setUp() : void
    {
        $this->logger = $this->createMock(Logger::class);
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
            ->expects(self::exactly(2))
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::exactly(2))
            ->method('getProperties')
            ->will(self::returnValue([
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
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

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
            ->will(self::returnValue('DefaultProperties'));

        $defaultProperties
            ->expects(self::exactly(3))
            ->method('getProperties')
            ->will(self::returnValue([
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
                                     ]));

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

        $dataCollectionFactory = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $dataCollectionFactory
            ->expects(self::any())
            ->method('createDataCollection')
            ->will(self::returnValue($collection));

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
            ->expects(self::once())
            ->method('fileStart')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::once())
            ->method('renderHeader')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::once())
            ->method('renderAllDivisionsHeader')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::any())
            ->method('renderSectionHeader')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::any())
            ->method('renderSectionBody')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::once())
            ->method('fileEnd')
            ->will(self::returnSelf());

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
            ->expects(self::exactly(2))
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::exactly(2))
            ->method('getProperties')
            ->will(self::returnValue([
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
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

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
            ->will(self::returnValue('DefaultProperties'));

        $defaultProperties
            ->expects(self::exactly(3))
            ->method('getProperties')
            ->will(self::returnValue([
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
                                     ]));

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

        $dataCollectionFactory = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $dataCollectionFactory
            ->expects(self::any())
            ->method('createDataCollection')
            ->will(self::returnValue($collection));

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
            ->expects(self::once())
            ->method('fileStart')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::once())
            ->method('renderHeader')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::once())
            ->method('renderAllDivisionsHeader')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::any())
            ->method('renderSectionHeader')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::any())
            ->method('renderSectionBody')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::once())
            ->method('fileEnd')
            ->will(self::returnSelf());

        $generator = new BuildGenerator('.', '.', $this->logger, $writerCollection, $dataCollectionFactory);
        $generator->setCollectPatternIds(true);

        $generator->run('test', new DateTimeImmutable(), false);
    }
}
