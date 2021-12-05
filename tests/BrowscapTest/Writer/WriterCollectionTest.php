<?php

declare(strict_types=1);

namespace BrowscapTest\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Filter\FullFilter;
use Browscap\Formatter\XmlFormatter;
use Browscap\Writer\CsvWriter;
use Browscap\Writer\WriterCollection;
use DateTimeImmutable;
use Exception;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionException;

use function assert;

use const DIRECTORY_SEPARATOR;

class WriterCollectionTest extends TestCase
{
    private const STORAGE_DIR = 'storage';

    private WriterCollection $object;

    private string $file;

    protected function setUp(): void
    {
        $this->file = vfsStream::url(self::STORAGE_DIR) . DIRECTORY_SEPARATOR . 'test.csv';

        $this->object = new WriterCollection();
    }

    /**
     * tests setting and getting a writer
     *
     * @throws ReflectionException
     */
    public function testAddWriterAndSetSilent(): void
    {
        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutput'])
            ->getMock();

        $mockFilter
            ->expects(static::once())
            ->method('isOutput')
            ->willReturn(true);

        $division = $this->createMock(Division::class);

        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilter'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('getFilter')
            ->willReturn($mockFilter);

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);

        assert($division instanceof Division);
        $this->object->setSilent($division);
    }

    /**
     * tests setting a file into silent mode
     */
    public function testSetSilentSection(): void
    {
        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputSection'])
            ->getMock();

        $mockFilter
            ->expects(static::once())
            ->method('isOutputSection')
            ->willReturn(true);

        $mockDivision = [];

        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilter'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('getFilter')
            ->willReturn($mockFilter);

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);
        $this->object->setSilentSection($mockDivision);
    }

    /**
     * tests rendering the start of the file
     */
    public function testFileStart(): void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['fileStart'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('fileStart');

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);
        $this->object->fileStart();
    }

    /**
     * tests rendering the end of the file
     */
    public function testFileEnd(): void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['fileEnd'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('fileEnd');

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);
        $this->object->fileEnd();
    }

    /**
     * tests rendering the header information
     */
    public function testRenderHeader(): void
    {
        $header = ['TestData to be renderd into the Header'];

        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderHeader'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('renderHeader');

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);
        $this->object->renderHeader($header);
    }

    /**
     * tests rendering the version information
     *
     * @throws Exception
     */
    public function testRenderVersion(): void
    {
        $version = 'test';

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutput', 'getType'])
            ->getMock();

        $mockFilter
            ->expects(static::never())
            ->method('isOutput')
            ->willReturn(true);
        $mockFilter
            ->expects(static::once())
            ->method('getType')
            ->willReturn('Test');

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockFormatter
            ->expects(static::once())
            ->method('getType')
            ->willReturn('test');

        $logger = $this->createMock(LoggerInterface::class);

        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->setMethods(['getFilter', 'getFormatter'])
            ->setConstructorArgs([$this->file, $logger])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('getFilter')
            ->willReturn($mockFilter);
        $mockWriter
            ->expects(static::once())
            ->method('getFormatter')
            ->willReturn($mockFormatter);

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);

        assert($collection instanceof DataCollection);
        $this->object->renderVersion($version, new DateTimeImmutable(), $collection);
        $this->object->close();
    }

    /**
     * tests rendering the header for all division
     *
     * @throws ReflectionException
     */
    public function testRenderAllDivisionsHeader(): void
    {
        $collection = $this->createMock(DataCollection::class);

        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderAllDivisionsHeader'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('renderAllDivisionsHeader');

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);

        assert($collection instanceof DataCollection);
        $this->object->renderAllDivisionsHeader($collection);
    }

    /**
     * tests rendering the header of one division
     */
    public function testRenderDivisionHeader(): void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderDivisionHeader'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('renderDivisionHeader');

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);
        $this->object->renderDivisionHeader('test');
    }

    /**
     * tests rendering the header of one section
     */
    public function testRenderSectionHeader(): void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderSectionHeader'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('renderSectionHeader');

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);
        $this->object->renderSectionHeader('test');
    }

    /**
     * tests rendering the body of one section
     *
     * @throws ReflectionException
     */
    public function testRenderSectionBody(): void
    {
        $section = [
            'Comment' => 1,
            'Win16' => true,
            'Platform' => 'bcd',
        ];

        $collection = $this->createMock(DataCollection::class);
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderSectionBody'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('renderSectionBody');

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);

        assert($collection instanceof DataCollection);
        $this->object->renderSectionBody($section, $collection);
    }

    /**
     * tests rendering the footer of one section
     */
    public function testRenderSectionFooter(): void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderSectionFooter'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('renderSectionFooter');

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);
        $this->object->renderSectionFooter();
    }

    /**
     * tests rendering the footer of one division
     */
    public function testRenderDivisionFooter(): void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderDivisionFooter'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('renderDivisionFooter');

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);
        $this->object->renderDivisionFooter();
    }

    /**
     * tests rendering the footer after all divisions
     */
    public function testRenderAllDivisionsFooter(): void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderAllDivisionsFooter'])
            ->getMock();

        $mockWriter
            ->expects(static::once())
            ->method('renderAllDivisionsFooter');

        assert($mockWriter instanceof CsvWriter);
        $this->object->addWriter($mockWriter);
        $this->object->renderAllDivisionsFooter();
    }
}
