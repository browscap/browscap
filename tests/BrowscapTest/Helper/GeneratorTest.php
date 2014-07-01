<?php

namespace BrowscapTest\Helper;

use Browscap\Helper\Generator;
use Monolog\Logger;
use Monolog\Handler\NullHandler;

/**
 * Class GeneratorTest
 *
 * @package BrowscapTest\Helper
 */
class GeneratorTest extends \PHPUnit_Framework_TestCase
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
        $this->markTestSkipped('need to be updated');

        $this->messages = array();
        $this->logger   = new Logger('browscapTest', array(new NullHandler()));
    }

    public function mockLog($level, $message)
    {
        $this->messages[] = array(
            'level' => $level,
            'message' => $message
        );
    }

    public function testSetGenerator()
    {
        $mockGenerator = $this->getMock('\\Browscap\\Generator\\AbstractGenerator', array(), array(), '', false);

        $generator = new Generator();
        self::assertSame($generator, $generator->setGenerator($mockGenerator));
    }

    public function testSetVersion()
    {
        $version = 'test-version';

        $generator = new Generator();
        self::assertSame($generator, $generator->setVersion($version));
        self::assertSame($version, $generator->getVersion());
    }

    public function testSetResourceFolder()
    {
        $folder = '.';

        $generator = new Generator();
        self::assertSame($generator, $generator->setResourceFolder($folder));
        self::assertSame($folder, $generator->getResourceFolder());
    }

    public function testSetCollectionCreator()
    {
        $mockCreator = $this->getMock('\\Browscap\\Helper\\CollectionCreator', array(), array(), '', false);

        $generator = new Generator();
        self::assertSame($generator, $generator->setCollectionCreator($mockCreator));
    }

    public function testSetCollectionParser()
    {
        $mockParser = $this->getMock('\\Browscap\\Generator\\CollectionParser', array(), array(), '', false);

        $generator = new Generator();
        self::assertSame($generator, $generator->setCollectionParser($mockParser));
    }

    public function testCreateCollectionThrowsExceptionIfNoCreatorIsSet()
    {
        $this->setExpectedException('\LogicException', 'An instance of \\Browscap\\Helper\\CollectionCreator is required for this function. Please set it with setCollectionCreator');

        $generator = new Generator();
        self::assertSame($generator, $generator->setLogger($this->logger));
        $generator->createCollection();
    }

    public function testCreateCollection()
    {
        $mockCollection = $this->getMock('\\Browscap\\Generator\\DataCollection', array(), array(), '', false);

        $mockCreator = $this->getMock('\\Browscap\\Helper\\CollectionCreator', array('setDataCollection', 'createDataCollection', 'setLogger'), array(), '', false);
        $mockCreator->expects(self::any())
            ->method('setDataCollection')
            ->will(self::returnSelf())
        ;
        $mockCreator->expects(self::any())
            ->method('createDataCollection')
            ->will(self::returnValue($mockCollection))
        ;
        $mockCreator->expects(self::any())
            ->method('setLogger')
            ->will(self::returnSelf())
        ;

        $generator = new Generator();
        self::assertSame($generator, $generator->setLogger($this->logger));
        self::assertSame($generator, $generator->setCollectionCreator($mockCreator));
        self::assertSame($generator, $generator->createCollection());
    }

    public function testParseCollectionThrowsExceptionIfNoCreatorIsSet()
    {
        $this->setExpectedException('\LogicException', 'An instance of \\Browscap\\Generator\\CollectionParser is required for this function. Please set it with setCollectionParser');

        $generator = new Generator();
        self::assertSame($generator, $generator->setLogger($this->logger));
        $generator->parseCollection();
    }

    public function testParseCollection()
    {
        $mockCollection = $this->getMock('\\Browscap\\Generator\\DataCollection', array(), array(), '', false);

        $mockCreator = $this->getMock('\\Browscap\\Helper\\CollectionCreator', array('createDataCollection', 'setDataCollection', 'setLogger', 'getLogger'), array(), '', false);
        $mockCreator->expects(self::any())
            ->method('createDataCollection')
            ->will(self::returnValue($mockCollection))
        ;
        $mockCreator->expects(self::any())
            ->method('setDataCollection')
            ->will(self::returnSelf())
        ;
        $mockCreator->expects(self::any())
            ->method('setLogger')
            ->will(self::returnSelf())
        ;
        $mockCreator->expects(self::any())
            ->method('getLogger')
            ->will(self::returnValue($this->logger))
        ;

        $mockParser = $this->getMock('\\Browscap\\Generator\\CollectionParser', array('setLogger', 'getLogger'), array(), '', false);
        $mockParser->expects(self::any())
            ->method('setLogger')
            ->will(self::returnSelf())
        ;
        $mockParser->expects(self::any())
            ->method('getLogger')
            ->will(self::returnValue($this->logger))
        ;

        $generator = new Generator();
        self::assertSame($generator, $generator->setLogger($this->logger));
        self::assertSame($generator, $generator->setCollectionCreator($mockCreator));
        self::assertSame($generator, $generator->setCollectionParser($mockParser));
        self::assertSame($generator, $generator->createCollection());
        self::assertSame($generator, $generator->parseCollection());
    }

    public function testCreateThrowsExceptionIfNoCreatorIsSet()
    {
        $this->setExpectedException('\LogicException', 'An instance of \\Browscap\\Generator\\AbstractGenerator is required for this function. Please set it with setGenerator');

        $generator = new Generator();
        self::assertSame($generator, $generator->setLogger($this->logger));
        $generator->create();
    }

    public function testCreate()
    {
        $mockCollection = $this->getMock('\\Browscap\\Generator\\DataCollection', array('getGenerationDate'), array(), '', false);
        $mockCollection->expects(self::any())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()))
        ;

        $mockCreator = $this->getMock('\\Browscap\\Helper\\CollectionCreator', array('createDataCollection', 'setDataCollection', 'setLogger', 'getLogger'), array(), '', false);
        $mockCreator->expects(self::any())
            ->method('createDataCollection')
            ->will(self::returnValue($mockCollection))
        ;
        $mockCreator->expects(self::any())
            ->method('setDataCollection')
            ->will(self::returnSelf())
        ;
        $mockCreator->expects(self::any())
            ->method('setLogger')
            ->will(self::returnSelf())
        ;
        $mockCreator->expects(self::any())
            ->method('getLogger')
            ->will(self::returnValue($this->logger))
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

        $mockGenerator = $this->getMock('\\Browscap\\Generator\\AbstractGenerator', array('setCollectionData', 'setComments', 'generate', 'setLogger', 'getLogger'), array(), '', false);
        $mockGenerator->expects(self::any())
            ->method('setCollectionData')
            ->will(self::returnSelf())
        ;
        $mockGenerator->expects(self::any())
            ->method('setComments')
            ->will(self::returnSelf())
        ;
        $mockGenerator->expects(self::any())
            ->method('generate')
            ->will(self::returnValue(''))
        ;
        $mockGenerator->expects(self::any())
            ->method('setLogger')
            ->will(self::returnSelf())
        ;
        $mockGenerator->expects(self::any())
            ->method('getLogger')
            ->will(self::returnValue($this->logger))
        ;

        $generator = new Generator();
        self::assertSame($generator, $generator->setLogger($this->logger));
        self::assertSame($generator, $generator->setGenerator($mockGenerator));
        self::assertSame($generator, $generator->setCollectionCreator($mockCreator));
        self::assertSame($generator, $generator->setCollectionParser($mockParser));
        self::assertSame($generator, $generator->createCollection());
        self::assertSame($generator, $generator->parseCollection());
        self::assertInternalType('string', $generator->create());
    }
}
