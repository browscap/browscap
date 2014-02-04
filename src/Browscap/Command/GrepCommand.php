<?php

namespace Browscap\Command;

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use phpbrowscap\Browscap;
use Browscap\Generator\BuildGenerator;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class GrepCommand extends Command
{
    const MODE_MATCHED = 'matched';
    const MODE_UNMATCHED = 'unmatched';
    
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

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
            ->addOption('iniFile', null, InputOption::VALUE_REQUIRED, 'The INI file to test against', null)
            ->addOption('mode', null, InputOption::VALUE_REQUIRED, 'What mode (matched/unmatched)', self::MODE_UNMATCHED)
            ->addOption('debug', null, InputOption::VALUE_NONE, "Should the debug mode entered?")
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        
        $debug = $input->getOption('debug');

        if ($debug) {
            $logHandlers = array(
                new StreamHandler('php://output', Logger::DEBUG)
            );

        } else {
            $logHandlers = array(
                new NullHandler(Logger::DEBUG)
            );
        }

        $this->logger = new Logger('browscap', $logHandlers);
        $cache_dir    = sys_get_temp_dir() . '/browscap-grep/' . microtime(true) . '/';

        if (!file_exists($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }

        $iniFile = $input->getOption('iniFile');
        
        if (!$iniFile || !file_exists($iniFile)) {
            $this->logger->log(Logger::DEBUG, 'iniFile Option not set or invalid');
            $resourceFolder = __DIR__ . BuildCommand::DEFAULT_RESSOURCE_FOLDER;
            
            $buildGenerator = new BuildGenerator($resourceFolder, $cache_dir);
            $buildGenerator
                ->setOutput($this->output)
                ->setLogger($this->logger)
                ->generateBuilds('temporary-version')
            ;
            
            $iniFile = $cache_dir . 'full_php_browscap.ini';
        }

        $this->logger->log(Logger::DEBUG, 'initialize Browscap');
        $this->browscap = new Browscap($cache_dir);
        $this->browscap->localFile = $iniFile;

        $inputFile = $input->getArgument('inputFile');
        $mode      = $input->getOption('mode');

        if (!in_array($mode, array(self::MODE_MATCHED, self::MODE_UNMATCHED))) {
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

            $this->logger->log(Logger::DEBUG, 'processing UA ' . $ua);
            $this->testUA($ua, $mode);
        }
    }

    /**
     * @param string $ua
     * @param string $mode
     */
    protected function testUA($ua, $mode)
    {
        $data = $this->browscap->getBrowser($ua, true);

        if ($mode == self::MODE_UNMATCHED && $data['Browser'] == 'Default Browser') {
            $this->output->writeln($ua);
        } else if ($mode == self::MODE_MATCHED && $data['Browser'] != 'Default Browser') {
            $this->output->writeln($ua);
        }
    }
}
