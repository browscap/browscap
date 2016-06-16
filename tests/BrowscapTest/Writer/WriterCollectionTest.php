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
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', [], [], '', false);

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
        $mockFilter = $this->getMock('\Browscap\Filter\FullFilter', ['isOutput'], [], '', false);
        $mockFilter
            ->expects(self::once())
            ->method('isOutput')
            ->will(self::returnValue(true));

        $mockDivision = $this->getMock('\Browscap\Data\Division', [], [], '', false);

        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', ['getFilter'], [], '', false);
        $mockWriter
            ->expects(self::once())
            ->method('getFilter')
            ->will(self::returnValue($mockFilter));

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->setSilent($mockDivision));
    }

    /**
     * tests setting a file into silent mode
     *
     * @group writer
     * @group sourcetest
     */
    public function testSetSilentSection()
    {
        $mockFilter = $this->getMock('\Browscap\Filter\FullFilter', ['isOutputSection'], [], '', false);
        $mockFilter
            ->expects(self::once())
            ->method('isOutputSection')
            ->will(self::returnValue(true));

        $mockDivision = [];

        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', ['getFilter'], [], '', false);
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
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', [], [], '', false);

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
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', [], [], '', false);

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

        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', [], [], '', false);

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

        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            ['getGenerationDate'],
            [],
            '',
            false
        );
        $mockCollection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()));

        $mockFilter = $this->getMock('\Browscap\Filter\FullFilter', ['isOutput', 'getType'], [], '', false);
        $mockFilter
            ->expects(self::never())
            ->method('isOutput')
            ->will(self::returnValue(true));
        $mockFilter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue('Test'));

        $mockFormatter = $this->getMock(
            '\Browscap\Formatter\XmlFormatter',
            ['getType'],
            [],
            '',
            false
        );
        $mockFormatter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue('test'));

        $mockLogger = $this->getMock('\Monolog\Logger', [], [], '', false);

        $mockWriter = $this->getMock(
            '\Browscap\Writer\CsvWriter',
            ['getFilter', 'getFormatter', 'getLogger'],
            [$this->file],
            '',
            true
        );
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
            ->will(self::returnValue($mockLogger));

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderVersion($version, $mockCollection));
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
        $mockCollection = $this->getMock('\Browscap\Data\DataCollection', [], [], '', false);

        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', [], [], '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderAllDivisionsHeader($mockCollection));
    }

    /**
     * tests rendering the header of one division
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderDivisionHeader()
    {
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', [], [], '', false);

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
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', [], [], '', false);

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
            'Comment'  => 1,
            'Win16'    => true,
            'Platform' => 'bcd',
        ];

        $mockCollection = $this->getMock('\Browscap\Data\DataCollection', [], [], '', false);
        $mockWriter     = $this->getMock('\Browscap\Writer\CsvWriter', [], [], '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderSectionBody($section, $mockCollection));
    }

    /**
     * tests rendering the footer of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionFooter()
    {
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', [], [], '', false);

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
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', [], [], '', false);

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
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', [], [], '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderAllDivisionsFooter());
    }
}
