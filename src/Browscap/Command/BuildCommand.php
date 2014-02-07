<?php

namespace Browscap\Command;

use Browscap\Generator\BuildGenerator;
use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
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
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $defaultBuildFolder = __DIR__ . '/../../../build';
        $defaultResourceFolder = __DIR__ . '/../../../resources';

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

        $stream = new StreamHandler('php://output', Logger::INFO);
        $stream->setFormatter(new LineFormatter('%message%' . "\n"));

        $logger = new Logger('browscap');
        $logger->pushHandler($stream);
        $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::NOTICE));

        ErrorHandler::register($logger);

        $buildGenerator = new BuildGenerator($resourceFolder, $buildFolder);
        $buildGenerator->setOutput($output);
        $buildGenerator->setLogger($logger);
        $buildGenerator->generateBuilds($version);

        $logger->log(Logger::INFO, 'Build done.');
    }
}
