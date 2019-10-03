<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Assert\InvalidArgumentException;
use Browscap\Data\DataCollection;
use Browscap\Data\DuplicateDataException;
use Browscap\Data\Factory\DataCollectionFactory;
use DateTimeImmutable;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class DataCollectionFactoryTest extends TestCase
{
    /**
     * @var DataCollectionFactory
     */
    private $object;

    public function setUp() : void
    {
        /** @var Logger $logger */
        $logger       = $this->createMock(Logger::class);
        $this->object = new DataCollectionFactory($logger);
    }

    /**
     * tests throwing an exception while creating a data collaction when a dir is invalid
     *
     * @throws \Assert\AssertionFailedException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testCreateDataCollectionThrowsExceptionOnInvalidDirectory() : void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerationDate'])
            ->getMock();

        $collection->expects(self::any())
            ->method('getGenerationDate')
            ->will(self::returnValue(new DateTimeImmutable()));

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File "./platforms.json" does not exist.');

        $this->object->createDataCollection('.');
    }

    /**
     * tests creating a data collection
     *
     * @throws \Assert\AssertionFailedException
     * @throws \ReflectionException
     */
    public function testCreateDataCollection() : void
    {
        $logger = $this->createMock(Logger::class);

        $collection = $this->getMockBuilder(DataCollection::class)
            ->setConstructorArgs([$logger])
            ->setMethods(['addPlatform', 'addDivision', 'addEngine', 'addDevice', 'addBrowser'])
            ->getMock();

        $collection->expects(self::any())
            ->method('addPlatform')
            ->will(self::returnSelf());
        $collection->expects(self::any())
            ->method('addEngine')
            ->will(self::returnSelf());
        $collection->expects(self::any())
            ->method('addDevice')
            ->will(self::returnSelf());
        $collection->expects(self::any())
            ->method('addBrowser')
            ->will(self::returnSelf());
        $collection->expects(self::any())
            ->method('addDivision')
            ->will(self::returnSelf());

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $result = $this->object->createDataCollection(__DIR__ . '/../../../fixtures');

        self::assertInstanceOf(DataCollection::class, $result);
        self::assertSame($collection, $result);
    }

    /**
     * tests creating a data collection
     *
     * @throws \Assert\AssertionFailedException
     */
    public function testCreateDataCollectionFailsForDuplicateDeviceEntries() : void
    {
        $this->expectException(DuplicateDataException::class);
        $this->expectExceptionMessage('it was tried to add device "unknown", but this was already added before');

        $this->object->createDataCollection(
            __DIR__ . '/../../../fixtures/duplicate-device-entries'
        );
    }

    /**
     * tests creating a data collection
     *
     * @throws \Assert\AssertionFailedException
     */
    public function testCreateDataCollectionFailsForDuplicateBrowserEntries() : void
    {
        $this->expectException(DuplicateDataException::class);
        $this->expectExceptionMessage('it was tried to add browser "chrome", but this was already added before');

        $this->object->createDataCollection(
            __DIR__ . '/../../../fixtures/duplicate-browser-entries'
        );
    }
}
