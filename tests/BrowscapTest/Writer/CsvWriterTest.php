<?php
declare(strict_types = 1);
namespace BrowscapTest\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Data\UserAgent;
use Browscap\Filter\FullFilter;
use Browscap\Formatter\CsvFormatter;
use Browscap\Writer\CsvWriter;
use Browscap\Writer\WriterInterface;
use Monolog\Logger;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class CsvWriterTest extends TestCase
{
    private const STORAGE_DIR = 'storage';

    /**
     * @var CsvWriter
     */
    private $object;

    /**
     * @var string
     */
    private $file;

    /**
     * @throws \ReflectionException
     */
    public function setUp() : void
    {
        vfsStream::setup(self::STORAGE_DIR);
        $this->file = vfsStream::url(self::STORAGE_DIR) . DIRECTORY_SEPARATOR . 'test.csv';

        $logger = $this->createMock(Logger::class);

        $this->object = new CsvWriter($this->file, $logger);
    }

    public function teardown() : void
    {
        $this->object->close();

        unlink($this->file);
    }

    /**
     * tests getting the writer type
     */
    public function testGetType() : void
    {
        self::assertSame(WriterInterface::TYPE_CSV, $this->object->getType());
    }

    /**
     * tests setting and getting a formatter
     *
     * @throws \ReflectionException
     */
    public function testSetGetFormatter() : void
    {
        $mockFormatter = $this->createMock(CsvFormatter::class);

        $this->object->setFormatter($mockFormatter);
        self::assertSame($mockFormatter, $this->object->getFormatter());
    }

    /**
     * tests setting and getting a filter
     *
     * @throws \ReflectionException
     */
    public function testSetGetFilter() : void
    {
        $mockFilter = $this->createMock(FullFilter::class);

        $this->object->setFilter($mockFilter);
        self::assertSame($mockFilter, $this->object->getFilter());
    }

    /**
     * tests setting a file into silent mode
     */
    public function testSetGetSilent() : void
    {
        $silent = true;

        $this->object->setSilent($silent);
        self::assertSame($silent, $this->object->isSilent());
    }

    /**
     * tests rendering the start of the file
     */
    public function testFileStart() : void
    {
        $this->object->fileStart();
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the end of the file
     */
    public function testFileEnd() : void
    {
        $this->object->fileEnd();
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header information
     */
    public function testRenderHeader() : void
    {
        $header = ['TestData to be renderd into the Header'];

        $this->object->renderHeader($header);
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the version information
     */
    public function testRenderVersionIfSilent() : void
    {
        $version = [
            'version' => 'test',
            'released' => date('Y-m-d'),
            'format' => 'TEST',
            'type' => 'full',
        ];

        $this->object->setSilent(true);

        $this->object->renderVersion($version);
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the version information
     */
    public function testRenderVersionIfNotSilent() : void
    {
        $version = [
            'version' => 'test',
            'released' => date('Y-m-d'),
            'format' => 'TEST',
            'type' => 'full',
        ];

        $this->object->setSilent(false);

        $this->object->renderVersion($version);
        self::assertSame(
            '"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL . '"test","' . date('Y-m-d') . '"' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the version information
     */
    public function testRenderVersionIfNotSilentButWithoutVersion() : void
    {
        $version = [];

        $this->object->setSilent(false);

        $this->object->renderVersion($version);
        self::assertSame(
            '"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL . '"0",""' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the header for all division
     */
    public function testRenderAllDivisionsHeader() : void
    {
        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::exactly(2))
            ->method('getProperties')
            ->will(self::returnValue([
                'Test' => 1,
                'isTest' => true,
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $mockFormatter = $this->getMockBuilder(CsvFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(self::exactly(2))
            ->method('formatPropertyName')
            ->will(self::returnArgument(0));

        $this->object->setFormatter($mockFormatter);

        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $map = [
            ['Test', $this->object, true],
            ['isTest', $this->object, false],
            ['abc', $this->object, true],
            ['PropertyName', $this->object, false],
            ['MasterParent', $this->object, false],
            ['LiteMode', $this->object, false],
            ['Parent', $this->object, true],
            ['alpha', $this->object, true],
        ];

        $mockFilter
            ->expects(self::exactly(6))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        $this->object->setFilter($mockFilter);

        $this->object->renderAllDivisionsHeader($collection);
        self::assertSame('Parent,Test' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the header for all division
     */
    public function testRenderAllDivisionsHeaderWithoutProperties() : void
    {
        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $this->object->renderAllDivisionsHeader($collection);
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one division
     */
    public function testRenderDivisionHeader() : void
    {
        $this->object->renderDivisionHeader('test');
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     */
    public function testRenderSectionHeader() : void
    {
        $this->object->renderSectionHeader('test');
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     */
    public function testRenderSectionBodyIfNotSilent() : void
    {
        $this->object->setSilent(false);

        $section = [
            'Test' => 1,
            'isTest' => true,
            'abc' => 'bcd',
        ];

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([
                'Test' => 'abc',
                'abc' => true,
                'alpha' => true,
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $mockFormatter = $this->getMockBuilder(CsvFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyValue'])
            ->getMock();

        $mockFormatter
            ->expects(self::exactly(4))
            ->method('formatPropertyValue')
            ->will(self::returnArgument(0));

        $this->object->setFormatter($mockFormatter);

        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $map = [
            ['Test', $this->object, true],
            ['isTest', $this->object, false],
            ['abc', $this->object, true],
            ['PropertyName', $this->object, false],
            ['MasterParent', $this->object, false],
            ['LiteMode', $this->object, false],
            ['Parent', $this->object, true],
            ['alpha', $this->object, true],
        ];

        $mockFilter
            ->expects(self::exactly(7))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        $this->object->setFilter($mockFilter);

        $this->object->renderSectionBody($section, $collection);
        self::assertSame(',1,bcd,' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     *
     * @throws \ReflectionException
     */
    public function testRenderSectionBodyIfSilent() : void
    {
        $this->object->setSilent(true);

        $section = [
            'Test' => 1,
            'isTest' => true,
            'abc' => 'bcd',
        ];

        $collection = $this->createMock(DataCollection::class);

        $this->object->renderSectionBody($section, $collection);
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one section
     */
    public function testRenderSectionFooter() : void
    {
        $this->object->renderSectionFooter();
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one division
     */
    public function testRenderDivisionFooter() : void
    {
        $this->object->renderDivisionFooter();
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer after all divisions
     */
    public function testRenderAllDivisionsFooter() : void
    {
        $this->object->renderAllDivisionsFooter();
        self::assertSame('', file_get_contents($this->file));
    }
}
