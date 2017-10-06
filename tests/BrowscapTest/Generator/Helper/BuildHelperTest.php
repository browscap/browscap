<?php
declare(strict_types = 1);
namespace BrowscapTest\Generator\Helper;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Data\UserAgent;
use Browscap\Generator\Helper\BuildHelper;
use Browscap\Writer\WriterCollection;
use Monolog\Logger;

/**
 * Class BuildGeneratorTestTest
 */
class BuildHelperTest extends \PHPUnit\Framework\TestCase
{
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
     * tests running a build
     *
     * @group generator
     * @group sourcetest
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
            ->with(self::callback(function (array $props) {
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
            ->expects(self::exactly(4))
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
                'PatternId' => 'tests/test.json::u0::c0::d::p',
                'Device_Type' => 'Desktop',
                'isTablet' => false,
                'isMobileDevice' => false,
            ]));

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

        $collectionCreator = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $collectionCreator->expects(self::once())
            ->method('createDataCollection')
            ->will(self::returnValue($collection));

        BuildHelper::run('test', '.', $this->logger, $writerCollection, $collectionCreator);
    }

    /**
     * tests running a build
     *
     * @group generator
     * @group sourcetest
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
            ->with(self::callback(function (array $props) {
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
            ->expects(self::exactly(4))
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::exactly(6))
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
                'PatternId' => 'tests/test.json::u0::c0::d::p',
                'Device_Type' => 'Desktop',
                'isTablet' => false,
                'isMobileDevice' => false,
            ]));

        $division
            ->expects(self::exactly(6))
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

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerationDate', 'getDefaultProperties', 'getDefaultBrowser', 'getDivisions'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTimeImmutable()));
        $collection
            ->expects(self::exactly(3))
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));
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

        BuildHelper::run('test', '.', $this->logger, $writerCollection, $collectionCreator);
    }

    /**
     * tests running a build with pattern id collection enabled
     *
     * @group generator
     * @group sourcetest
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
            ->with(self::callback(function (array $props) {
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
            ->expects(self::exactly(3))
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::exactly(4))
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'Defaultproperties',
                'Version' => '1.0',
                'MajorVer' => 1,
                'Browser' => 'xyz',
                'PatternId' => 'tests/test.json::u0::c0::d::p',
                'Device_Type' => 'Desktop',
                'isTablet' => false,
                'isMobileDevice' => false,
            ]));

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

        $collectionCreator = $this->getMockBuilder(DataCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $collectionCreator->expects(self::once())
            ->method('createDataCollection')
            ->will(self::returnValue($collection));

        BuildHelper::run('test', '.', $this->logger, $writerCollection, $collectionCreator, true);
    }
}
