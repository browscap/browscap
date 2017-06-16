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

namespace BrowscapTest\Generator;

use Browscap\Generator\BuildCustomFileGenerator;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class BuildGeneratorTest
 *
 * @category   BrowscapTest
 * @author     James Titcumb <james@asgrim.com>
 */
class BuildCustomFileGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $messages = [];

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->logger   = new Logger('browscapTest', [new NullHandler()]);
        $this->messages = [];
    }

    /**
     * tests failing the build without parameters
     *
     * @group generator
     * @group sourcetest
     *
     * @expectedException \Exception
     * @expectedExceptionMessage You must specify a resource folder
     */
    public function testConstructFailsWithoutParameters()
    {
        new BuildCustomFileGenerator(null, null);
    }

    /**
     * tests failing the build without build dir
     *
     * @group generator
     * @group sourcetest
     *
     * @expectedException \Exception
     * @expectedExceptionMessage You must specify a build folder
     */
    public function testConstructFailsWithoutTheSecondParameter()
    {
        new BuildCustomFileGenerator('.', null);
    }

    /**
     * tests failing the build if the build dir does not exist
     *
     * @group generator
     * @group sourcetest
     *
     * @expectedException \Exception
     * @expectedExceptionMessage The directory "/dar" does not exist, or we cannot access it
     */
    public function testConstructFailsIfTheDirDoesNotExsist()
    {
        new BuildCustomFileGenerator('/dar', null);
    }

    /**
     * tests failing the build if no build dir is a file
     *
     * @group generator
     * @group sourcetest
     */
    public function testConstructFailsIfTheDirIsNotAnDirectory()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage('The path "' . __FILE__ . '" did not resolve to a directory');

        new BuildCustomFileGenerator(__FILE__, null);
    }

    /**
     * tests setting and getting a logger
     *
     * @group generator
     * @group sourcetest
     */
    public function testSetLogger()
    {
        $logger = $this->createMock(\Monolog\Logger::class);

        $resourceFolder = __DIR__ . '/../../../resources/';
        $buildFolder    = __DIR__ . '/../../../build/browscap-ua-test-' . time();

        @mkdir($buildFolder, 0777, true);

        $generator = new BuildCustomFileGenerator($resourceFolder, $buildFolder);
        self::assertSame($generator, $generator->setLogger($logger));
        self::assertSame($logger, $generator->getLogger());
    }

    /**
     * tests running a build
     *
     * @group generator
     * @group sourcetest
     */
    public function testBuild()
    {
        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();

        $division
            ->expects(self::exactly(4))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => [
                            'properties' => [
                                'Parent' => 'DefaultProperties',
                                'Browser' => 'xyz',
                                'Version' => '1.0',
                                'MajorBer' => '1',
                            ],
                            'userAgent' => 'abc',
                        ],
                    ]
                )
            );
        $division
            ->expects(self::once())
            ->method('getVersions')
            ->will(self::returnValue([2]));

        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerationDate', 'getDefaultProperties', 'getDefaultBrowser', 'getDivisions', 'checkProperty'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()));
        $collection
            ->expects(self::exactly(2))
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDefaultBrowser')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDivisions')
            ->will(self::returnValue([$division]));
        $collection
            ->expects(self::once())
            ->method('checkProperty')
            ->will(self::returnValue(true));

        $mockCreator = $this->getMockBuilder(\Browscap\Helper\CollectionCreator::class)
            ->disableOriginalConstructor()
            ->setMethods(['createDataCollection'])
            ->getMock();

        $mockCreator
            ->expects(self::any())
            ->method('createDataCollection')
            ->will(self::returnValue($collection));

        $writerCollection = $this->getMockBuilder(\Browscap\Writer\WriterCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'fileStart',
                'renderHeader',
                'renderAllDivisionsHeader',
                'renderSectionHeader',
                'renderSectionBody',
                'fileEnd',
            ])
            ->getMock();

        $writerCollection
            ->expects(self::once())
            ->method('fileStart')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::once())
            ->method('renderHeader')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::once())
            ->method('renderAllDivisionsHeader')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::exactly(3))
            ->method('renderSectionHeader')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::exactly(3))
            ->method('renderSectionBody')
            ->will(self::returnSelf());
        $writerCollection
            ->expects(self::once())
            ->method('fileEnd')
            ->will(self::returnSelf());

        // First, generate the INI files
        $resourceFolder = __DIR__ . '/../../../resources/';
        $buildFolder    = __DIR__ . '/../../../build/browscap-ua-test-' . time();

        @mkdir($buildFolder, 0777, true);

        $generator = new BuildCustomFileGenerator($resourceFolder, $buildFolder);
        self::assertSame($generator, $generator->setLogger($this->logger));
        self::assertSame($generator, $generator->setCollectionCreator($mockCreator));
        self::assertSame($generator, $generator->setWriterCollection($writerCollection));

        $generator->run('test');
    }
}
