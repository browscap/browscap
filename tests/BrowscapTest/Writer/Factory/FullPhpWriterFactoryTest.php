<?php

declare(strict_types=1);

namespace BrowscapTest\Writer\Factory;

use Browscap\Writer\Factory\FullPhpWriterFactory;
use Browscap\Writer\WriterCollection;
use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use function assert;

class FullPhpWriterFactoryTest extends TestCase
{
    private const STORAGE_DIR = 'storage';

    private FullPhpWriterFactory $object;

    /** @throws void */
    protected function setUp(): void
    {
        vfsStream::setup(self::STORAGE_DIR);

        $this->object = new FullPhpWriterFactory();
    }

    /**
     * tests creating a writer collection
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testCreateCollection(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $dir    = vfsStream::url(self::STORAGE_DIR);

        assert($logger instanceof LoggerInterface);
        static::assertInstanceOf(WriterCollection::class, $this->object->createCollection($logger, $dir));
    }
}
