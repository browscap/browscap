<?php

declare(strict_types=1);

namespace BrowscapTest\Writer\Factory;

use Browscap\Writer\Factory\FullCollectionFactory;
use Browscap\Writer\WriterCollection;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use function assert;

class FullCollectionFactoryTest extends TestCase
{
    private const STORAGE_DIR = 'storage';

    /** @var FullCollectionFactory */
    private $object;

    protected function setUp(): void
    {
        vfsStream::setup(self::STORAGE_DIR);

        $this->object = new FullCollectionFactory();
    }

    /**
     * tests creating a writer collection
     */
    public function testCreateCollection(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $dir    = vfsStream::url(self::STORAGE_DIR);

        assert($logger instanceof LoggerInterface);
        static::assertInstanceOf(WriterCollection::class, $this->object->createCollection($logger, $dir));
    }
}
