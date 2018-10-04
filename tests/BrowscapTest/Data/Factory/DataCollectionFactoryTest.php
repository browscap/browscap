<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Browscap\Data\DataCollection;
use Browscap\Data\DuplicateDataException;
use Browscap\Data\Factory\DataCollectionFactory;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DataCollectionFactoryTest extends TestCase
{
    /**
     * @var DataCollectionFactory
     */
    private $object;

    /**
     * @throws \Exception
     */
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
            ->will(self::returnValue(new \DateTimeImmutable()));

        $property = new \ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Directory "./platforms" does not exist.');

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

        $result = $this->object->createDataCollection(__DIR__ . '/../../../fixtures/build-ok');

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

    /**
     * tests creating a data collection
     *
     * @throws \Assert\AssertionFailedException
     */
    public function testCreateDataCollectionFailsBecauseOfMissingFiles() : void
    {
        $path = __DIR__ . '/../../../fixtures/missing-file';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('File "%s/core/default-browser.json" does not exist.', $path));

        $this->object->createDataCollection($path);
    }

    /**
     * tests creating a data collection
     *
     * @throws \Assert\AssertionFailedException
     */
    public function testCreateDataCollectionFailsBecauseOfInvalidChars() : void
    {
        $path = __DIR__ . '/../../../fixtures/non-ascii-chars';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('File "%s/core/default-browser.json" contains Non-ASCII-Characters.', $path));

        $this->object->createDataCollection($path);
    }

    /**
     * tests creating a data collection
     *
     * @throws \Assert\AssertionFailedException
     */
    public function testCreateDataCollectionFailsBecauseOfInvalidJson() : void
    {
        $path = __DIR__ . '/../../../fixtures/invalid-json';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('File "%s/core/default-browser.json" had invalid JSON.', $path));

        $this->object->createDataCollection($path);
    }

    /**
     * tests creating a data collection
     *
     * @throws \Assert\AssertionFailedException
     */
    public function testCreateDataCollectionFailsBecauseOfEmptyDirectory() : void
    {
        $path = __DIR__ . '/../../../fixtures/empty-directory';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Directory "%s/browsers" was empty.', $path));

        $this->object->createDataCollection($path);
    }
}
