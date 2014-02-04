<?php

namespace Browscap\Command;

use Browscap\Generator\BuildGenerator;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class BuildCommand extends Command
{
    const DEFAULT_BUILD_FOLDER     = '/../../../build';
    const DEFAULT_RESSOURCE_FOLDER = '/../../../resources';

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $defaultBuildFolder    = __DIR__ . self::DEFAULT_BUILD_FOLDER;
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESSOURCE_FOLDER;

        $this
            ->setName('build')
            ->setDescription('The JSON source files and builds the INI files')
            ->addArgument('version', InputArgument::REQUIRED, "Version number to apply")
            ->addOption('output', null, InputOption::VALUE_REQUIRED, "Where to output the build files to", $defaultBuildFolder)
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, "Where the resource files are located", $defaultResourceFolder)
            ->addOption('debug', null, InputOption::VALUE_NONE, "Should the debug mode entered?")
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resourceFolder = $input->getOption('resources');
        $buildFolder    = $input->getOption('output');
        $debug          = $input->getOption('debug');
        $version        = $input->getArgument('version');

        if ($debug) {
            $logHandlers = array(
                new StreamHandler('php://output', Logger::DEBUG)
            );
        } else {
            $logHandlers = array(
                new NullHandler(Logger::DEBUG)
            );
        }

        /** @var $logger \Psr\Log\LoggerInterface */
        $logger = new Logger('browscap', $logHandlers);

        $buildGenerator = new BuildGenerator($resourceFolder, $buildFolder);
        $buildGenerator
            ->setOutput($output)
            ->setLogger($logger)
            ->generateBuilds($version)
        ;

        $output->writeln('<info>Build done.</info>');
    }
}
