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
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Command;

use Browscap\Generator\BuildGenerator;
use Browscap\Generator\DiffGenerator;
use Browscap\Helper\CollectionCreator;
use Browscap\Helper\LoggerHelper;
use Browscap\Writer\Factory\FullPhpWriterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DiffCommand
 *
 * @category   Browscap
 * @author     James Titcumb <james@asgrim.com>
 */
class DiffCommand extends Command
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $defaultResourceFolder = __DIR__ . BuildCommand::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('diff')
            ->setDescription('Compare the data contained within two .ini files (regardless of order or format)')
            ->addArgument('left', InputArgument::REQUIRED, 'The left .ini file to compare')
            ->addArgument('right', InputArgument::OPTIONAL, 'The right .ini file to compare')
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder);
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
     * @throws \LogicException When this abstract method is not implemented
     * @return null|int        null or 0 if everything went fine, or an error code
     *
     * @see    setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $leftFilename  = $input->getArgument('left');
        $rightFilename = $input->getArgument('right');

        $loggerHelper = new LoggerHelper();
        $logger       = $loggerHelper->create($output);

        if (!$rightFilename || !file_exists($rightFilename)) {
            $logger->info('right file not set or invalid - creating right file from resources');

            $cacheDir      = sys_get_temp_dir() . '/browscap-diff/' . microtime(true) . '/';
            $rightFilename = $cacheDir . 'full_php_browscap.ini';

            if (!file_exists($cacheDir)) {
                mkdir($cacheDir, 0777, true);
            }

            $buildGenerator = new BuildGenerator(
                $input->getOption('resources'),
                $cacheDir
            );

            $writerCollectionFactory = new FullPhpWriterFactory();
            $writerCollection        = $writerCollectionFactory->createCollection($logger, $cacheDir);

            $buildGenerator
                ->setLogger($logger)
                ->setCollectionCreator(new CollectionCreator())
                ->setWriterCollection($writerCollection);

            $buildGenerator->run($input->getArgument('version'), false);
        }

        $generator = new DiffGenerator();

        $generator
            ->setLogger($logger)
            ->run($leftFilename, $rightFilename);

        $logger->info('Diff done.');
    }
}
