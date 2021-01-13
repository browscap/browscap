<?php

declare(strict_types=1);

namespace BrowscapTest;

use Browscap\Browscap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class BrowscapTest extends TestCase
{
    private static function assertAppHasCommand(Application $app, string $command): void
    {
        $cmdObject = $app->get($command);

        static::assertInstanceOf(Command::class, $cmdObject);
        static::assertSame($command, $cmdObject->getName());
    }

    /**
     * tests adding commands
     *
     * @group sourcetest
     */
    public function testConstructorAddsExpectedCommands(): void
    {
        $app = new Browscap();

        self::assertAppHasCommand($app, 'build');
    }
}
