<?php

namespace Browscap;

use Symfony\Component\Console\Application;

class Browscap extends Application
{
    public function __construct()
    {
        parent::__construct('Browser Capabilities Project', 'dev-master');

        $commands = array(
            new Command\BuildCommand(),
            new Command\DiffCommand(),
            new Command\GrepCommand(),
            new Command\ReorderCommand(),
        );

        foreach ($commands as $command) {
            $this->add($command);
        }
    }
}
