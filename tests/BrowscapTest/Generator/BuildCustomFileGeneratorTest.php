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
class BuildCustomFileGeneratorTest extends \PHPUnit_Framework_TestCase
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
     */
    public function testConstructFailsWithoutParameters()
    {
        $this->setExpectedException('\Exception', 'You must specify a resource folder');
        new BuildCustomFileGenerator(null, null);
    }

    /**
     * tests failing the build without build dir
     *
     * @group generator
     * @group sourcetest
     */
    public function testConstructFailsWithoutTheSecondParameter()
    {
        $this->setExpectedException('\Exception', 'You must specify a build folder');
        new BuildCustomFileGenerator('.', null);
    }

    /**
     * tests failing the build if the build dir does not exist
     *
     * @group generator
     * @group sourcetest
     */
    public function testConstructFailsIfTheDirDoesNotExsist()
    {
        $this->setExpectedException('\Exception', 'The directory "/dar" does not exist, or we cannot access it');
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
        $this->setExpectedException('\Exception', 'The path "' . __FILE__ . '" did not resolve to a directory');
        new BuildCustomFileGenerator(__FILE__, null);
    }

    /**
     * tests creating a generator instance
     *
     * @group generator
     * @group sourcetest
     */
    public function testConstructPassesIfAllDirsExist()
    {
        new BuildCustomFileGenerator('.', '.');
    }

    /**
     * tests setting and getting a logger
     *
     * @group generator
     * @group sourcetest
     */
    public function testSetLogger()
    {
        $mock = $this->getMock('\Monolog\Logger', [], [], '', false);

        $resourceFolder = __DIR__ . '/../../../resources/';
        $buildFolder    = __DIR__ . '/../../../build/browscap-ua-test-' . time();

        @mkdir($buildFolder, 0777, true);

        $generator = new BuildCustomFileGenerator($resourceFolder, $buildFolder);
        self::assertSame($generator, $generator->setLogger($mock));
        self::assertSame($mock, $generator->getLogger());
    }

    /**
     * tests running a build
     *
     * @group generator
     * @group sourcetest
     */
    public function testBuild()
    {
        $mockDivision = $this->getMock(
            '\Browscap\Data\Division',
            ['getUserAgents', 'getVersions'],
            [],
            '',
            false
        );
        $mockDivision
            ->expects(self::exactly(4))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => [
                            'properties' => [
                                'Parent'   => 'DefaultProperties',
                                'Browser'  => 'xyz',
                                'Version'  => '1.0',
                                'MajorBer' => '1',
                            ],
                            'userAgent'  => 'abc',
                        ],
                    ]
                )
            );
        $mockDivision
            ->expects(self::once())
            ->method('getVersions')
            ->will(self::returnValue([2]));

        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            ['getGenerationDate', 'getDefaultProperties', 'getDefaultBrowser', 'getDivisions', 'checkProperty'],
            [],
            '',
            false
        );
        $mockCollection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()));
        $mockCollection
            ->expects(self::exactly(2))
            ->method('getDefaultProperties')
            ->will(self::returnValue($mockDivision));
        $mockCollection
            ->expects(self::once())
            ->method('getDefaultBrowser')
            ->will(self::returnValue($mockDivision));
        $mockCollection
            ->expects(self::once())
            ->method('getDivisions')
            ->will(self::returnValue([$mockDivision]));
        $mockCollection
            ->expects(self::once())
            ->method('checkProperty')
            ->will(self::returnValue(true));

        $mockCreator = $this->getMock(
            '\Browscap\Helper\CollectionCreator',
            ['createDataCollection'],
            [],
            '',
            false
        );
        $mockCreator
            ->expects(self::any())
            ->method('createDataCollection')
            ->will(self::returnValue($mockCollection));

        $writerCollection = $this->getMock(
            '\Browscap\Writer\WriterCollection',
            [
                'fileStart',
                'renderHeader',
                'renderAllDivisionsHeader',
                'renderSectionHeader',
                'renderSectionBody',
                'fileEnd',
            ],
            [],
            '',
            false
        );
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
