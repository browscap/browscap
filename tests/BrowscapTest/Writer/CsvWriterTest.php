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

use Browscap\Writer\CsvWriter;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use org\bovigo\vfs\vfsStream;

/**
 * Class CsvWriterTest
 *
 * @category   BrowscapTest
 * @package    Writer
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class CsvWriterTest extends \PHPUnit_Framework_TestCase
{
    const STORAGE_DIR = 'storage';

    /**
     * @var \Browscap\Writer\CsvWriter
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
        
        $this->object = new CsvWriter($this->file);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    public function teardown()
    {
        $this->object->close();
        
        unlink($this->file);
    }

    public function testSetGetLogger()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', array(), array(), '', false);

        self::assertSame($this->object, $this->object->setLogger($mockLogger));
        self::assertSame($mockLogger, $this->object->getLogger());
    }

    public function testSetGetFormatter()
    {
        $mockFormatter = $this->getMock('\Browscap\Formatter\CsvFormatter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));
        self::assertSame($mockFormatter, $this->object->getFormatter());
    }

    public function testSetGetFilter()
    {
        $mockFilter = $this->getMock('\Browscap\Filter\FullFilter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->setFilter($mockFilter));
        self::assertSame($mockFilter, $this->object->getFilter());
    }

    public function testSetGetSilent()
    {
        $silent = true;

        self::assertSame($this->object, $this->object->setSilent($silent));
        self::assertSame($silent, $this->object->isSilent());
    }

    public function testFileStart()
    {
        self::assertSame($this->object, $this->object->fileStart());
        self::assertSame('', file_get_contents($this->file));
    }

    public function testFileEnd()
    {
        self::assertSame($this->object, $this->object->fileEnd());
        self::assertSame('', file_get_contents($this->file));
    }

    public function testRenderHeader()
    {
        $header = array('TestData to be renderd into the Header');
        
        self::assertSame($this->object, $this->object->renderHeader($header));
        self::assertSame('', file_get_contents($this->file));
    }

    public function testRenderVersionIfSilent()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', array(), array(), '', false);
        $this->object->setLogger($mockLogger);

        $version = array(
            'version' => 'test',
            'released' => date('Y-m-d'),
            'format' => 'TEST',
            'type' => 'full',
            
        );
        
        $this->object->setSilent(true);
        
        self::assertSame($this->object, $this->object->renderVersion($version));
        self::assertSame('', file_get_contents($this->file));
    }

    public function testRenderVersionIfNotSilent()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', array(), array(), '', false);
        $this->object->setLogger($mockLogger);

        $version = array(
            'version' => 'test',
            'released' => date('Y-m-d'),
            'format' => 'TEST',
            'type' => 'full',
            
        );
        
        $this->object->setSilent(false);
        
        self::assertSame($this->object, $this->object->renderVersion($version));
        self::assertSame('"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL . '"test","2014-07-09"' . PHP_EOL, file_get_contents($this->file));
    }

    public function testRenderVersionIfNotSilentButWithoutVersion()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', array(), array(), '', false);
        $this->object->setLogger($mockLogger);

        $version = array();
        
        $this->object->setSilent(false);
        
        self::assertSame($this->object, $this->object->renderVersion($version));
        self::assertSame('"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL . '"0",""' . PHP_EOL, file_get_contents($this->file));
    }
}
