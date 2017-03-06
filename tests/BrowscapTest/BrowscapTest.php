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
        self::assertAppHasCommand($app, 'diff');
        self::assertAppHasCommand($app, 'grep');
    }
}
