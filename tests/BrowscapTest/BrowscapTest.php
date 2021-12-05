<?php

declare(strict_types=1);

namespace BrowscapTest;

use Browscap\Browscap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Exception\LogicException;

class BrowscapTest extends TestCase
{
    /**
     * @throws CommandNotFoundException
     */
    private static function assertAppHasCommand(Application $app, string $command): void
    {
        $cmdObject = $app->get($command);

        static::assertInstanceOf(Command::class, $cmdObject);
        static::assertSame($command, $cmdObject->getName());
    }

    /**
     * tests adding commands
     *
     * @throws LogicException
     * @throws CommandNotFoundException
     *
     * @group sourcetest
     */
    public function testConstructorAddsExpectedCommands(): void
    {
        $app = new Browscap();

        self::assertAppHasCommand($app, 'build');
    }
}
