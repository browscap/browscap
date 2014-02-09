<?php

namespace Browscap\Command;

use Browscap\Generator\BrowscapIniGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Helper\CollectionCreator;
use Browscap\Helper\Generator;
use Browscap\Helper\LoggerHelper;
use Monolog\Logger;
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
     * @var \phpbrowscap\Browscap
     */
    protected $browscap;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger = null;

    /**
     * (non-PHPdoc)
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
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cache_dir = sys_get_temp_dir() . '/browscap-grep/' . microtime(true) . '/';

        if (!file_exists($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }

        $loggerHelper = new LoggerHelper();
        $this->logger = $loggerHelper->create();

        $iniFile = $input->getArgument('iniFile');

        if (!$iniFile || !file_exists($iniFile)) {
            $this->logger->log(Logger::INFO, 'iniFile Argument not set or invalid - creating iniFile from resources');

            $iniFile = $cache_dir . 'full_php_browscap.ini';
            $resourceFolder = $input->getOption('resources');

            $collectionCreator = new CollectionCreator();
            $collectionParser = new CollectionParser();
            $iniGenerator = new BrowscapIniGenerator();

            $generatorHelper = new Generator();
            $generatorHelper
                ->setVersion('temporary-version')
                ->setResourceFolder($resourceFolder)
                ->setCollectionCreator($collectionCreator)
                ->setCollectionParser($collectionParser)
                ->createCollection()
                ->parseCollection()
                ->setGenerator($iniGenerator->setOptions(true, true, false))
            ;

            file_put_contents($iniFile, $generatorHelper->create());
        }

        $this->browscap = new Browscap($cache_dir);
        $this->browscap->localFile = $iniFile;

        $inputFile = $input->getArgument('inputFile');
        $mode = $input->getOption('mode');

        if (!in_array($mode, array(self::MODE_MATCHED, self::MODE_UNMATCHED))) {
            throw new \Exception('Mode must be "matched" or "unmatched"');
        }

        if (!file_exists($inputFile)) {
            throw new \Exception('Input File "' . $inputFile . '" does not exist, or cannot access');
        }

        $fileContents = file_get_contents($inputFile);

        $uas = explode("\n", $fileContents);

        foreach ($uas as $ua) {
            if ($ua == '') {
                continue;
            }

            $this->testUA($ua, $mode);
        }

        $this->logger->log(Logger::INFO, 'Grep done.');
    }

    /**
     * @param string $ua
     * @param string $mode
     */
    protected function testUA($ua, $mode)
    {
        $data = $this->browscap->getBrowser($ua, true);

        if ($mode == 'unmatched' && $data['Browser'] == 'Default Browser') {
            $this->logger->log(Logger::INFO, $ua);
        } else if ($mode == 'matched' && $data['Browser'] != 'Default Browser') {
            $this->logger->log(Logger::INFO, $ua);
        }
    }
}
