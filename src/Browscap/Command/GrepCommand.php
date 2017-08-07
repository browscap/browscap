<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace Browscap\Command;

use Browscap\Generator\BuildGenerator;
use Browscap\Generator\GrepGenerator;
use Browscap\Helper\CollectionCreator;
use Browscap\Helper\LoggerHelper;
use Browscap\Writer\Factory\FullPhpWriterFactory;
use BrowscapPHP\Browscap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GrepCommand
 *
 * @category   Browscap
 *
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
     * @throws \Exception
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @see    setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFile = $input->getArgument('inputFile');
        $mode      = $input->getOption('mode');

        if (!in_array($mode, [self::MODE_MATCHED, self::MODE_UNMATCHED])) {
            throw new \Exception('Mode must be "matched" or "unmatched"');
        }

        if (!file_exists($inputFile)) {
            throw new \Exception('Input File "' . $inputFile . '" does not exist, or cannot access');
        }

        $cacheDir = sys_get_temp_dir() . '/browscap-grep/' . microtime(true) . '/';

        if (!file_exists($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $loggerHelper = new LoggerHelper();
        $this->logger = $loggerHelper->create($output);

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
                ->setWriterCollection($writerCollection);

            $buildGenerator->run($input->getArgument('version'), false);
        }

        $generator           = new GrepGenerator();
        $browscap            = new Browscap($cacheDir);
        $browscap->localFile = $iniFile;

        $generator
            ->setLogger($this->logger)
            ->run($browscap, $inputFile, $mode);

        $this->logger->info('Grep done.');
    }
}
