<?php

declare(strict_types=1);

namespace BrowscapTest\Data\Factory;

use Assert\AssertionFailedException;
use Assert\InvalidArgumentException;
use Browscap\Data\DataCollection;
use Browscap\Data\DuplicateDataException;
use Browscap\Data\Factory\DataCollectionFactory;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;

use function assert;
use function sprintf;

class DataCollectionFactoryTest extends TestCase
{
    /** @var DataCollectionFactory */
    private $object;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        assert($logger instanceof LoggerInterface);
        $this->object = new DataCollectionFactory($logger);
    }

    /**
     * tests throwing an exception while creating a data collaction when a dir is invalid
     *
     * @throws AssertionFailedException
     * @throws ReflectionException
     * @throws Exception
     */
    public function testCreateDataCollectionThrowsExceptionOnInvalidDirectory(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Directory "./platforms" does not exist.');

        $this->object->createDataCollection('.');
    }

    /**
     * tests creating a data collection
     *
     * @throws AssertionFailedException
     * @throws ReflectionException
     */
    public function testCreateDataCollection(): void
    {
        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['addPlatform', 'addDivision', 'addEngine', 'addDevice', 'addBrowser'])
            ->getMock();

        $collection->expects(static::exactly(2))
            ->method('addPlatform')
            ->willReturnSelf();
        $collection->expects(static::exactly(2))
            ->method('addEngine')
            ->willReturnSelf();
        $collection->expects(static::once())
            ->method('addDevice')
            ->willReturnSelf();
        $collection->expects(static::exactly(3))
            ->method('addBrowser')
            ->willReturnSelf();
        $collection->expects(static::once())
            ->method('addDivision')
            ->willReturnSelf();

        $property = new ReflectionProperty($this->object, 'collection');
        $property->setAccessible(true);
        $property->setValue($this->object, $collection);

        $result = $this->object->createDataCollection(__DIR__ . '/../../../fixtures/build-ok');

        static::assertInstanceOf(DataCollection::class, $result);
        static::assertSame($collection, $result);
    }

    /**
     * tests creating a data collection
     *
     * @throws AssertionFailedException
     */
    public function testCreateDataCollectionFailsForDuplicateDeviceEntries(): void
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
     * @throws AssertionFailedException
     */
    public function testCreateDataCollectionFailsForDuplicateBrowserEntries(): void
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
     * @throws AssertionFailedException
     */
    public function testCreateDataCollectionFailsBecauseOfMissingFiles(): void
    {
        $path = __DIR__ . '/../../../fixtures/missing-file';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('File "%s/core/default-browser.json" does not exist.', $path));

        $this->object->createDataCollection($path);
    }

    /**
     * tests creating a data collection
     *
     * @throws AssertionFailedException
     */
    public function testCreateDataCollectionFailsBecauseOfInvalidChars(): void
    {
        $path = __DIR__ . '/../../../fixtures/non-ascii-chars';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('File "%s/core/default-browser.json" contains Non-ASCII-Characters.', $path));

        $this->object->createDataCollection($path);
    }

    /**
     * tests creating a data collection
     *
     * @throws AssertionFailedException
     */
    public function testCreateDataCollectionFailsBecauseOfInvalidJson(): void
    {
        $path = __DIR__ . '/../../../fixtures/invalid-json';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('File "%s/core/default-browser.json" had invalid JSON.', $path));

        $this->object->createDataCollection($path);
    }

    /**
     * tests creating a data collection
     *
     * @throws AssertionFailedException
     */
    public function testCreateDataCollectionFailsBecauseOfEmptyDirectory(): void
    {
        $path = __DIR__ . '/../../../fixtures/empty-directory';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Directory "%s/browsers" was empty.', $path));

        $this->object->createDataCollection($path);
    }
}
