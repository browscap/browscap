<?php

namespace Browscap;

use Symfony\Component\Console\Application;

class Browscap extends Application
{
    public function __construct()
    {
        parent::__construct('Browser Capabilities Project', 'dev-master');

        $commands = array(
            new \Browscap\Command\BuildCommand(),
            new \Browscap\Command\DiffCommand(),
        );

        foreach ($commands as $command) {
            $this->add($command);
        }
    }
}
