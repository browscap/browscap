<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   BrowscapTest
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Writer\Factory;

use Browscap\Writer\Factory\FullCollectionFactory;
use org\bovigo\vfs\vfsStream;

/**
 * Class FullCollectionFactoryTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class FullCollectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    const STORAGE_DIR = 'storage';

    /**
     * @var \Browscap\Writer\Factory\FullCollectionFactory
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        vfsStream::setup(self::STORAGE_DIR);

        $this->object = new FullCollectionFactory();
    }

    /**
     * tests creating a writer collection
     *
     * @group writer
     * @group sourcetest
     */
    public function testCreateCollection()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', [], [], '', false);
        $dir        = vfsStream::url(self::STORAGE_DIR);

        self::assertInstanceOf('\Browscap\Writer\WriterCollection', $this->object->createCollection($mockLogger, $dir));
    }
}
