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

use Browscap\Writer\JsonWriter;
use org\bovigo\vfs\vfsStream;

/**
 * Class JsonWriterTest
 *
 * @category   BrowscapTest
 * @package    Writer
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
        $this->file = vfsStream::url(self::STORAGE_DIR) . DIRECTORY_SEPARATOR . 'test.json';

        $this->object = new JsonWriter($this->file);
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

    public function testGetType()
    {
        self::assertSame('json', $this->object->getType());
    }

    public function testSetGetFormatter()
    {
        $mockFormatter = $this->getMock('\Browscap\Formatter\JsonFormatter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));
        self::assertSame($mockFormatter, $this->object->getFormatter());
    }

    public function testSetGetFilter()
    {
        $mockFilter = $this->getMock('\Browscap\Filter\StandartFilter', array(), array(), '', false);

        self::assertSame($this->object, $this->object->setFilter($mockFilter));
        self::assertSame($mockFilter, $this->object->getFilter());
    }

    public function testSetGetSilent()
    {
        $silent = true;

        self::assertSame($this->object, $this->object->setSilent($silent));
        self::assertSame($silent, $this->object->isSilent());
    }

    public function testFileStartIfNotSilent()
    {
        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->fileStart());
        self::assertSame(
            '{' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    public function testFileStartIfSilent()
    {
        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->fileStart());
        self::assertSame('', file_get_contents($this->file));
    }

    public function testFileEndIfNotSilent()
    {
        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->fileEnd());
        self::assertSame('}' . PHP_EOL, file_get_contents($this->file));
    }

    public function testFileEndIfSilent()
    {
        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->fileEnd());
        self::assertSame('', file_get_contents($this->file));
    }

    public function testRenderHeaderIfSilent()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', array(), array(), '', false);
        $this->object->setLogger($mockLogger);

        $header = array('TestData to be renderd into the Header');

        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->renderHeader($header));
        self::assertSame('', file_get_contents($this->file));
    }

    public function testRenderHeaderIfNotSilent()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', array(), array(), '', false);
        $this->object->setLogger($mockLogger);

        $header = array('TestData to be renderd into the Header');

        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderHeader($header));
        self::assertSame(
            '    "comments": [' . PHP_EOL . '        "TestData to be renderd into the Header"' . PHP_EOL . '    ],'
            . PHP_EOL,
            file_get_contents($this->file)
        );
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
        self::assertSame(
            '    "GJK_Browscap_Version": {' . PHP_EOL . '        "Version": "test",' . PHP_EOL
            . '        "Released": "2014-08-12"' . PHP_EOL . '    },' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    public function testRenderVersionIfNotSilentButWithoutVersion()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', array(), array(), '', false);
        $this->object->setLogger($mockLogger);

        $version = array();

        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderVersion($version));
        self::assertSame(
            '    "GJK_Browscap_Version": {' . PHP_EOL . '        "Version": "0",' . PHP_EOL
            . '        "Released": ""' . PHP_EOL . '    },' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    public function testRenderAllDivisionsHeader()
    {
        $mockCollection = $this->getMock('\Browscap\Data\DataCollection', array(), array(), '', false);

        self::assertSame($this->object, $this->object->renderAllDivisionsHeader($mockCollection));
        self::assertSame('', file_get_contents($this->file));
    }

    public function testRenderDivisionHeader()
    {
        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->renderDivisionHeader('test'));
        self::assertSame('', file_get_contents($this->file));
    }

    public function testRenderSectionHeaderIfNotSilent()
    {
        $this->object->setSilent(false);

        $mockFormatter = $this->getMock(
            '\Browscap\Formatter\JsonFormatter',
            array('formatPropertyName'),
            array(),
            '',
            false
        );
        $mockFormatter
            ->expects(self::once())
            ->method('formatPropertyName')
            ->will(self::returnValue('test'))
        ;

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        self::assertSame($this->object, $this->object->renderSectionHeader('test'));
        self::assertSame('    "test": {' . PHP_EOL, file_get_contents($this->file));
    }

    public function testRenderSectionHeaderIfSilent()
    {
        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->renderSectionHeader('test'));
        self::assertSame('', file_get_contents($this->file));
    }

    public function testRenderSectionBodyIfNotSilent()
    {
        $this->object->setSilent(false);

        $section = array(
            'Test'   => 1,
            'isTest' => true,
            'abc'    => 'bcd'
        );

        $expectedAgents = array(
            0 => array(
                'properties' => array(
                    'Test' => 'abc',
                    'abc'  => true
                )
            )
        );

        $mockDivision = $this->getMock('\Browscap\Data\Division', array('getUserAgents'), array(), '', false);
        $mockDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($expectedAgents))
        ;

        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            array('getDefaultProperties'),
            array(),
            '',
            false
        );
        $mockCollection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($mockDivision))
        ;

        $mockFormatter = $this->getMock(
            '\Browscap\Formatter\JsonFormatter',
            array('formatPropertyName', 'formatPropertyValue'),
            array(),
            '',
            false
        );
        $mockFormatter
            ->expects(self::exactly(2))
            ->method('formatPropertyName')
            ->will(self::returnArgument(0))
        ;
        $mockFormatter
            ->expects(self::exactly(2))
            ->method('formatPropertyValue')
            ->will(self::returnArgument(0))
        ;

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        $mockFilter = $this->getMock('\Browscap\Filter\StandartFilter', array('isOutputProperty'), array(), '', false);
        $map        = array(
            array('Test', $this->object, true),
            array('isTest', $this->object, false),
            array('abc', $this->object, true),
        );

        $mockFilter
            ->expects(self::exactly(2))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map))
        ;

        self::assertSame($this->object, $this->object->setFilter($mockFilter));

        self::assertSame($this->object, $this->object->renderSectionBody($section, $mockCollection));
        self::assertSame(
            '        "Test": "1",' . PHP_EOL . '        "abc": "bcd"' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    public function testRenderSectionBodyIfNotSilentWithParents()
    {
        $this->object->setSilent(false);

        $section = array(
            'Parent'   => 'X1',
            'Comment'  => '1',
            'Win16'    => true,
            'Platform' => 'bcd'
        );

        $sections = array(
            'X1' => array(
                'Comment'  => '12',
                'Win16'    => false,
                'Platform' => 'bcd'
            ),
            'X2' => $section
        );

        $expectedAgents = array(
            0 => array(
                'properties' => array(
                    'Comment'  => 1,
                    'Win16'    => true,
                    'Platform' => 'bcd'
                )
            )
        );

        $mockDivision = $this->getMock('\Browscap\Data\Division', array('getUserAgents'), array(), '', false);
        $mockDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($expectedAgents))
        ;

        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            array('getDefaultProperties'),
            array(),
            '',
            false
        );
        $mockCollection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($mockDivision))
        ;

        $mockFormatter = $this->getMock(
            '\Browscap\Formatter\JsonFormatter',
            array('formatPropertyName'),
            array(),
            '',
            false
        );
        $mockFormatter
            ->expects(self::exactly(2))
            ->method('formatPropertyName')
            ->will(self::returnArgument(0))
        ;

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        $map = array(
            array('Comment', $this->object, true),
            array('Win16', $this->object, false),
            array('Platform', $this->object, true),
            array('Parent', $this->object, true),
        );

        $mockFilter = $this->getMock('\Browscap\Filter\StandartFilter', array('isOutputProperty'), array(), '', false);
        $mockFilter
            ->expects(self::exactly(4))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map))
        ;

        self::assertSame($this->object, $this->object->setFilter($mockFilter));

        self::assertSame($this->object, $this->object->renderSectionBody($section, $mockCollection, $sections));
        self::assertSame(
            '        "Parent": "X1",' . PHP_EOL . '        "Comment": "1"' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    public function testRenderSectionBodyIfNotSilentWithDefaultPropertiesAsParent()
    {
        $this->object->setSilent(false);

        $section = array(
            'Parent'   => 'DefaultProperties',
            'Comment'  => '1',
            'Win16'    => true,
            'Platform' => 'bcd'
        );

        $sections = array(
            'X2' => $section
        );

        $expectedAgents = array(
            0 => array(
                'properties' => array(
                    'Comment'  => '12',
                    'Win16'    => true,
                    'Platform' => 'bcd'
                )
            )
        );

        $mockDivision = $this->getMock('\Browscap\Data\Division', array('getUserAgents'), array(), '', false);
        $mockDivision
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue($expectedAgents))
        ;

        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            array('getDefaultProperties'),
            array(),
            '',
            false
        );
        $mockCollection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($mockDivision))
        ;

        $mockFormatter = $this->getMock(
            '\Browscap\Formatter\JsonFormatter',
            array('formatPropertyName'),
            array(),
            '',
            false
        );
        $mockFormatter
            ->expects(self::exactly(2))
            ->method('formatPropertyName')
            ->will(self::returnArgument(0))
        ;

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        $map = array(
            array('Comment', $this->object, true),
            array('Win16', $this->object, false),
            array('Platform', $this->object, true),
            array('Parent', $this->object, true),
        );

        $mockFilter = $this->getMock('\Browscap\Filter\StandartFilter', array('isOutputProperty'), array(), '', false);
        $mockFilter
            ->expects(self::exactly(4))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map))
        ;

        self::assertSame($this->object, $this->object->setFilter($mockFilter));

        self::assertSame($this->object, $this->object->renderSectionBody($section, $mockCollection, $sections));
        self::assertSame(
            '        "Parent": "DefaultProperties",' . PHP_EOL . '        "Comment": "1"' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    public function testRenderSectionBodyIfSilent()
    {
        $this->object->setSilent(true);

        $section = array(
            'Test'   => 1,
            'isTest' => true,
            'abc'    => 'bcd'
        );

        $mockCollection = $this->getMock('\Browscap\Data\DataCollection', array(), array(), '', false);

        self::assertSame($this->object, $this->object->renderSectionBody($section, $mockCollection));
        self::assertSame('', file_get_contents($this->file));
    }

    public function testRenderSectionFooterIfNotSilent()
    {
        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderSectionFooter());
        self::assertSame('    },' . PHP_EOL, file_get_contents($this->file));
    }

    public function testRenderSectionFooterIfSilent()
    {
        $this->object->setSilent(true);

        self::assertSame($this->object, $this->object->renderSectionFooter());
        self::assertSame('', file_get_contents($this->file));
    }

    public function testRenderDivisionFooter()
    {
        self::assertSame($this->object, $this->object->renderDivisionFooter());
        self::assertSame('', file_get_contents($this->file));
    }

    public function testRenderAllDivisionsFooter()
    {
        self::assertSame($this->object, $this->object->renderAllDivisionsFooter());
        self::assertSame('</browsercapitems>' . PHP_EOL, file_get_contents($this->file));
    }
}
