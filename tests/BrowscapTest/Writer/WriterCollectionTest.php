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
 * @package    Writer
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
 * @package    Writer
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
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $file = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    public function setUp()
    {
        $this->root = vfsStream::setup(self::STORAGE_DIR);
        $this->file = vfsStream::url(self::STORAGE_DIR) . DIRECTORY_SEPARATOR . 'test.csv';

        $this->object = new WriterCollection();
    }

    public function testAddWriter()
    {
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
    }

    public function testSetSilent()
    {
        $mockFilter = $this->getMock('\Browscap\Filter\FullFilter', array('isOutput'), array(), '', false);
        $mockFilter
            ->expects(self::once())
            ->method('isOutput')
            ->will(self::returnValue(true))
        ;

        $mockDivision = $this->getMock('\Browscap\Data\Division', array(), array(), '', false);

        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array('getFilter'), array(), '', false);
        $mockWriter
            ->expects(self::once())
            ->method('getFilter')
            ->will(self::returnValue($mockFilter))
        ;

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->setSilent($mockDivision));
    }

    public function testFileStart()
    {
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->fileStart());
    }

    public function testFileEnd()
    {
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->fileEnd());
    }

    public function testRenderHeader()
    {
        $header = array('TestData to be renderd into the Header');

        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderHeader($header));
    }

    public function testRenderVersion()
    {
        $version = 'test';

        $mockCollection = $this->getMock('\Browscap\Data\DataCollection', array('getGenerationDate'), array(), '', false);
        $mockCollection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()))
        ;

        $mockFilter = $this->getMock('\Browscap\Filter\FullFilter', array('isOutput', 'getType'), array(), '', false);
        $mockFilter
            ->expects(self::never())
            ->method('isOutput')
            ->will(self::returnValue(true))
        ;
        $mockFilter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue('Test'))
        ;

        $mockFormatter = $this->getMock(
            '\Browscap\Formatter\XmlFormatter',
            array('getType'),
            array(),
            '',
            false
        );
        $mockFormatter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue('test'))
        ;

        $mockLogger = $this->getMock('\Monolog\Logger', array(), array(), '', false);

        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array('getFilter', 'getFormatter', 'getLogger'), array($this->file), '', true);
        $mockWriter
            ->expects(self::once())
            ->method('getFilter')
            ->will(self::returnValue($mockFilter))
        ;
        $mockWriter
            ->expects(self::once())
            ->method('getFormatter')
            ->will(self::returnValue($mockFormatter))
        ;
        $mockWriter
            ->expects(self::once())
            ->method('getLogger')
            ->will(self::returnValue($mockLogger))
        ;

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderVersion($version, $mockCollection));
        self::assertSame($this->object, $this->object->close());
    }

    public function testRenderAllDivisionsHeader()
    {
        $mockCollection = $this->getMock('\Browscap\Data\DataCollection', array(), array(), '', false);

        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderAllDivisionsHeader($mockCollection));
    }

    public function testRenderDivisionHeader()
    {
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderDivisionHeader('test'));
    }

    public function testRenderSectionHeader()
    {
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderSectionHeader('test'));
    }

    public function testRenderSectionBody()
    {
        $section = array(
            'Comment'  => 1,
            'Win16'    => true,
            'Platform' => 'bcd'
        );

        $mockCollection = $this->getMock('\Browscap\Data\DataCollection', array(), array(), '', false);
        $mockWriter     = $this->getMock('\Browscap\Writer\CsvWriter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderSectionBody($section, $mockCollection));
    }

    public function testRenderSectionFooter()
    {
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderSectionFooter());
    }

    public function testRenderDivisionFooter()
    {
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderDivisionFooter());
    }

    public function testRenderAllDivisionsFooter()
    {
        $mockWriter = $this->getMock('\Browscap\Writer\CsvWriter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->addWriter($mockWriter));
        self::assertSame($this->object, $this->object->renderAllDivisionsFooter());
    }
}
