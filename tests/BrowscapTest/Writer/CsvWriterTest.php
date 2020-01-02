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
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

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

    protected function setUp() : void
    {
        vfsStream::setup(self::STORAGE_DIR);
        $this->file = vfsStream::url(self::STORAGE_DIR) . \DIRECTORY_SEPARATOR . 'test.csv';

        $logger = $this->createMock(LoggerInterface::class);

        /* @var LoggerInterface $logger */
        $this->object = new CsvWriter($this->file, $logger);
    }

    protected function teardown() : void
    {
        $this->object->close();

        unlink($this->file);
    }

    /**
     * tests getting the writer type
     */
    public function testGetType() : void
    {
        static::assertSame(WriterInterface::TYPE_CSV, $this->object->getType());
    }

    /**
     * tests setting and getting a formatter
     */
    public function testSetGetFormatter() : void
    {
        $mockFormatter = $this->createMock(CsvFormatter::class);

        /* @var CsvFormatter $mockFormatter */
        $this->object->setFormatter($mockFormatter);
        static::assertSame($mockFormatter, $this->object->getFormatter());
    }

    /**
     * tests setting and getting a filter
     */
    public function testSetGetFilter() : void
    {
        $mockFilter = $this->createMock(FullFilter::class);

        /* @var FullFilter $mockFilter */
        $this->object->setFilter($mockFilter);
        static::assertSame($mockFilter, $this->object->getFilter());
    }

    /**
     * tests setting a file into silent mode
     */
    public function testSetGetSilent() : void
    {
        $silent = true;

        $this->object->setSilent($silent);
        static::assertSame($silent, $this->object->isSilent());
    }

    /**
     * tests rendering the start of the file
     */
    public function testFileStart() : void
    {
        $this->object->fileStart();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the end of the file
     */
    public function testFileEnd() : void
    {
        $this->object->fileEnd();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header information
     */
    public function testRenderHeader() : void
    {
        $header = ['TestData to be renderd into the Header'];

        $this->object->renderHeader($header);
        static::assertSame('', file_get_contents($this->file));
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
        static::assertSame('', file_get_contents($this->file));
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
        static::assertSame(
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
        static::assertSame(
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
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn([
                'Test' => 1,
                'isTest' => true,
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($division);

        $mockFormatter = $this->getMockBuilder(CsvFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(static::exactly(2))
            ->method('formatPropertyName')
            ->willReturnArgument(0);

        /* @var CsvFormatter $mockFormatter */
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
            ->expects(static::exactly(6))
            ->method('isOutputProperty')
            ->willReturnMap($map);

        /* @var FullFilter $mockFilter */
        $this->object->setFilter($mockFilter);

        /* @var DataCollection $collection */
        $this->object->renderAllDivisionsHeader($collection);
        static::assertSame('Parent,Test' . PHP_EOL, file_get_contents($this->file));
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
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($division);

        /* @var DataCollection $collection */
        $this->object->renderAllDivisionsHeader($collection);
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one division
     */
    public function testRenderDivisionHeader() : void
    {
        $this->object->renderDivisionHeader('test');
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     */
    public function testRenderSectionHeader() : void
    {
        $this->object->renderSectionHeader('test');
        static::assertSame('', file_get_contents($this->file));
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
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Test' => 'abc',
                'abc' => true,
                'alpha' => true,
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($division);

        $mockFormatter = $this->getMockBuilder(CsvFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyValue'])
            ->getMock();

        $mockFormatter
            ->expects(static::exactly(4))
            ->method('formatPropertyValue')
            ->willReturnArgument(0);

        /* @var CsvFormatter $mockFormatter */
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
            ->expects(static::exactly(7))
            ->method('isOutputProperty')
            ->willReturnMap($map);

        /* @var FullFilter $mockFilter */
        $this->object->setFilter($mockFilter);

        /* @var DataCollection $collection */
        $this->object->renderSectionBody($section, $collection);
        static::assertSame(',1,bcd,' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
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

        /* @var DataCollection $collection */
        $this->object->renderSectionBody($section, $collection);
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one section
     */
    public function testRenderSectionFooter() : void
    {
        $this->object->renderSectionFooter();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one division
     */
    public function testRenderDivisionFooter() : void
    {
        $this->object->renderDivisionFooter();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer after all divisions
     */
    public function testRenderAllDivisionsFooter() : void
    {
        $this->object->renderAllDivisionsFooter();
        static::assertSame('', file_get_contents($this->file));
    }
}
