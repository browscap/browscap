<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   BrowscapTest
 * @copyright  1998-2017 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Writer;

use Browscap\Writer\CsvWriter;
use org\bovigo\vfs\vfsStream;

/**
 * Class CsvWriterTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class CsvWriterTest extends \PHPUnit\Framework\TestCase
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

        $this->object = new CsvWriter($this->file);
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
        $logger = $this->createMock(\Monolog\Logger::class);

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
        self::assertSame('csv', $this->object->getType());
    }

    /**
     * tests setting and getting a formatter
     *
     * @group writer
     * @group sourcetest
     */
    public function testSetGetFormatter()
    {
        $mockFormatter = $this->createMock(\Browscap\Formatter\CsvFormatter::class);

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
        $mockFilter = $this->createMock(\Browscap\Filter\FullFilter::class);

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
    public function testRenderHeader()
    {
        $header = ['TestData to be renderd into the Header'];

        self::assertSame($this->object, $this->object->renderHeader($header));
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the version information
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderVersionIfSilent()
    {
        $logger = $this->createMock(\Monolog\Logger::class);
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
        $logger = $this->createMock(\Monolog\Logger::class);
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
            '"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL . '"test","' . date('Y-m-d') . '"' . PHP_EOL,
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
        $logger = $this->createMock(\Monolog\Logger::class);
        $this->object->setLogger($logger);

        $version = [];

        $this->object->setSilent(false);

        self::assertSame($this->object, $this->object->renderVersion($version));
        self::assertSame(
            '"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL . '"0",""' . PHP_EOL,
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
        $expectedAgents = [
            0 => [
                'properties' => [
                    'Test' => 1,
                    'isTest' => true,
                ],
            ],
        ];

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

        $mockFormatter = $this->getMockBuilder(\Browscap\Formatter\CsvFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(self::once())
            ->method('formatPropertyName')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        $map = [
            ['Test', $this->object, true],
            ['isTest', $this->object, false],
        ];

        $mockFilter = $this->getMockBuilder(\Browscap\Filter\FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $mockFilter
            ->expects(self::exactly(6))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        self::assertSame($this->object, $this->object->setFilter($mockFilter));

        self::assertSame($this->object, $this->object->renderAllDivisionsHeader($collection));
        self::assertSame('Test' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the header for all division
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderAllDivisionsHeaderWithoutProperties()
    {
        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([]));

        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

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
        self::assertSame($this->object, $this->object->renderDivisionHeader('test'));
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionHeader()
    {
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
            'Test' => 1,
            'isTest' => true,
            'abc' => 'bcd',
        ];

        $expectedAgents = [
            0 => [
                'properties' => [
                    'Test' => 'abc',
                    'abc' => true,
                    'alpha' => true,
                ],
            ],
        ];

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

        $mockFormatter = $this->getMockBuilder(\Browscap\Formatter\CsvFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyValue'])
            ->getMock();

        $mockFormatter
            ->expects(self::exactly(3))
            ->method('formatPropertyValue')
            ->will(self::returnArgument(0));

        self::assertSame($this->object, $this->object->setFormatter($mockFormatter));

        $mockFilter = $this->getMockBuilder(\Browscap\Filter\FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $map        = [
            ['Test', $this->object, true],
            ['isTest', $this->object, false],
            ['abc', $this->object, true],
            ['alpha', $this->object, true],
        ];

        $mockFilter
            ->expects(self::exactly(7))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        self::assertSame($this->object, $this->object->setFilter($mockFilter));

        $logger = $this->createMock(\Monolog\Logger::class);
        $this->object->setLogger($logger);

        self::assertSame($this->object, $this->object->renderSectionBody($section, $collection));
        self::assertSame('1,bcd,' . PHP_EOL, file_get_contents($this->file));
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
            'Test' => 1,
            'isTest' => true,
            'abc' => 'bcd',
        ];

        $collection = $this->createMock(\Browscap\Data\DataCollection::class);

        self::assertSame($this->object, $this->object->renderSectionBody($section, $collection));
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one section
     *
     * @group writer
     * @group sourcetest
     */
    public function testRenderSectionFooter()
    {
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
