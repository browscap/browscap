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

namespace BrowscapTest;

use Browscap\Browscap;
use Symfony\Component\Console\Application;

/**
 * Class BrowscapTest
 *
 * @category   BrowscapTest
 * @author     James Titcumb <james@asgrim.com>
 */
class BrowscapTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param \Symfony\Component\Console\Application $app
     * @param string                                 $command
     */
    private static function assertAppHasCommand(Application $app, $command)
    {
        $cmdObject = $app->get($command);

        self::assertInstanceOf('Symfony\Component\Console\Command\Command', $cmdObject);
        self::assertSame($command, $cmdObject->getName());
    }

    /**
     * tests adding commands
     *
     * @group browscap
     * @group sourcetest
     */
    public function testConstructorAddsExpectedCommands()
    {
        $app = new Browscap();

        self::assertAppHasCommand($app, 'build');
    }
}
