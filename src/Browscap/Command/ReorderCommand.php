<?php

namespace Browscap\Command;

use Browscap\Generator\BrowscapIniGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\CollectionCreator;
use Browscap\Helper\Generator;
use Browscap\Helper\LoggerHelper;
use Browscap\Parser\IniParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Browscap\Generator\DataCollection;

/**
 * @author James Titcumb <james@asgrim.com>
 * @package Browscap\Command
 */
class ReorderCommand extends Command
{
    /**
     * @var string
     */
    const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    /**
     * (non-PHPdoc)
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $defaultResourceFolder = __DIR__ . BuildCommand::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('reorder')
            ->setDescription('Reorders the resource files')
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder)
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Should the debug mode entered?')
        ;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resourceFolder = $input->getOption('resources');
        $debug = $input->getOption('debug');

        $loggerHelper = new LoggerHelper();
        $logger = $loggerHelper->create($debug);

        $collection = new DataCollection('temporary-version');

        $collectionCreator = new CollectionCreator();
        $collectionCreator
            ->setLogger($logger)
            ->setDataCollection($collection)
            ->createDataCollection($resourceFolder)
        ;

        $allDivisions = $collection->getDivisions();

        $logger->info('Reorder done.');
    }
}
