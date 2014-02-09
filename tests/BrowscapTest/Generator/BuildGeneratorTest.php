<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\BuildGenerator;
class BuildGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $messages = array();

    public function setUp()
    {
        $this->messages = array();
    }

    public function mockLog($level, $message)
    {
        $this->messages[] = array(
            'level' => $level,
            'message' => $message
        );
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
        $this->setExpectedException('\Exception', 'The directory \'/dar\' does not exist, or we cannot access it');
        new BuildGenerator('/dar', null);
    }

    public function testConstructFailsIfTheDirIsNotAnDirectory()
    {
        $this->setExpectedException('\Exception', 'The path \'' . __FILE__ . '\' did not resolve to a directory');
        new BuildGenerator(__FILE__, null);
    }

    public function testConstructPassesIfAllDirsExist()
    {
        new BuildGenerator('.', '.');
    }

    public function testSetLogger()
    {
        $mock = $this->getMock('\\Monolog\\Logger', array(), array(), '', false);

        $generator = new BuildGenerator('.', '.');
        self::assertSame($generator, $generator->setLogger($mock));
    }

    public function testSetCollectionCreator()
    {
        $mock = $this->getMock('\\Browscap\\Helper\\CollectionCreator', array(), array(), '', false);

        $generator = new BuildGenerator('.', '.');
        self::assertSame($generator, $generator->setCollectionCreator($mock));
    }

    public function testSetCollectionParser()
    {
        $mock = $this->getMock('\\Browscap\\Generator\\CollectionParser', array(), array(), '', false);

        $generator = new BuildGenerator('.', '.');
        self::assertSame($generator, $generator->setCollectionParser($mock));
    }

    public function testBuild()
    {
        $mockLogger = $this->getMock('\\Monolog\\Logger', array('log'), array(), '', false);
        $mockLogger->expects($this->any())
            ->method('log')
            ->will(self::returnCallback(array($this, 'mockLog')))
        ;

        $mockCreator = $this->getMock('\\Browscap\\Helper\\CollectionCreator', array('addPlatformsFile', 'addSourceFile'), array(), '', false);
        $mockCreator->expects($this->any())
            ->method('addPlatformsFile')
            ->will(self::returnValue(true))
        ;
        $mockCreator->expects($this->any())
            ->method('addSourceFile')
            ->will(self::returnValue(true))
        ;

        $mockParser = $this->getMock('\\Browscap\\Generator\\CollectionParser', array(), array(), '', false);

        $generator = new BuildGenerator('.', '.');
        self::assertSame($generator, $generator->setLogger($mockLogger));
        self::assertSame($generator, $generator->setCollectionCreator($mockCreator));
        self::assertSame($generator, $generator->setCollectionParser($mockParser));

        $generator->generateBuilds('test');
    }
}
