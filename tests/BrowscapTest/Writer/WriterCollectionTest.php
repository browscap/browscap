<?php
declare(strict_types = 1);
namespace BrowscapTest\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Filter\FullFilter;
use Browscap\Formatter\XmlFormatter;
use Browscap\Writer\CsvWriter;
use Browscap\Writer\WriterCollection;
use Monolog\Logger;
use org\bovigo\vfs\vfsStream;

/**
 * Class WriterCollectionTest
 *
 * @category   BrowscapTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class WriterCollectionTest extends \PHPUnit\Framework\TestCase
{
    private const STORAGE_DIR = 'storage';

    /**
     * @var \Browscap\Writer\WriterCollection
     */
    private $object;

    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;

    /**
     * @var string
     */
    private $file;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->root = vfsStream::setup(self::STORAGE_DIR);
        $this->file = vfsStream::url(self::STORAGE_DIR) . DIRECTORY_SEPARATOR . 'test.csv';

        $this->object = new WriterCollection();
    }

    /**
     * tests setting and getting a writer
     *
     * @group writer
     * @group sourcetest
     */
    public function testAddWriterAndSetSilent() : void
    {
        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutput'])
            ->getMock();

        $mockFilter
            ->expects(self::once())
            ->method('isOutput')
            ->will(self::returnValue(true));

        $division = $this->createMock(Division::class);

        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilter'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('getFilter')
            ->will(self::returnValue($mockFilter));

        $this->object->addWriter($mockWriter);

        $this->object->setSilent($division);
    }

    /**
     * tests setting a file into silent mode
     *
     * @group writer
     * @group sourcetest
     */
    public function testSetSilentSection() : void
    {
        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputSection'])
            ->getMock();

        $mockFilter
            ->expects(self::once())
            ->method('isOutputSection')
            ->will(self::returnValue(true));

        $mockDivision = [];

        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilter'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('getFilter')
            ->will(self::returnValue($mockFilter));

        $this->object->addWriter($mockWriter);
        $this->object->setSilentSection($mockDivision);
    }

    /**
     * tests rendering the start of the file
     *
     * @group writer
     * @group sourcetest
     */
    public function testFileStart() : void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['fileStart'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('fileStart');

        $this->object->addWriter($mockWriter);
        $this->object->fileStart();
    }

    /**
     * tests rendering the end of the file
     *
     * @group writer
     * @group sourcetest
     */
    public function testFileEnd() : void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['fileEnd'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('fileEnd');

        $this->object->addWriter($mockWriter);
        $this->object->fileEnd();
    }

    /**
     * tests rendering the header information
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderHeader() : void
    {
        $header = ['TestData to be renderd into the Header'];

        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderHeader'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('renderHeader');

        $this->object->addWriter($mockWriter);
        $this->object->renderHeader($header);
    }

    /**
     * tests rendering the version information
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderVersion() : void
    {
        $version = 'test';

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerationDate'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()));

        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutput', 'getType'])
            ->getMock();

        $mockFilter
            ->expects(self::never())
            ->method('isOutput')
            ->will(self::returnValue(true));
        $mockFilter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue('Test'));

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockFormatter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue('test'));

        $logger = $this->createMock(Logger::class);

        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->setMethods(['getFilter', 'getFormatter'])
            ->setConstructorArgs([$this->file, $logger])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('getFilter')
            ->will(self::returnValue($mockFilter));
        $mockWriter
            ->expects(self::once())
            ->method('getFormatter')
            ->will(self::returnValue($mockFormatter));

        $this->object->addWriter($mockWriter);
        $this->object->renderVersion($version, $collection);
        $this->object->close();
    }

    /**
     * tests rendering the header for all division
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderAllDivisionsHeader() : void
    {
        $collection = $this->createMock(DataCollection::class);

        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderAllDivisionsHeader'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('renderAllDivisionsHeader');

        $this->object->addWriter($mockWriter);
        $this->object->renderAllDivisionsHeader($collection);
    }

    /**
     * tests rendering the header of one division
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderDivisionHeader() : void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderDivisionHeader'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('renderDivisionHeader');

        $this->object->addWriter($mockWriter);
        $this->object->renderDivisionHeader('test');
    }

    /**
     * tests rendering the header of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionHeader() : void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderSectionHeader'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('renderSectionHeader');

        $this->object->addWriter($mockWriter);
        $this->object->renderSectionHeader('test');
    }

    /**
     * tests rendering the body of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionBody() : void
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
            ->expects(self::once())
            ->method('renderSectionBody');

        $this->object->addWriter($mockWriter);
        $this->object->renderSectionBody($section, $collection);
    }

    /**
     * tests rendering the footer of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionFooter() : void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderSectionFooter'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('renderSectionFooter');

        $this->object->addWriter($mockWriter);
        $this->object->renderSectionFooter();
    }

    /**
     * tests rendering the footer of one division
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderDivisionFooter() : void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderDivisionFooter'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('renderDivisionFooter');

        $this->object->addWriter($mockWriter);
        $this->object->renderDivisionFooter();
    }

    /**
     * tests rendering the footer after all divisions
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderAllDivisionsFooter() : void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderAllDivisionsFooter'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('renderAllDivisionsFooter');

        $this->object->addWriter($mockWriter);
        $this->object->renderAllDivisionsFooter();
    }
}
