<?php

namespace Browscap\Command;

use Browscap\Parser\IniParser;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class ImportCommand extends Command
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $outputDirectory;

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
            ->setName('import')
            ->setDescription('Import from the legacy browscap database into the new JSON format')
            ->addArgument('iniFile', InputArgument::REQUIRED, 'The INI file to import from - note you should parse the FULL browscap INI files')
            ->addOption('debug', null, InputOption::VALUE_NONE, "Should the debug mode entered?")
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output          = $output;
        $this->outputDirectory = __DIR__ . '/../../../resources';

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

        $this->logger->log(Logger::DEBUG, 'checking output directory');
        if (!file_exists($this->outputDirectory . '/user-agents')) {
            mkdir($this->outputDirectory . '/user-agents', 0755, true);
        }

        $filename = $input->getArgument('iniFile');

        $this->logger->log(Logger::DEBUG, 'parse ini file: ' . $filename);
        $iniParser = new IniParser($filename);
        $data      = $iniParser->parse();

        $this->logger->log(Logger::DEBUG, 'process parsing output');
        $divisions = $this->processArrayToDivisions($data);

        $this->logger->log(Logger::DEBUG, 'save processed divisions');
        $divisionId = 0;
        foreach ($divisions as $divisionName => $userAgents) {
            if ($divisionName == 'Browscap Version') {
                continue;
            }

            $this->logger->log(Logger::DEBUG, 'process division ' . $divisionName);
            $this->saveDivision($divisionId++, $divisionName, $userAgents);
        }
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function processArrayToDivisions(array $data)
    {
        $divisions = array();

        foreach ($data as $section => $properties) {
            $this->logger->log(Logger::DEBUG, 'process division ' . $properties['Division']);
            $divisions[$properties['Division']][$section] = $properties;
        }

        return $divisions;
    }

    /**
     * @param string $divisionName
     *
     * @return string
     */
    public function getJsonFilenameFromDivisionName($divisionName)
    {
        return preg_replace('/[^a-z0-9]/', '-', strtolower($divisionName)) . '.json';
    }

    /**
     * @param string $filename
     * @param array  $jsonData
     */
    public function writeJsonData($filename, array $jsonData)
    {
        $jsonEncoded = json_encode($jsonData, JSON_PRETTY_PRINT);

        $jsonEncoded = str_replace('    ', '  ', $jsonEncoded);

        $fullpath = $this->outputDirectory . $filename;
        file_put_contents($fullpath, $jsonEncoded);
    }

    /**
     * @param integer $divisionId
     * @param string  $divisionName
     * @param array   $userAgents
     */
    public function saveDivision($divisionId, $divisionName, array $userAgents)
    {
        $jsonData = array();
        $jsonData['division'] = $divisionName;
        $jsonData['sortIndex'] = ($divisionId * 10);
        $jsonData['userAgents'] = array();

        foreach ($userAgents as $section => $userAgent) {
            $jsonUA = array();
            $jsonUA['userAgent'] = $section;
            $jsonUA['properties'] = $userAgent;

            unset($jsonUA['properties']['Division']);

            $jsonData['userAgents'][] = $jsonUA;
        }

        $filename = $this->getJsonFilenameFromDivisionName($divisionName);
        $this->writeJsonData('/user-agents/' . $filename, $jsonData);

        $msg = sprintf('<info>Written %d user agents to JSON: %s', count($jsonData['userAgents']), $filename);
        $this->output->writeln($msg);
    }

    /**
     * @param string $message
     */
    protected function log($message)
    {
        if (null === $this->logger) {
            return;
        }

        $this->logger->log(Logger::DEBUG, $message);
    }
}
