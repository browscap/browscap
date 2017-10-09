<?php
declare(strict_types = 1);
namespace BrowscapTest;

use Browscap\Browscap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class BrowscapTest extends TestCase
{
    /**
     * @param Application $app
     * @param string      $command
     */
    private static function assertAppHasCommand(Application $app, $command) : void
    {
        $cmdObject = $app->get($command);

        self::assertInstanceOf(Command::class, $cmdObject);
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
