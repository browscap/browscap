<?php

namespace BrowscapTest\Helper;

use Browscap\Helper\CollectionCreator;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class CollectionCreatorTest
 *
 * @package BrowscapTest\Helper
 */
class CollectionCreatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    public function setUp()
    {
        $this->logger = new Logger('browscapTest', array(new NullHandler()));
    }

    public function testCreateDataCollectionThrowsExceptionIfNoDataCollectionIsSet()
    {
        $this->setExpectedException('\LogicException', 'An instance of \\Browscap\\Generator\\DataCollection is required for this function. Please set it with setDataCollection');

        $creator = new CollectionCreator();
        $creator->createDataCollection('.');
    }

    public function testCreateDataCollectionThrowsExceptionOnInvalidDirectory()
    {
        $this->setExpectedException('\RunTimeException', 'File "./platforms.json" does not exist.');

        $mockCollection = $this->getMock('\\Browscap\\Generator\\DataCollection', array('getGenerationDate'), array(), '', false);
        $mockCollection->expects(self::any())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()))
        ;

        $creator = new CollectionCreator();
        $creator
            ->setLogger($this->logger)
            ->setDataCollection($mockCollection)
        ;
        $creator->createDataCollection('.');
    }

    public function testCreateDataCollection()
    {
        $mockCollection = $this->getMock(
            '\Browscap\Generator\DataCollection',
            array('addPlatformsFile', 'addSourceFile', 'addPlatformsFile'),
            array(),
            '',
            false
        );
        $mockCollection->expects(self::any())
            ->method('addPlatformsFile')
            ->will(self::returnSelf())
        ;
        $mockCollection->expects(self::any())
            ->method('addPlatformsFile')
            ->will(self::returnSelf())
        ;
        $mockCollection->expects(self::any())
            ->method('addSourceFile')
            ->will(self::returnSelf())
        ;

        $creator = new CollectionCreator();
        $creator
            ->setLogger($this->logger)
            ->setDataCollection($mockCollection)
        ;

        $result = $creator->createDataCollection(__DIR__ . '/../../fixtures');
        self::assertInstanceOf('\\Browscap\\Generator\\DataCollection', $result);
        self::assertSame($mockCollection, $result);
    }
}
