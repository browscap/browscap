<?php

namespace Browscap\Command;

use Browscap\Generator\BrowscapIniGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\CollectionCreator;
use Browscap\Helper\Generator;
use Browscap\Helper\LoggerHelper;
use phpbrowscap\Browscap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author James Titcumb <james@asgrim.com>
 * @package Browscap\Command
 *
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
     * @var \phpbrowscap\Browscap
     */
    protected $browscap;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger = null;

    /**
     * (non-PHPdoc)
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
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
     * (non-PHPdoc)
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
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

            $writerCollectionFactory = new \Browscap\Writer\Factory\FullPhpWriterFactory();
            $writerCollection        = $writerCollectionFactory->createCollection($logger, $cacheDir);

            $buildGenerator
                ->setLogger($logger)
                ->setCollectionCreator(new CollectionCreator())
                ->setWriterCollection($writerCollection)
                ->run($input->getArgument('version'))
            ;
        }

        $generator = new \Browscap\Generator\GrepGenerator();

        $generator
            ->setLogger($logger)
            ->run($cacheDir, $iniFile, $inputFile, $mode)
            ;

        $this->logger->info('Grep done.');
    }
}
