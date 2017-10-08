<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Browscap\Data\DataCollection;
use Browscap\Data\Factory\DataCollectionFactory;
use Monolog\Logger;
use RunTimeException;

/**
 * Class CollectionCreatorTestTest
 */
class DataCollectionFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Factory\DataCollectionFactory
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        self::markTestSkipped();
        $logger       = $this->createMock(Logger::class);
        $this->object = new DataCollectionFactory($logger);
    }

    /**
     * tests throwing an exception while creating a data collaction when a dir is invalid
     *
     * @group helper
     * @group sourcetest
     */
    public function testCreateDataCollectionThrowsExceptionOnInvalidDirectory() : void
    {
        $this->expectException(RunTimeException::class);
        $this->expectExceptionMessage('File "./platforms.json" does not exist.');

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerationDate'])
            ->getMock();

        $collection->expects(self::any())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTimeImmutable()));

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

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
        $logger = $this->createMock(Logger::class);

        $collection = $this->getMockBuilder(DataCollection::class)
            ->setConstructorArgs([$logger])
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

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $result = $this->object->createDataCollection(__DIR__ . '/../../fixtures');

        self::assertInstanceOf(DataCollection::class, $result);
        self::assertSame($collection, $result);
    }
}
