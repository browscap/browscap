<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\BuildGenerator;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class BuildGeneratorTest
 *
 * @package BrowscapTest\Generator
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

    public function setUp()
    {
        $this->logger   = new Logger('browscapTest', array(new NullHandler()));
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

    public function testSetGeneratorHelper()
    {
        $mock = $this->getMock('\\Browscap\\Helper\\Generator', array(), array(), '', false);

        $generator = new BuildGenerator('.', '.');
        self::assertSame($generator, $generator->setGeneratorHelper($mock));
    }

    public function testBuild()
    {
        $mockCollection = $this->getMock('\\Browscap\\Generator\\DataCollection', array('getGenerationDate'), array(), '', false);
        $mockCollection->expects(self::any())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()))
        ;

        $mockCreator = $this->getMock('\\Browscap\\Helper\\CollectionCreator', array('createDataCollection'), array(), '', false);
        $mockCreator->expects(self::any())
            ->method('createDataCollection')
            ->will(self::returnValue($mockCollection))
        ;

        $mockGenerator = $this->getMock('\\Browscap\\Helper\\Generator', array('setVersion', 'create'), array(), '', false);
        $mockGenerator->expects(self::any())
            ->method('setVersion')
            ->will(self::returnSelf())
        ;
        $mockGenerator->expects(self::any())
            ->method('create')
            ->will(self::returnValue('This is a test'))
        ;

        $mockParser = $this->getMock('\\Browscap\\Generator\\CollectionParser', array('parse', 'setLogger', 'getLogger'), array(), '', false);
        $mockParser->expects(self::any())
            ->method('parse')
            ->will(self::returnValue(array()))
        ;
        $mockParser->expects(self::any())
            ->method('setLogger')
            ->will(self::returnSelf())
        ;
        $mockParser->expects(self::any())
            ->method('getLogger')
            ->will(self::returnValue($this->logger))
        ;

        $buildDir = sys_get_temp_dir() . '/bcap-build-generator-test/';
        mkdir($buildDir);

        $generator = new BuildGenerator('.', $buildDir);
        self::assertSame($generator, $generator->setLogger($this->logger));
        self::assertSame($generator, $generator->setCollectionCreator($mockCreator));
        self::assertSame($generator, $generator->setCollectionParser($mockParser));
        self::assertSame($generator, $generator->setGeneratorHelper($mockGenerator));

        $generator->generateBuilds('test');
    }
}
