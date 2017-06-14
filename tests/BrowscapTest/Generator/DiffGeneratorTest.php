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
namespace BrowscapTest\Generator;

use Browscap\Generator\DiffGenerator;

/**
 * Class DiffGeneratorTest
 *
 * @category   BrowscapTest
 *
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

        $in = <<<'HERE'
; comment

[test]
test=test
HERE;

        file_put_contents($tmpfile, $in);

        self::assertNull($this->object->run($tmpfile, $tmpfile));

        unlink($tmpfile);
    }
}
