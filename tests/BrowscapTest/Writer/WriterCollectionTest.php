<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowscapTest\Writer;

use Browscap\Writer\WriterCollection;
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
    const STORAGE_DIR = 'storage';

    /**
     * @var \Browscap\Writer\WriterCollection
     */
    private $object = null;

    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $root = null;

    /**
     * @var string
     */
    private $file = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
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
    public function testAddWriterAndSetSilent()
    {
        $mockFilter = $this->getMockBuilder(\Browscap\Filter\FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutput'])
            ->getMock();

        $mockFilter
            ->expects(self::once())
            ->method('isOutput')
            ->will(self::returnValue(true));

        $division = $this->createMock(\Browscap\Data\Division::class);

        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
    public function testSetSilentSection()
    {
        $mockFilter = $this->getMockBuilder(\Browscap\Filter\FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputSection'])
            ->getMock();

        $mockFilter
            ->expects(self::once())
            ->method('isOutputSection')
            ->will(self::returnValue(true));

        $mockDivision = [];

        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
    public function testFileStart()
    {
        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
    public function testFileEnd()
    {
        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
    public function testRenderHeader()
    {
        $header = ['TestData to be renderd into the Header'];

        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
    public function testRenderVersion()
    {
        $version = 'test';

        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerationDate'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()));

        $mockFilter = $this->getMockBuilder(\Browscap\Filter\FullFilter::class)
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

        $mockFormatter = $this->getMockBuilder(\Browscap\Formatter\XmlFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockFormatter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue('test'));

        $logger = $this->createMock(\Monolog\Logger::class);

        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
            ->setMethods(['getFilter', 'getFormatter', 'getLogger'])
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
    public function testRenderAllDivisionsHeader()
    {
        $collection = $this->createMock(\Browscap\Data\DataCollection::class);

        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
    public function testRenderDivisionHeader()
    {
        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
    public function testRenderSectionHeader()
    {
        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
    public function testRenderSectionBody()
    {
        $section = [
            'Comment' => 1,
            'Win16' => true,
            'Platform' => 'bcd',
        ];

        $collection = $this->createMock(\Browscap\Data\DataCollection::class);
        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
    public function testRenderSectionFooter()
    {
        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
    public function testRenderDivisionFooter()
    {
        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
    public function testRenderAllDivisionsFooter()
    {
        $mockWriter = $this->getMockBuilder(\Browscap\Writer\CsvWriter::class)
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
