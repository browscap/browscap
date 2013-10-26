<?php

namespace Browscap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Browscap\Generator\BuildGenerator;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class BuildCommand extends Command
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('The JSON source files and builds the INI files')
            ->addArgument('version', InputArgument::REQUIRED, "Version number to apply");
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resourceFolder = __DIR__ . '/../../../resources';
        $buildFolder = __DIR__ . '/../../../build';
        $version = $input->getArgument('version');

        $buildGenerator = new BuildGenerator($resourceFolder, $buildFolder);
        $buildGenerator->setOutput($output);
        $buildGenerator->generateBuilds($version);

        $output->writeln('<info>All done.</info>');
    }
}
