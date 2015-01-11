<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   Browscap
 * @package    Command
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Command;

use Browscap\Generator\BuildGenerator;
use Browscap\Generator\GrepGenerator;
use Browscap\Helper\CollectionCreator;
use Browscap\Helper\LoggerHelper;
use Browscap\Writer\Factory\FullPhpWriterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use phpbrowscap\Browscap;

/**
 * Class GrepCommand
 *
 * @category   Browscap
 * @package    Command
 * @author     James Titcumb <james@asgrim.com>
 */
class GrepCommand extends Command
{
    /**
     * @var string
     */
    const MODE_MATCHED = 'matched';

    /**
     * @var string
     */
    const MODE_UNMATCHED = 'unmatched';

    /**
     * @var string
     */
    const FOUND_INVISIBLE = 'invisible';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $defaultResourceFolder = __DIR__ . BuildCommand::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('grep')
            ->setDescription('')
            ->addArgument('inputFile', InputArgument::REQUIRED, 'The input file to test')
            ->addArgument('iniFile', InputArgument::OPTIONAL, 'The INI file to test against')
            ->addOption('mode', null, InputOption::VALUE_REQUIRED, 'What mode (matched/unmatched)', self::MODE_UNMATCHED)
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder)
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Should the debug mode entered?')
        ;
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @throws \Exception
     * @return null|integer null or 0 if everything went fine, or an error code
     *
     * @see    setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFile = $input->getArgument('inputFile');
        $mode      = $input->getOption('mode');

        if (!in_array($mode, array(self::MODE_MATCHED, self::MODE_UNMATCHED))) {
            throw new \Exception('Mode must be "matched" or "unmatched"');
        }

        if (!file_exists($inputFile)) {
            throw new \Exception('Input File "' . $inputFile . '" does not exist, or cannot access');
        }

        $cacheDir = sys_get_temp_dir() . '/browscap-grep/' . microtime(true) . '/';

        if (!file_exists($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $debug = $input->getOption('debug');

        $loggerHelper = new LoggerHelper();
        $this->logger = $loggerHelper->create($debug);

        $iniFile = $input->getArgument('iniFile');

        if (!$iniFile || !file_exists($iniFile)) {
            $this->logger->info('iniFile Argument not set or invalid - creating iniFile from resources');

            $iniFile = $cacheDir . 'full_php_browscap.ini';

            $buildGenerator = new BuildGenerator(
                $input->getOption('resources'),
                $cacheDir
            );

            $writerCollectionFactory = new FullPhpWriterFactory();
            $writerCollection        = $writerCollectionFactory->createCollection($this->logger, $cacheDir);

            $buildGenerator
                ->setLogger($this->logger)
                ->setCollectionCreator(new CollectionCreator())
                ->setWriterCollection($writerCollection)
            ;

            $buildGenerator->run($input->getArgument('version'), false);
        }

        $generator = new GrepGenerator();
        $browscap  = new Browscap($cacheDir);
        $browscap->localFile = $iniFile;

        $generator
            ->setLogger($this->logger)
            ->run($browscap, $inputFile, $mode)
        ;

        $this->logger->info('Grep done.');
    }
}
