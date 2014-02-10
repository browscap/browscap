<?php

namespace BrowscapTest\Helper;

use Browscap\Helper\CollectionCreator;
class CollectionCreatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDataCollectionThrowsExceptionIfNoDataCollectionIsSet()
    {
        $this->setExpectedException('\LogicException', 'An instance of \\Browscap\\Generator\\DataCollection is required for this function. Please set it with setDataCollection');
        
        $creator = new CollectionCreator();
        $creator->createDataCollection('test-version', '.');
    }
    
    public function testCreateDataCollectionThrowsExceptionOnInvalidDirectory()
    {
        $this->setExpectedException('\RunTimeException', 'File "./platforms.json" does not exist.');
        
        $mockCollection = $this->getMock('\\Browscap\\Generator\\DataCollection', array('getGenerationDate'), array(), '', false);
        $mockCollection->expects($this->any())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()))
        ;
        
        $creator = new CollectionCreator();
        $creator->setDataCollection($mockCollection);
        $creator->createDataCollection('test-version', '.');
    }
    
    public function testCreateDataCollection()
    {
        $mockCollection = $this->getMock('\\Browscap\\Generator\\DataCollection', array('addPlatformsFile', 'addSourceFile'), array(), '', false);
        $mockCollection->expects($this->any())
            ->method('addPlatformsFile')
            ->will(self::returnValue(true))
        ;
        
        $mockCollection->expects($this->any())
            ->method('addSourceFile')
            ->will(self::returnValue(true))
        ;
        
        $creator = new CollectionCreator();
        $creator->setDataCollection($mockCollection);
        
        $result = $creator->createDataCollection('test-version', __DIR__ . '/../../fixtures');
        self::assertInstanceOf('\\Browscap\\Generator\\DataCollection', $result);
        self::assertSame($mockCollection, $result);
    }
}
