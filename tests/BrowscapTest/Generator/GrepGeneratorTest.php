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
 * @package    Generator
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Generator;

use Browscap\Command\GrepCommand;
use Browscap\Generator\GrepGenerator;

/**
 * Class GrepGeneratorTest
 *
 * @category   BrowscapTest
 * @package    Generator
 * @author     James Titcumb <james@asgrim.com>
 */
class GrepGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Browscap\Generator\GrepGenerator
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new GrepGenerator();
    }

    /**
     * tests setting and getting a logger
     *
     * @group generator
     * @group sourcetest
     */
    public function testSetLogger()
    {
        $mock = $this->getMock('\Monolog\Logger', array(), array(), '', false);

        self::assertSame($this->object, $this->object->setLogger($mock));
    }

    /**
     * tests running the generation of a grep command
     *
     * @group generator
     * @group sourcetest
     */
    public function testRun()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', array(), array(), '', false);

        self::assertSame($this->object, $this->object->setLogger($mockLogger));

        $mockBrowscap = $this->getMock('\phpbrowscap\Browscap', array(), array(), '', false);

        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
; comment

test
HERE;

        file_put_contents($tmpfile, $in);

        self::assertNull($this->object->run($mockBrowscap, $tmpfile, GrepCommand::MODE_UNMATCHED));

        unlink($tmpfile);
    }
}
