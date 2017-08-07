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
namespace BrowscapTest\Writer\Factory;

use Browscap\Writer\Factory\FullPhpWriterFactory;
use org\bovigo\vfs\vfsStream;

/**
 * Class FullPhpWriterFactoryTest
 *
 * @category   BrowscapTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class FullPhpWriterFactoryTest extends \PHPUnit\Framework\TestCase
{
    const STORAGE_DIR = 'storage';

    /**
     * @var \Browscap\Writer\Factory\FullPhpWriterFactory
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        vfsStream::setup(self::STORAGE_DIR);

        $this->object = new FullPhpWriterFactory();
    }

    /**
     * tests creating a writer collection
     *
     * @group writer
     * @group sourcetest
     */
    public function testCreateCollection()
    {
        $logger = $this->createMock(\Monolog\Logger::class);
        $dir    = vfsStream::url(self::STORAGE_DIR);

        self::assertInstanceOf(\Browscap\Writer\WriterCollection::class, $this->object->createCollection($logger, $dir));
    }
}
