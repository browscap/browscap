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

namespace BrowscapTest\Helper;

use Browscap\Helper\CollectionCreator;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class CollectionCreatorTest
 *
 * @category   BrowscapTest
 * @author     James Titcumb <james@asgrim.com>
 */
class CollectionCreatorTest extends \PHPUnit\Framework\TestCase
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
        $this->logger = new Logger('browscapTest', [new NullHandler()]);
        $this->object = new CollectionCreator();
    }

    /**
     * tests throwing an exception while creating a data collaction if the collction class is not set before
     *
     * @group helper
     * @group sourcetest
     * @expectedException \LogicException
     * @expectedExceptionMessage An instance of \Browscap\Data\DataCollection is required for this function. Please set it with setDataCollection
     */
    public function testCreateDataCollectionThrowsExceptionIfNoDataCollectionIsSet()
    {
        $this->object->createDataCollection('.');
    }

    /**
     * tests throwing an exception while creating a data collaction when a dir is invalid
     *
     * @group helper
     * @group sourcetest
     *
     * @expectedException \RunTimeException
     * @expectedExceptionMessage File "./devices.json" does not exist.
     */
    public function testCreateDataCollectionThrowsExceptionOnInvalidDirectory()
    {
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerationDate'])
            ->getMock();

        $collection->expects(self::any())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()));

        $this->object
            ->setLogger($this->logger)
            ->setDataCollection($collection);
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
        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['addPlatformsFile', 'addSourceFile', 'addEnginesFile', 'addDevicesFile'])
            ->getMock();

        $collection->expects(self::any())
            ->method('addPlatformsFile')
            ->will(self::returnSelf());
        $collection->expects(self::any())
            ->method('addEnginesFile')
            ->will(self::returnSelf());
        $collection->expects(self::any())
            ->method('addDevicesFile')
            ->will(self::returnSelf());
        $collection->expects(self::any())
            ->method('addSourceFile')
            ->will(self::returnSelf());

        $this->object
            ->setLogger($this->logger)
            ->setDataCollection($collection);

        $result = $this->object->createDataCollection(__DIR__ . '/../../fixtures');
        self::assertInstanceOf(\Browscap\Data\DataCollection::class, $result);
        self::assertSame($collection, $result);
    }
}
