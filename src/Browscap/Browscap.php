<?php

namespace Browscap;

use Symfony\Component\Console\Application;

class Browscap extends Application
{

    public function __construct()
    {
        parent::__construct('Browser Capabilities Project', null);

        $commands = array(
            new \Browscap\Command\PropertyCommand(),
            new \Browscap\Command\BuildCommand(),
        );

        foreach ($commands as $command) {
            $this->add($command);
        }
    }

}
