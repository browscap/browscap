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

namespace BrowscapTest\Generator;

use Browscap\Generator\DiffGenerator;

/**
 * Class DiffGeneratorTest
 *
 * @category   BrowscapTest
 * @author     James Titcumb <james@asgrim.com>
 */
class DiffGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Generator\DiffGenerator
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new DiffGenerator();
    }

    /**
     * tests setting and getting a logger
     *
     * @group generator
     * @group sourcetest
     */
    public function testSetLogger()
    {
        $logger = $this->createMock(\Monolog\Logger::class);

        self::assertSame($this->object, $this->object->setLogger($logger));
    }

    /**
     * tests running the generation of a diff
     *
     * @group generator
     * @group sourcetest
     */
    public function testRun()
    {
        $mock = $this->createMock(\Monolog\Logger::class);

        self::assertSame($this->object, $this->object->setLogger($mock));

        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
; comment

[test]
test=test
HERE;

        file_put_contents($tmpfile, $in);

        self::assertNull($this->object->run($tmpfile, $tmpfile));

        unlink($tmpfile);
    }
}
