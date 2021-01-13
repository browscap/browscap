<?php

declare(strict_types=1);

namespace Browscap;

use Symfony\Component\Console\Application;

class Browscap extends Application
{
    public function __construct()
    {
        parent::__construct('Browser Capabilities Project', 'dev-master');

        $this->add(new Command\BuildCommand());
        $this->add(new Command\CheckDuplicateTestsCommand());

        $this->add(new Command\RewriteBrowsersCommand());
        $this->add(new Command\ValidateBrowsersCommand());

        $this->add(new Command\RewriteDevicesCommand());
        $this->add(new Command\ValidateDevicesCommand());

        $this->add(new Command\RewriteEnginesCommand());
        $this->add(new Command\ValidateEnginesCommand());

        $this->add(new Command\RewritePlatformsCommand());
        $this->add(new Command\ValidatePlatformsCommand());

        $this->add(new Command\RewriteCoreDivisionsCommand());
        $this->add(new Command\ValidateCoreDivisionsCommand());

        $this->add(new Command\RewriteDivisionsCommand());
        $this->add(new Command\ValidateDivisionsCommand());

        $this->add(new Command\ValidateCommand());

        $this->add(new Command\RewriteTestFixturesCommand());

        $sorterHelper = new Command\Helper\Sorter();
        $this->getHelperSet()->set($sorterHelper);

        $rewriteHelper = new Command\Helper\RewriteHelper();
        $this->getHelperSet()->set($rewriteHelper);

        $validateHelper = new Command\Helper\ValidateHelper();
        $this->getHelperSet()->set($validateHelper);
    }
}
