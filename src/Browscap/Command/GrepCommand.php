<?php

namespace Browscap\Command;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use phpbrowscap\Browscap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class GrepCommand extends Command
{
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
        $this
            ->setName('grep')
            ->setDescription('')
            ->addArgument('inputFile', InputArgument::REQUIRED, 'The input file to test')
            ->addArgument('iniFile', InputArgument::REQUIRED, 'The INI file to test against')
            ->addOption('mode', null, InputOption::VALUE_REQUIRED, 'What mode (matched/unmatched)', 'unmatched');
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $iniFile = $input->getArgument('iniFile');

        if (!file_exists($iniFile)) {
            throw new \Exception('INI File "' . $iniFile . '" does not exist, or cannot access');
        }

        $stream = new StreamHandler('php://output', Logger::INFO);
        $stream->setFormatter(new \Monolog\Formatter\LineFormatter('%message%'));

        $this->logger = new Logger('browscap');
        $this->logger->pushHandler($stream);
        $this->logger->pushHandler(new \Monolog\Logger\ErrorLogHandler(\Monolog\Logger\ErrorLogHandler::OPERATING_SYSTEM, Logger::NOTICE));

        \Monolog\Logger\ErrorHandler::register($this->logger);

        $cache_dir = sys_get_temp_dir() . '/browscap-grep/' . microtime(true) . '/';

        if (!file_exists($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }

        $this->browscap = new Browscap($cache_dir);
        $this->browscap->localFile = $iniFile;

        $inputFile = $input->getArgument('inputFile');
        $mode = $input->getOption('mode');

        if (!in_array($mode, array('matched','unmatched'))) {
            throw new \Exception("Mode must be 'matched' or 'unmatched'");
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
    }

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
