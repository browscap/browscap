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

use Browscap\Writer\JsonWriter;
use org\bovigo\vfs\vfsStream;

/**
 * Class JsonWriterTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class JsonWriterTest extends \PHPUnit_Framework_TestCase
{
    const STORAGE_DIR = 'storage';

    /**
     * @var \Browscap\Writer\JsonWriter
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
        $this->file = vfsStream::url(self::STORAGE_DIR) . DIRECTORY_SEPARATOR . 'test.json';

        $this->object = new JsonWriter($this->file);
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
        $logger = $this->getMockBuilder(\Monolog\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        self::assertSame($this->object, $this->object->setLogger($logger));
        self::assertSame($logger, $this->object->getLogger());
    }

    /**
     * tests getting the writer type
     *
     * @group writer
     * @group sourcetest
     */
    public function testGetType()
    {
        self::assertSame('json', $this->object->getType());
    }

    /**
     * tests setting and getting a formatter
     *
     * @group writer
     * @group sourcetest
     */
    public function testSetGetFormatter()
    {
        $mockFormatter = $this->getMockBuilder(\Browscap\Formatter\JsonFormatter::class)
            ->disableOriginalConstructor()
            ->getMock();

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
        $mockFilter = $this->getMockBuilder(\Browscap\Filter\StandardFilter::class)
            ->disableOriginalConstructor()
            ->getMock();

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
    public function testFileStartIfNotSilent()
    {
        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->fileStart());
        self::assertSame(
            '{' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the start of the file
     *
     * @group writer
     * @group sourcetest
     */
    public function testFileStartIfSilent()
    {
        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->fileStart());
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the end of the file
     *
     * @group writer
     * @group sourcetest
     */
    public function testFileEndIfNotSilent()
    {
        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->fileEnd());
        self::assertSame('}' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the end of the file
     *
     * @group writer
     * @group sourcetest
     */
    public function testFileEndIfSilent()
    {
        $this->object->setSilent(true);

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
        $logger = $this->getMockBuilder(\Monolog\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->object->setLogger($logger);

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
        $logger = $this->getMockBuilder(\Monolog\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->object->setLogger($logger);

        $header = ['TestData to be renderd into the Header'];

        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderHeader($header));
        self::assertSame(
            '  "comments": [' . PHP_EOL . '    "TestData to be renderd into the Header"' . PHP_EOL . '  ],'
            . PHP_EOL,
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
        $logger = $this->getMockBuilder(\Monolog\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->object->setLogger($logger);

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
        $logger = $this->getMockBuilder(\Monolog\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->object->setLogger($logger);

        $version = [
            'version' => 'test',
            'released' => date('Y-m-d'),
            'format' => 'TEST',
            'type' => 'full',

        ];

        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderVersion($version));
        self::assertSame(
            '  "GJK_Browscap_Version": {' . PHP_EOL . '    "Version": "test",' . PHP_EOL
            . '    "Released": "' . date('Y-m-d') . '"' . PHP_EOL . '  },' . PHP_EOL,
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
        $logger = $this->getMockBuilder(\Monolog\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->object->setLogger($logger);

        $version = [];

        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderVersion($version));
        self::assertSame(
            '  "GJK_Browscap_Version": {' . PHP_EOL . '    "Version": "0",' . PHP_EOL
            . '    "Released": ""' . PHP_EOL . '  },' . PHP_EOL,
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
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        self::assertSame($this->object, $this->object->renderAllDivisionsHeader($collection));
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one division
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderDivisionHeader()
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

        $mockFormatter = $this->getMockBuilder(\Browscap\Formatter\JsonFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(self::once())
            ->method('formatPropertyName')
            ->will(self::returnValue('test'));

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        self::assertSame($this->object, $this->object->renderSectionHeader('test'));
        self::assertSame('  test: ', file_get_contents($this->file));
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
            'Test'   => 1,
            'isTest' => true,
            'abc'    => 'bcd',
        ];

        $expectedAgents = [
            0 => [
                'properties' => [
                    'Test' => 'abc',
                    'abc'  => true,
                ],
            ],
        ];

        $mockExpander = $this->getMockBuilder(\Browscap\Data\Expander::class)
            ->disableOriginalConstructor()
            ->setMethods(['trimProperty'])
            ->getMock();

        $mockExpander
            ->expects(self::any())
            ->method('trimProperty')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setExpander($mockExpander));

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($expectedAgents));

        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $mockFormatter = $this->getMockBuilder(\Browscap\Formatter\JsonFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName', 'formatPropertyValue'])
            ->getMock();

        $mockFormatter
            ->expects(self::never())
            ->method('formatPropertyName')
            ->will(self::returnArgument(0));
        $mockFormatter
            ->expects(self::once())
            ->method('formatPropertyValue')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        $mockFilter = $this->getMockBuilder(\Browscap\Filter\StandardFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $map        = [
            ['Test', $this->object, true],
            ['isTest', $this->object, false],
            ['abc', $this->object, true],
        ];

        $mockFilter
            ->expects(self::exactly(2))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        self::assertSame($this->object, $this->object->setFilter($mockFilter));

        self::assertSame($this->object, $this->object->renderSectionBody($section, $collection));
        self::assertSame(
            '{"Test":1,"abc":"bcd"}',
            file_get_contents($this->file)
        );
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

        $mockExpander = $this->getMockBuilder(\Browscap\Data\Expander::class)
            ->disableOriginalConstructor()
            ->setMethods(['trimProperty'])
            ->getMock();

        $mockExpander
            ->expects(self::any())
            ->method('trimProperty')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setExpander($mockExpander));

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($expectedAgents));

        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $mockFormatter = $this->getMockBuilder(\Browscap\Formatter\JsonFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName', 'formatPropertyValue'])
            ->getMock();

        $mockFormatter
            ->expects(self::never())
            ->method('formatPropertyName')
            ->will(self::returnArgument(0));
        $mockFormatter
            ->expects(self::once())
            ->method('formatPropertyValue')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        $map = [
            ['Comment', $this->object, true],
            ['Win16', $this->object, false],
            ['Platform', $this->object, true],
            ['Parent', $this->object, true],
        ];

        $mockFilter = $this->getMockBuilder(\Browscap\Filter\StandardFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $mockFilter
            ->expects(self::exactly(4))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        self::assertSame($this->object, $this->object->setFilter($mockFilter));

        self::assertSame($this->object, $this->object->renderSectionBody($section, $collection, $sections));
        self::assertSame(
            '{"Parent":"X1","Comment":"1"}',
            file_get_contents($this->file)
        );
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

        $mockExpander = $this->getMockBuilder(\Browscap\Data\Expander::class)
            ->disableOriginalConstructor()
            ->setMethods(['trimProperty'])
            ->getMock();

        $mockExpander
            ->expects(self::any())
            ->method('trimProperty')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setExpander($mockExpander));

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($expectedAgents));

        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $mockFormatter = $this->getMockBuilder(\Browscap\Formatter\JsonFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName', 'formatPropertyValue'])
            ->getMock();

        $mockFormatter
            ->expects(self::never())
            ->method('formatPropertyName')
            ->will(self::returnArgument(0));
        $mockFormatter
            ->expects(self::once())
            ->method('formatPropertyValue')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        $map = [
            ['Comment', $this->object, true],
            ['Win16', $this->object, false],
            ['Platform', $this->object, true],
            ['Parent', $this->object, true],
        ];

        $mockFilter = $this->getMockBuilder(\Browscap\Filter\StandardFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $mockFilter
            ->expects(self::exactly(4))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        self::assertSame($this->object, $this->object->setFilter($mockFilter));

        self::assertSame($this->object, $this->object->renderSectionBody($section, $collection, $sections));
        self::assertSame(
            '{"Parent":"DefaultProperties","Comment":"1"}',
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

        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        self::assertSame($this->object, $this->object->renderSectionBody($section, $collection));
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
        self::assertSame(',' . PHP_EOL, file_get_contents($this->file));
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
