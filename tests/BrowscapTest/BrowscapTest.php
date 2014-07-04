<?php

namespace BrowscapTest;

use Symfony\Component\Console\Application;
use Browscap\Browscap;

/**
 * Class BrowscapTest
 *
 * @package BrowscapTest
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
