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

use Browscap\Writer\IniWriter;
use org\bovigo\vfs\vfsStream;

/**
 * Class IniWriterTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class IniWriterTest extends \PHPUnit_Framework_TestCase
{
    const STORAGE_DIR = 'storage';

    /**
     * @var \Browscap\Writer\IniWriter
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
        $this->file = vfsStream::url(self::STORAGE_DIR) . DIRECTORY_SEPARATOR . 'test.ini';

        $this->object = new IniWriter($this->file);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function teardown()
    {
        $this->object->close();

        unlink($this->file);
    }

    /**
     * tests setting and getting a logger
     *
     * @group writer
     * @group sourcetest
     */
    public function testSetGetLogger()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', [], [], '', false);

        self::assertSame($this->object, $this->object->setLogger($mockLogger));
        self::assertSame($mockLogger, $this->object->getLogger());
    }

    /**
     * tests getting the writer type
     *
     * @group writer
     * @group sourcetest
     */
    public function testGetType()
    {
        self::assertSame('ini', $this->object->getType());
    }

    /**
     * tests setting and getting a formatter
     *
     * @group writer
     * @group sourcetest
     */
    public function testSetGetFormatter()
    {
        $mockFormatter = $this->getMock('\Browscap\Formatter\PhpFormatter', [], [], '', false);

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));
        self::assertSame($mockFormatter, $this->object->getFormatter());
    }

    /**
     * tests setting and getting a filter
     *
     * @group writer
     * @group sourcetest
     */
    public function testSetGetFilter()
    {
        $mockFilter = $this->getMock('\Browscap\Filter\FullFilter', [], [], '', false);

        self::assertSame($this->object, $this->object->setFilter($mockFilter));
        self::assertSame($mockFilter, $this->object->getFilter());
    }

    /**
     * tests setting a file into silent mode
     *
     * @group writer
     * @group sourcetest
     */
    public function testSetGetSilent()
    {
        $silent = true;

        self::assertSame($this->object, $this->object->setSilent($silent));
        self::assertSame($silent, $this->object->isSilent());
    }

    /**
     * tests rendering the start of the file
     *
     * @group writer
     * @group sourcetest
     */
    public function testFileStart()
    {
        self::assertSame($this->object, $this->object->fileStart());
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the end of the file
     *
     * @group writer
     * @group sourcetest
     */
    public function testFileEnd()
    {
        self::assertSame($this->object, $this->object->fileEnd());
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header information
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderHeaderIfSilent()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', [], [], '', false);
        $this->object->setLogger($mockLogger);

        $header = ['TestData to be renderd into the Header'];

        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->renderHeader($header));
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header information
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderHeaderIfNotSilent()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', [], [], '', false);
        $this->object->setLogger($mockLogger);

        $header = ['TestData to be renderd into the Header'];

        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderHeader($header));
        self::assertSame(
            ';;; TestData to be renderd into the Header' . PHP_EOL . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the version information
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderVersionIfSilent()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', [], [], '', false);
        $this->object->setLogger($mockLogger);

        $version = [
            'version' => 'test',
            'released' => date('Y-m-d'),
            'format' => 'TEST',
            'type' => 'full',

        ];

        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->renderVersion($version));
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the version information
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderVersionIfNotSilent()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', [], [], '', false);
        $this->object->setLogger($mockLogger);

        $version = [
            'version' => 'test',
            'released' => date('Y-m-d'),
            'format' => 'TEST',
            'type' => 'full',

        ];

        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderVersion($version));
        self::assertSame(
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; Browscap Version' . PHP_EOL . PHP_EOL . '[GJK_Browscap_Version]'
            . PHP_EOL . 'Version=test' . PHP_EOL . 'Released=' . date('Y-m-d') . PHP_EOL . 'Format=TEST' . PHP_EOL
            . 'Type=full' . PHP_EOL . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the version information
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderVersionIfNotSilentButWithoutVersion()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', [], [], '', false);
        $this->object->setLogger($mockLogger);

        $version = [];

        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderVersion($version));
        self::assertSame(
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; Browscap Version' . PHP_EOL . PHP_EOL . '[GJK_Browscap_Version]'
            . PHP_EOL . 'Version=0' . PHP_EOL . 'Released=' . PHP_EOL . 'Format=' . PHP_EOL . 'Type='
            . PHP_EOL . PHP_EOL,
            file_get_contents($this->file)
        );
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

        self::assertSame($this->object, $this->object->renderAllDivisionsHeader($mockCollection));
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one division
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderDivisionHeaderIfNotSilent()
    {
        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderDivisionHeader('test'));
        self::assertSame(
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; test' . PHP_EOL . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the header of one division
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderDivisionHeaderIfSilent()
    {
        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->renderDivisionHeader('test'));
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionHeaderIfNotSilent()
    {
        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderSectionHeader('test'));
        self::assertSame('[test]' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionHeaderIfSilent()
    {
        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->renderSectionHeader('test'));
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionBodyIfNotSilent()
    {
        $this->object->setSilent(false);

        $section = [
            'Comment'  => 1,
            'Win16'    => true,
            'Platform' => 'bcd',
        ];

        $expectedAgents = [
            0 => [
                'properties' => [
                    'Comment' => 1,
                    'Win16'   => true,
                ],
            ],
        ];

        $mockExpander = $this->getMock('\Browscap\Data\Expander', ['trimProperty'], [], '', false);
        $mockExpander
            ->expects(self::any())
            ->method('trimProperty')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setExpander($mockExpander));

        $mockDivision = $this->getMock('\Browscap\Data\Division', ['getUserAgents'], [], '', false);
        $mockDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($expectedAgents));

        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            ['getDefaultProperties'],
            [],
            '',
            false
        );
        $mockCollection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($mockDivision));

        $mockFormatter = $this->getMock(
            '\Browscap\Formatter\PhpFormatter',
            ['formatPropertyName'],
            [],
            '',
            false
        );
        $mockFormatter
            ->expects(self::once())
            ->method('formatPropertyName')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        $mockFilter = $this->getMock('\Browscap\Filter\FullFilter', ['isOutputProperty'], [], '', false);
        $map        = [
            ['Comment', $this->object, true],
            ['Win16', $this->object, false],
            ['Platform', $this->object, true],
        ];

        $mockFilter
            ->expects(self::exactly(2))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        self::assertSame($this->object, $this->object->setFilter($mockFilter));

        self::assertSame($this->object, $this->object->renderSectionBody($section, $mockCollection));
        self::assertSame('Comment="1"' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionBodyIfNotSilentWithParents()
    {
        $this->object->setSilent(false);

        $section = [
            'Parent'   => 'X1',
            'Comment'  => '1',
            'Win16'    => true,
            'Platform' => 'bcd',
        ];

        $sections = [
            'X1' => [
                'Comment'  => '12',
                'Win16'    => false,
                'Platform' => 'bcd',
            ],
            'X2' => $section,
        ];

        $expectedAgents = [
            0 => [
                'properties' => [
                    'Comment'  => 1,
                    'Win16'    => true,
                    'Platform' => 'bcd',
                ],
            ],
        ];

        $mockExpander = $this->getMock('\Browscap\Data\Expander', ['trimProperty'], [], '', false);
        $mockExpander
            ->expects(self::any())
            ->method('trimProperty')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setExpander($mockExpander));

        $mockDivision = $this->getMock('\Browscap\Data\Division', ['getUserAgents'], [], '', false);
        $mockDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($expectedAgents));

        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            ['getDefaultProperties'],
            [],
            '',
            false
        );
        $mockCollection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($mockDivision));

        $mockFormatter = $this->getMock(
            '\Browscap\Formatter\PhpFormatter',
            ['formatPropertyName'],
            [],
            '',
            false
        );
        $mockFormatter
            ->expects(self::exactly(2))
            ->method('formatPropertyName')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        $map = [
            ['Comment', $this->object, true],
            ['Win16', $this->object, false],
            ['Platform', $this->object, true],
            ['Parent', $this->object, true],
        ];

        $mockFilter = $this->getMock('\Browscap\Filter\FullFilter', ['isOutputProperty'], [], '', false);
        $mockFilter
            ->expects(self::exactly(4))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        self::assertSame($this->object, $this->object->setFilter($mockFilter));

        self::assertSame($this->object, $this->object->renderSectionBody($section, $mockCollection, $sections));
        self::assertSame('Parent="X1"' . PHP_EOL . 'Comment="1"' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionBodyIfNotSilentWithDefaultPropertiesAsParent()
    {
        $this->object->setSilent(false);

        $section = [
            'Parent'   => 'DefaultProperties',
            'Comment'  => '1',
            'Win16'    => true,
            'Platform' => 'bcd',
        ];

        $sections = [
            'X2' => $section,
        ];

        $expectedAgents = [
            0 => [
                'properties' => [
                    'Comment'  => '12',
                    'Win16'    => true,
                    'Platform' => 'bcd',
                ],
            ],
        ];

        $mockExpander = $this->getMock('\Browscap\Data\Expander', ['trimProperty'], [], '', false);
        $mockExpander
            ->expects(self::any())
            ->method('trimProperty')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setExpander($mockExpander));

        $mockDivision = $this->getMock('\Browscap\Data\Division', ['getUserAgents'], [], '', false);
        $mockDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($expectedAgents));

        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            ['getDefaultProperties'],
            [],
            '',
            false
        );
        $mockCollection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($mockDivision));

        $mockFormatter = $this->getMock(
            '\Browscap\Formatter\PhpFormatter',
            ['formatPropertyName'],
            [],
            '',
            false
        );
        $mockFormatter
            ->expects(self::exactly(2))
            ->method('formatPropertyName')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        $map = [
            ['Comment', $this->object, true],
            ['Win16', $this->object, false],
            ['Platform', $this->object, true],
            ['Parent', $this->object, true],
        ];

        $mockFilter = $this->getMock('\Browscap\Filter\FullFilter', ['isOutputProperty'], [], '', false);
        $mockFilter
            ->expects(self::exactly(4))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        self::assertSame($this->object, $this->object->setFilter($mockFilter));

        self::assertSame($this->object, $this->object->renderSectionBody($section, $mockCollection, $sections));
        self::assertSame(
            'Parent="DefaultProperties"' . PHP_EOL . 'Comment="1"' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the body of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionBodyIfSilent()
    {
        $this->object->setSilent(true);

        $section = [
            'Test'   => 1,
            'isTest' => true,
            'abc'    => 'bcd',
        ];

        $mockCollection = $this->getMock('\Browscap\Data\DataCollection', [], [], '', false);

        self::assertSame($this->object, $this->object->renderSectionBody($section, $mockCollection));
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionFooterIfNotSilent()
    {
        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderSectionFooter());
        self::assertSame(PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionFooterIfSilent()
    {
        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->renderSectionFooter());
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one division
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderDivisionFooter()
    {
        self::assertSame($this->object, $this->object->renderDivisionFooter());
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer after all divisions
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderAllDivisionsFooter()
    {
        self::assertSame($this->object, $this->object->renderAllDivisionsFooter());
        self::assertSame('', file_get_contents($this->file));
    }
}
