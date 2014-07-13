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
 * @package    Generator
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Generator;

use Browscap\Generator\BuildGenerator;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class BuildGeneratorTest
 *
 * @category   BrowscapTest
 * @package    Generator
 * @author     James Titcumb <james@asgrim.com>
 */
class BuildGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $messages = array();

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    public function setUp()
    {
        $this->logger   = new Logger('browscapTest', array(new NullHandler()));
        $this->messages = array();
    }

    public function testConstructFailsWithoutParameters()
    {
        $this->setExpectedException('\Exception', 'You must specify a resource folder');
        new BuildGenerator(null, null);
    }

    public function testConstructFailsWithoutTheSecondParameter()
    {
        $this->setExpectedException('\Exception', 'You must specify a build folder');
        new BuildGenerator('.', null);
    }

    public function testConstructFailsIfTheDirDoesNotExsist()
    {
        $this->setExpectedException('\Exception', 'The directory "/dar" does not exist, or we cannot access it');
        new BuildGenerator('/dar', null);
    }

    public function testConstructFailsIfTheDirIsNotAnDirectory()
    {
        $this->setExpectedException('\Exception', 'The path "' . __FILE__ . '" did not resolve to a directory');
        new BuildGenerator(__FILE__, null);
    }

    public function testConstructPassesIfAllDirsExist()
    {
        new BuildGenerator('.', '.');
    }

    public function testSetLogger()
    {
        $mock = $this->getMock('\Monolog\Logger', array(), array(), '', false);

        $generator = new BuildGenerator('.', '.');
        self::assertSame($generator, $generator->setLogger($mock));
    }

    public function testSetCollectionCreator()
    {
        $mock = $this->getMock('\Browscap\Helper\CollectionCreator', array(), array(), '', false);

        $generator = new BuildGenerator('.', '.');
        self::assertSame($generator, $generator->setCollectionCreator($mock));
    }

    public function testBuild()
    {
        $mockDivision = $this->getMock('\Browscap\Data\Division', array('getUserAgents', 'getVersions'), array(), '', false);
        $mockDivision
            ->expects(self::exactly(5))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    array(
                        0 => array(
                            'properties' => array(
                                'Parent'   => 'DefaultProperties',
                                'Browser'  => 'xyz',
                                'Version'  => '1.0',
                                'MajorBer' => '1',
                            ),
                            'userAgent'  => 'abc'
                        )
                    )
                )
            )
        ;
        $mockDivision
            ->expects(self::once())
            ->method('getVersions')
            ->will(self::returnValue(array(2)))
        ;

        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            array('getGenerationDate', 'getDefaultProperties', 'getDefaultBrowser', 'getDivisions', 'checkProperty'),
            array(),
            '',
            false
        );
        $mockCollection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()))
        ;
        $mockCollection
            ->expects(self::exactly(2))
            ->method('getDefaultProperties')
            ->will(self::returnValue($mockDivision))
        ;
        $mockCollection
            ->expects(self::once())
            ->method('getDefaultBrowser')
            ->will(self::returnValue($mockDivision))
        ;
        $mockCollection
            ->expects(self::once())
            ->method('getDivisions')
            ->will(self::returnValue(array($mockDivision)))
        ;
        $mockCollection
            ->expects(self::once())
            ->method('checkProperty')
            ->will(self::returnValue(true))
        ;

        $mockCreator = $this->getMock(
            '\Browscap\Helper\CollectionCreator',
            array('createDataCollection'),
            array(),
            '',
            false
        );
        $mockCreator
            ->expects(self::any())
            ->method('createDataCollection')
            ->will(self::returnValue($mockCollection))
        ;

        $writerCollection = $this->getMock(
            '\Browscap\Writer\WriterCollection',
            array(
                'fileStart',
                'renderHeader',
                'renderAllDivisionsHeader',
                'renderSectionHeader',
                'renderSectionBody',
                'fileEnd',
            ),
            array(),
            '',
            false
        );
        $writerCollection
            ->expects(self::once())
            ->method('fileStart')
            ->will(self::returnSelf())
        ;
        $writerCollection
            ->expects(self::once())
            ->method('renderHeader')
            ->will(self::returnSelf())
        ;
        $writerCollection
            ->expects(self::once())
            ->method('renderAllDivisionsHeader')
            ->will(self::returnSelf())
        ;
        $writerCollection
            ->expects(self::exactly(3))
            ->method('renderSectionHeader')
            ->will(self::returnSelf())
        ;
        $writerCollection
            ->expects(self::exactly(3))
            ->method('renderSectionBody')
            ->will(self::returnSelf())
        ;
        $writerCollection
            ->expects(self::once())
            ->method('fileEnd')
            ->will(self::returnSelf())
        ;

        $generator = new BuildGenerator('.', '.');
        self::assertSame($generator, $generator->setLogger($this->logger));
        self::assertSame($generator, $generator->setCollectionCreator($mockCreator));
        self::assertSame($generator, $generator->setWriterCollection($writerCollection));

        $generator->run('test');
    }

    public function testBuildWithoutZip()
    {
        $mockDivision = $this->getMock('\Browscap\Data\Division', array('getUserAgents', 'getVersions'), array(), '', false);
        $mockDivision
            ->expects(self::exactly(5))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    array(
                        0 => array(
                            'properties' => array(
                                'Parent'   => 'DefaultProperties',
                                'Browser'  => 'xyz',
                                'Version'  => '1.0',
                                'MajorBer' => '1',
                            ),
                            'userAgent'  => 'abc'
                        )
                    )
                )
            )
        ;
        $mockDivision
            ->expects(self::once())
            ->method('getVersions')
            ->will(self::returnValue(array(2)))
        ;

        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            array('getGenerationDate', 'getDefaultProperties', 'getDefaultBrowser', 'getDivisions', 'checkProperty'),
            array(),
            '',
            false
        );
        $mockCollection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()))
        ;
        $mockCollection
            ->expects(self::exactly(2))
            ->method('getDefaultProperties')
            ->will(self::returnValue($mockDivision))
        ;
        $mockCollection
            ->expects(self::once())
            ->method('getDefaultBrowser')
            ->will(self::returnValue($mockDivision))
        ;
        $mockCollection
            ->expects(self::once())
            ->method('getDivisions')
            ->will(self::returnValue(array($mockDivision)))
        ;
        $mockCollection
            ->expects(self::once())
            ->method('checkProperty')
            ->will(self::returnValue(true))
        ;

        $mockCreator = $this->getMock(
            '\Browscap\Helper\CollectionCreator',
            array('createDataCollection'),
            array(),
            '',
            false
        );
        $mockCreator
            ->expects(self::any())
            ->method('createDataCollection')
            ->will(self::returnValue($mockCollection))
        ;

        $writerCollection = $this->getMock(
            '\Browscap\Writer\WriterCollection',
            array(
                'fileStart',
                'renderHeader',
                'renderAllDivisionsHeader',
                'renderSectionHeader',
                'renderSectionBody',
                'fileEnd',
            ),
            array(),
            '',
            false
        );
        $writerCollection
            ->expects(self::once())
            ->method('fileStart')
            ->will(self::returnSelf())
        ;
        $writerCollection
            ->expects(self::once())
            ->method('renderHeader')
            ->will(self::returnSelf())
        ;
        $writerCollection
            ->expects(self::once())
            ->method('renderAllDivisionsHeader')
            ->will(self::returnSelf())
        ;
        $writerCollection
            ->expects(self::exactly(3))
            ->method('renderSectionHeader')
            ->will(self::returnSelf())
        ;
        $writerCollection
            ->expects(self::exactly(3))
            ->method('renderSectionBody')
            ->will(self::returnSelf())
        ;
        $writerCollection
            ->expects(self::once())
            ->method('fileEnd')
            ->will(self::returnSelf())
        ;

        $generator = new BuildGenerator('.', '.');
        self::assertSame($generator, $generator->setLogger($this->logger));
        self::assertSame($generator, $generator->setCollectionCreator($mockCreator));
        self::assertSame($generator, $generator->setWriterCollection($writerCollection));

        $generator->run('test', false);
    }
}
