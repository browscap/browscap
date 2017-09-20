<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowscapTest\Helper;

use Browscap\Data\DataCollection;
use Browscap\Helper\CollectionCreator;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class CollectionCreatorTest
 *
 * @category   BrowscapTest
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class CollectionCreatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Browscap\Helper\CollectionCreator
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->logger = new Logger('browscapTest', [new NullHandler()]);
        $this->object = new CollectionCreator();
    }

    /**
     * tests throwing an exception while creating a data collaction if the collction class is not set before
     *
     * @group helper
     * @group sourcetest
     */
    public function testCreateDataCollectionThrowsExceptionIfNoDataCollectionIsSet() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('An instance of \Browscap\Data\DataCollection is required for this function. Please set it with setDataCollection');

        $this->object->createDataCollection('.');
    }

    /**
     * tests throwing an exception while creating a data collaction when a dir is invalid
     *
     * @group helper
     * @group sourcetest
     */
    public function testCreateDataCollectionThrowsExceptionOnInvalidDirectory() : void
    {
        $this->expectException('\RunTimeException');
        $this->expectExceptionMessage('File "./platforms.json" does not exist.');

        $collection = $this->getMockBuilder(DataCollection::class)
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
    public function testCreateDataCollection() : void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
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
        self::assertInstanceOf(DataCollection::class, $result);
        self::assertSame($collection, $result);
    }
}
