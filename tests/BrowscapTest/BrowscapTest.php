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
 * @package    Browscap
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest;

use Symfony\Component\Console\Application;
use Browscap\Browscap;

/**
 * Class BrowscapTest
 *
 * @category   BrowscapTest
 * @package    Browscap
 * @author     James Titcumb <james@asgrim.com>
 */
class BrowscapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $command
     */
    public function assertAppHasCommand(Application $app, $command)
    {
        $cmdObject = $app->get($command);

        self::assertInstanceOf('Symfony\Component\Console\Command\Command', $cmdObject);
        self::assertSame($command, $cmdObject->getName());
    }

    public function testConstructorAddsExpectedCommands()
    {
        $app = new Browscap();

        self::assertAppHasCommand($app, 'build');
        self::assertAppHasCommand($app, 'diff');
        self::assertAppHasCommand($app, 'grep');
    }
}
