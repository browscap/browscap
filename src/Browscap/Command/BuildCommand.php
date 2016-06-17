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
use Browscap\Helper\CollectionCreator;
use Browscap\Helper\LoggerHelper;
use Browscap\Writer\Factory\FullCollectionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BuildCommand
 *
 * @category   Browscap
 * @author     James Titcumb <james@asgrim.com>
 */
class BuildCommand extends Command
{
    /**
     * @var string
     */
    const DEFAULT_BUILD_FOLDER = '/../../../build';

    /**
     * @var string
     */
    const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $defaultBuildFolder    = __DIR__ . self::DEFAULT_BUILD_FOLDER;
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('build')
            ->setDescription('The JSON source files and builds the INI files')
            ->addArgument('version', InputArgument::REQUIRED, 'Version number to apply')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Where to output the build files to', $defaultBuildFolder)
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder)
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Should the debug mode entered?');
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
        $loggerHelper = new LoggerHelper();
        $logger       = $loggerHelper->create($input->getOption('debug'));

        $logger->info('Build started.');

        $buildFolder = $input->getOption('output');

        $buildGenerator = new BuildGenerator(
            $input->getOption('resources'),
            $buildFolder
        );

        $writerCollectionFactory = new FullCollectionFactory();
        $writerCollection        = $writerCollectionFactory->createCollection($logger, $buildFolder);

        $buildGenerator
            ->setLogger($logger)
            ->setCollectionCreator(new CollectionCreator())
            ->setWriterCollection($writerCollection);

        $buildGenerator->run($input->getArgument('version'));

        $logger->info('Build done.');
    }
}
