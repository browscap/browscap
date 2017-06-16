<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   Browscap
 * @copyright  1998-2017 Browser Capabilities Project
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
            ->addOption('coverage', null, InputOption::VALUE_NONE, 'Collect and build with pattern ids useful for coverage');
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
        $logger       = $loggerHelper->create($output);

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

        if ($input->getOption('coverage') !== false) {
            $buildGenerator->setCollectPatternIds(true);
        }

        $buildGenerator->run($input->getArgument('version'));

        $logger->info('Build done.');
    }
}
