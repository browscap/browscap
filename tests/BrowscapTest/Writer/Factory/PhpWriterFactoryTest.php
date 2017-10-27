<?php
declare(strict_types = 1);
namespace BrowscapTest\Writer\Factory;

use Browscap\Writer\Factory\PhpWriterFactory;
use Browscap\Writer\WriterCollection;
use Monolog\Logger;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class PhpWriterFactoryTest extends TestCase
{
    public const STORAGE_DIR = 'storage';

    /**
     * @var PhpWriterFactory
     */
    private $object;

    public function setUp() : void
    {
        vfsStream::setup(self::STORAGE_DIR);

        $this->object = new PhpWriterFactory();
    }

    /**
     * tests creating a writer collection
     */
    public function testCreateCollection() : void
    {
        $logger = $this->createMock(Logger::class);
        $dir    = vfsStream::url(self::STORAGE_DIR);

        self::assertInstanceOf(WriterCollection::class, $this->object->createCollection($logger, $dir));
    }
}
