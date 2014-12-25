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

use Browscap\Generator\BuildFullFileOnlyGenerator;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class BuildGeneratorTest
 *
 * @category   BrowscapTest
 * @package    Generator
 * @author     James Titcumb <james@asgrim.com>
 */
class BuildFullFileOnlyGeneratorTest extends \PHPUnit_Framework_TestCase
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
        new BuildFullFileOnlyGenerator(null, null);
    }

    public function testConstructFailsWithoutTheSecondParameter()
    {
        $this->setExpectedException('\Exception', 'You must specify a build folder');
        new BuildFullFileOnlyGenerator('.', null);
    }

    public function testConstructFailsIfTheDirDoesNotExsist()
    {
        $this->setExpectedException('\Exception', 'The directory "/dar" does not exist, or we cannot access it');
        new BuildFullFileOnlyGenerator('/dar', null);
    }

    public function testConstructFailsIfTheDirIsNotAnDirectory()
    {
        $this->setExpectedException('\Exception', 'The path "' . __FILE__ . '" did not resolve to a directory');
        new BuildFullFileOnlyGenerator(__FILE__, null);
    }

    public function testConstructPassesIfAllDirsExist()
    {
        new BuildFullFileOnlyGenerator('.', '.');
    }

    public function testSetLogger()
    {
        $mock = $this->getMock('\Monolog\Logger', array(), array(), '', false);

        $resourceFolder = __DIR__ . '/../../../resources/';
        $buildFolder    = __DIR__ . '/../../../build/browscap-ua-test-' . time();

        @mkdir($buildFolder, 0777, true);

        $generator = new BuildFullFileOnlyGenerator($resourceFolder, $buildFolder);
        self::assertSame($generator, $generator->setLogger($mock));
        self::assertSame($mock, $generator->getLogger());
    }

    public function testBuild()
    {
        $mockDivision = $this->getMock(
            '\Browscap\Data\Division',
            array('getUserAgents', 'getVersions'),
            array(),
            '',
            false
        );
        $mockDivision
            ->expects(self::never())
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
            ->expects(self::never())
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
            ->expects(self::never())
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

        // First, generate the INI files
        $resourceFolder = __DIR__ . '/../../../resources/';
        $buildFolder    = __DIR__ . '/../../../build/browscap-ua-test-' . time();

        @mkdir($buildFolder, 0777, true);

        $generator = new BuildFullFileOnlyGenerator($resourceFolder, $buildFolder);
        self::assertSame($generator, $generator->setLogger($this->logger));

        $generator->run('test');
    }
}
