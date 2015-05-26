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
 * @package    Helper
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Helper;

use Browscap\Helper\CollectionCreator;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class CollectionCreatorTest
 *
 * @category   BrowscapTest
 * @package    Helper
 * @author     James Titcumb <james@asgrim.com>
 */
class CollectionCreatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @var \Browscap\Helper\CollectionCreator
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->logger = new Logger('browscapTest', array(new NullHandler()));
        $this->object = new CollectionCreator();
    }

    /**
     * tests throwing an exception while creating a data collaction if the collction class is not set before
     *
     * @group helper
     * @group sourcetest
     */
    public function testCreateDataCollectionThrowsExceptionIfNoDataCollectionIsSet()
    {
        $this->setExpectedException('\LogicException', 'An instance of \Browscap\Data\DataCollection is required for this function. Please set it with setDataCollection');

        $this->object->createDataCollection('.');
    }

    /**
     * tests throwing an exception while creating a data collaction when a dir is invalid
     *
     * @group helper
     * @group sourcetest
     */
    public function testCreateDataCollectionThrowsExceptionOnInvalidDirectory()
    {
        $this->setExpectedException('\RunTimeException', 'File "./devices.json" does not exist.');

        $mockCollection = $this->getMock('\Browscap\Data\DataCollection', array('getGenerationDate'), array(), '', false);
        $mockCollection->expects(self::any())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()))
        ;

        $this->object
            ->setLogger($this->logger)
            ->setDataCollection($mockCollection)
        ;
        $this->object->createDataCollection('.');
    }

    /**
     * tests creating a data collection
     *
     * @group helper
     * @group sourcetest
     */
    public function testCreateDataCollection()
    {
        $mockCollection = $this->getMock(
            '\Browscap\Data\DataCollection',
            array('addPlatformsFile', 'addSourceFile', 'addEnginesFile', 'addDevicesFile'),
            array(),
            '',
            false
        );
        $mockCollection->expects(self::any())
            ->method('addPlatformsFile')
            ->will(self::returnSelf())
        ;
        $mockCollection->expects(self::any())
            ->method('addEnginesFile')
            ->will(self::returnSelf())
        ;
        $mockCollection->expects(self::any())
            ->method('addDevicesFile')
            ->will(self::returnSelf())
        ;
        $mockCollection->expects(self::any())
            ->method('addSourceFile')
            ->will(self::returnSelf())
        ;

        $this->object
            ->setLogger($this->logger)
            ->setDataCollection($mockCollection)
        ;

        $result = $this->object->createDataCollection(__DIR__ . '/../../fixtures');
        self::assertInstanceOf('\Browscap\Data\DataCollection', $result);
        self::assertSame($mockCollection, $result);
    }
}
