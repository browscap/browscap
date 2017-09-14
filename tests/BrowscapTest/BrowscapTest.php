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
namespace BrowscapTest;

use Browscap\Browscap;
use Symfony\Component\Console\Application;

/**
 * Class BrowscapTest
 *
 * @category   BrowscapTest
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class BrowscapTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param \Symfony\Component\Console\Application $app
     * @param string                                 $command
     */
    private static function assertAppHasCommand(Application $app, $command) : void
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
    public function testConstructorAddsExpectedCommands() : void
    {
        $app = new Browscap();

        self::assertAppHasCommand($app, 'build');
    }
}
