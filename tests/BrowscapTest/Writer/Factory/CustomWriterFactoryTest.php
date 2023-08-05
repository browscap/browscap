<?php

declare(strict_types=1);

namespace BrowscapTest\Writer\Factory;

use Browscap\Formatter\FormatterInterface;
use Browscap\Writer\Factory\CustomWriterFactory;
use Browscap\Writer\WriterCollection;
use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use function assert;

class CustomWriterFactoryTest extends TestCase
{
    private const STORAGE_DIR = 'storage';

    private CustomWriterFactory $object;

    /** @throws void */
    protected function setUp(): void
    {
        vfsStream::setup(self::STORAGE_DIR);

        $this->object = new CustomWriterFactory();
    }

    /**
     * tests creating a writer collection
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testCreateCollectionWithDefaultParams(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $dir    = vfsStream::url(self::STORAGE_DIR);

        assert($logger instanceof LoggerInterface);
        static::assertInstanceOf(WriterCollection::class, $this->object->createCollection($logger, $dir));
    }

    /**
     * tests creating a writer collection
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testCreateCollectionForCsvFile(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $dir    = vfsStream::url(self::STORAGE_DIR);

        assert($logger instanceof LoggerInterface);
        static::assertInstanceOf(WriterCollection::class, $this->object->createCollection($logger, $dir, null, [], FormatterInterface::TYPE_CSV));
    }

    /**
     * tests creating a writer collection
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testCreateCollectionForAspFile(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $dir    = vfsStream::url(self::STORAGE_DIR);

        assert($logger instanceof LoggerInterface);
        static::assertInstanceOf(WriterCollection::class, $this->object->createCollection($logger, $dir, null, [], FormatterInterface::TYPE_ASP));
    }

    /**
     * tests creating a writer collection
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testCreateCollectionForXmlFile(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $dir    = vfsStream::url(self::STORAGE_DIR);

        assert($logger instanceof LoggerInterface);
        static::assertInstanceOf(WriterCollection::class, $this->object->createCollection($logger, $dir, null, [], FormatterInterface::TYPE_XML));
    }

    /**
     * tests creating a writer collection
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testCreateCollectionForJsonFile(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $dir    = vfsStream::url(self::STORAGE_DIR);

        assert($logger instanceof LoggerInterface);
        static::assertInstanceOf(WriterCollection::class, $this->object->createCollection($logger, $dir, null, [], FormatterInterface::TYPE_JSON));
    }

    /**
     * tests creating a writer collection
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testCreateCollectionForPhpFile(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $dir    = vfsStream::url(self::STORAGE_DIR);

        assert($logger instanceof LoggerInterface);
        static::assertInstanceOf(WriterCollection::class, $this->object->createCollection($logger, $dir, null, [], FormatterInterface::TYPE_PHP));
    }
}
