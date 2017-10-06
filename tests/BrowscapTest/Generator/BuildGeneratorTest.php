<?php
declare(strict_types = 1);
namespace BrowscapTest\Generator;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Data\UserAgent;
use Browscap\Generator\BuildGenerator;
use Browscap\Writer\WriterCollection;
use Monolog\Logger;

/**
 * Class BuildGeneratorTestTest
 */
class BuildGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $messages = [];

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        self::markTestSkipped();
        $this->logger = $this->createMock(Logger::class);
    }

    /**
     * tests failing the build if the build dir does not exist
     *
     * @group generator
     * @group sourcetest
     */
    public function testConstructFailsIfTheDirDoesNotExsist() : void
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage('The directory "/dar" does not exist, or we cannot access it');

        $writerCollection      = $this->createMock(WriterCollection::class);
        $dataCollectionFactory = $this->createMock(DataCollectionFactory::class);

        new BuildGenerator('/dar', '', $this->logger, $writerCollection, $dataCollectionFactory);
    }

    /**
     * tests failing the build if no build dir is a file
     *
     * @group generator
     * @group sourcetest
     */
    public function testConstructFailsIfTheDirIsNotAnDirectory() : void
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage('The path "' . __FILE__ . '" did not resolve to a directory');

        $writerCollection      = $this->createMock(WriterCollection::class);
        $dataCollectionFactory = $this->createMock(DataCollectionFactory::class);

        new BuildGenerator(__FILE__, '', $this->logger, $writerCollection, $dataCollectionFactory);
    }

    /**
     * tests running a build
     *
     * @group generator
     * @group sourcetest
     */
    public function testBuild() : void
    {
        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(self::exactly(3))
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::exactly(4))
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'DefaultProperties',
                'Browser' => 'xyz',
                'Version' => '1.0',
                'MajorBer' => '1',
                'Device_Type' => 'Desktop',
                'isTablet' => false,
                'isMobileDevice' => false,
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

        $division
            ->expects(self::exactly(4))
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

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerationDate', 'getDefaultProperties', 'getDefaultBrowser', 'getDivisions'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTimeImmutable()));
        $collection
            ->expects(self::exactly(2))
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDefaultBrowser')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDivisions')
            ->will(self::returnValue([$division]));

        $mockCreator = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $mockCreator
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

        $generator = new BuildGenerator('.', '.', $this->logger, $writerCollection, $mockCreator);
        $generator->setCollectPatternIds(false);

        $generator->run('test', false);
    }

    /**
     * tests running a build without generating a zip file
     *
     * @group generator
     * @group sourcetest
     */
    public function testBuildWithoutZip() : void
    {
        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(self::exactly(3))
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::exactly(4))
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'DefaultProperties',
                'Browser' => 'xyz',
                'Version' => '1.0',
                'MajorBer' => '1',
                'Device_Type' => 'Desktop',
                'isTablet' => false,
                'isMobileDevice' => false,
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

        $division
            ->expects(self::exactly(4))
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

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerationDate', 'getDefaultProperties', 'getDefaultBrowser', 'getDivisions'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTimeImmutable()));
        $collection
            ->expects(self::exactly(2))
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDefaultBrowser')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDivisions')
            ->will(self::returnValue([$division]));

        $mockCreator = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $mockCreator
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

        $generator = new BuildGenerator('.', '.', $this->logger, $writerCollection, $mockCreator);
        $generator->setCollectPatternIds(true);

        $generator->run('test', false);
    }
}
