<?php
declare(strict_types = 1);
namespace Browscap;

use Symfony\Component\Console\Application;

class Browscap extends Application
{
    public function __construct()
    {
        parent::__construct('Browser Capabilities Project', 'dev-master');

        $this->add(new Command\BuildCommand());
        $this->add(new Command\CheckDuplicateTestsCommand());
    }
}
