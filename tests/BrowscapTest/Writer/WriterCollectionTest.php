<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   BrowscapTest
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Writer;

use Browscap\Writer\WriterCollection;
use org\bovigo\vfs\vfsStream;

/**
 * Class WriterCollectionTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class WriterCollectionTest extends \PHPUnit_Framework_TestCase
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
    public function testAddWriter()
    {
        $mockWriter = $this->createMock(\Browscap\Writer\CsvWriter::class);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
    }

    /**
     * tests setting a file into silent mode
     *
     * @group writer
     * @group sourcetest
     */
    public function testSetSilent()
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

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->setSilent($division));
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

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->setSilentSection($mockDivision));
    }

    /**
     * tests rendering the start of the file
     *
     * @group writer
     * @group sourcetest
     */
    public function testFileStart()
    {
        $mockWriter = $this->createMock(\Browscap\Writer\CsvWriter::class);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->fileStart());
    }

    /**
     * tests rendering the end of the file
     *
     * @group writer
     * @group sourcetest
     */
    public function testFileEnd()
    {
        $mockWriter = $this->createMock(\Browscap\Writer\CsvWriter::class);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->fileEnd());
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

        $mockWriter = $this->createMock(\Browscap\Writer\CsvWriter::class);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderHeader($header));
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
            ->setConstructorArgs([$this->file])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('getFilter')
            ->will(self::returnValue($mockFilter));
        $mockWriter
            ->expects(self::once())
            ->method('getFormatter')
            ->will(self::returnValue($mockFormatter));
        $mockWriter
            ->expects(self::once())
            ->method('getLogger')
            ->will(self::returnValue($logger));

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderVersion($version, $collection));
        self::assertSame($this->object, $this->object->close());
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

        $mockWriter = $this->createMock(\Browscap\Writer\CsvWriter::class);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderAllDivisionsHeader($collection));
    }

    /**
     * tests rendering the header of one division
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderDivisionHeader()
    {
        $mockWriter = $this->createMock(\Browscap\Writer\CsvWriter::class);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderDivisionHeader('test'));
    }

    /**
     * tests rendering the header of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionHeader()
    {
        $mockWriter = $this->createMock(\Browscap\Writer\CsvWriter::class);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderSectionHeader('test'));
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
        $mockWriter = $this->createMock(\Browscap\Writer\CsvWriter::class);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderSectionBody($section, $collection));
    }

    /**
     * tests rendering the footer of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionFooter()
    {
        $mockWriter = $this->createMock(\Browscap\Writer\CsvWriter::class);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderSectionFooter());
    }

    /**
     * tests rendering the footer of one division
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderDivisionFooter()
    {
        $mockWriter = $this->createMock(\Browscap\Writer\CsvWriter::class);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderDivisionFooter());
    }

    /**
     * tests rendering the footer after all divisions
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderAllDivisionsFooter()
    {
        $mockWriter = $this->createMock(\Browscap\Writer\CsvWriter::class);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderAllDivisionsFooter());
    }
}
