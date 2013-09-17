<?php

namespace BrowscapTest;

use Symfony\Component\Console\Application;
use Browscap\Browscap;

class BrowscapTest extends \PHPUnit_Framework_TestCase
{
    public function assertAppHasCommand(Application $app, $command)
    {
        $cmdObject = $app->get($command);

        $this->assertInstanceOf('Symfony\Component\Console\Command\Command', $cmdObject);
        $this->assertSame($command, $cmdObject->getName());
    }

    public function testConstructorAddsExpectedCommands()
    {
        $app = new Browscap();

        $this->assertAppHasCommand($app, 'build');
        $this->assertAppHasCommand($app, 'diff');
    }
}
