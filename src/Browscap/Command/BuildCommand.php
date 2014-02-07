<?php

namespace Browscap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Browscap\Generator\BuildGenerator;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class BuildCommand extends Command
{
    const DEFAULT_BUILD_FOLDER = '/../../../build';
    const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $defaultBuildFolder = __DIR__ . self::DEFAULT_BUILD_FOLDER;
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('build')
            ->setDescription('The JSON source files and builds the INI files')
            ->addArgument('version', InputArgument::REQUIRED, "Version number to apply")
            ->addOption('output', null, InputOption::VALUE_REQUIRED, "Where to output the build files to", $defaultBuildFolder)
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, "Where the resource files are located", $defaultResourceFolder);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resourceFolder = $input->getOption('resources');
        $buildFolder = $input->getOption('output');
        $version = $input->getArgument('version');

        $buildGenerator = new BuildGenerator($resourceFolder, $buildFolder);
        $buildGenerator->setOutput($output);
        $buildGenerator->generateBuilds($version);

        $output->writeln('<info>All done.</info>');
    }
}
