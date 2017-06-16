<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   BrowscapTest
 * @copyright  1998-2017 Browser Capabilities Project
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
