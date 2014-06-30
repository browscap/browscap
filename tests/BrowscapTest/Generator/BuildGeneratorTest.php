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

        $buildDir = sys_get_temp_dir() . '/bcap-build-generator-test/';
        mkdir($buildDir);

        $generator = new BuildGenerator('.', $buildDir);
        self::assertSame($generator, $generator->setLogger($this->logger));
        self::assertSame($generator, $generator->setCollectionCreator($mockCreator));

        $generator->generateBuilds('test');
    }
}
