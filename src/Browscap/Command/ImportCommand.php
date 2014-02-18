<?php

namespace Browscap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Browscap\Parser\IniParser;
use Browscap\Generator\CollectionParser;
use Browscap\Helper\CollectionCreator;
use Browscap\Helper\Generator;
use Browscap\Helper\LoggerHelper;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class ImportCommand extends Command
{
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
            ->addArgument('iniFile', InputArgument::REQUIRED, 'The INI file to import from - note you should parse the FULL browscap INI files');
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->outputDirectory = BuildCommand::DEFAULT_RESOURCES_FOLDER;

        if (!file_exists($this->outputDirectory . '/user-agents')) {
            mkdir($this->outputDirectory . '/user-agents', 0755, true);
        }

        $loggerHelper = new LoggerHelper();
        $this->logger = $loggerHelper->create();

        $collectionCreator = new CollectionCreator();
        $collectionParser = new CollectionParser();

        $generatorHelper = new Generator();
        $generatorHelper
            ->setLogger($this->logger)
            ->setVersion('temporary-version')
            ->setResourceFolder($this->outputDirectory)
            ->setCollectionCreator($collectionCreator)
            ->setCollectionParser($collectionParser)
            ->createCollection()
            ->parseCollection()
        ;

        $filename = $input->getArgument('iniFile');

        $iniParser = new IniParser($filename);
        $data      = $iniParser->parse();

        $divisions = $this->processArrayToDivisions($data, $generatorHelper->getCollectionData());

        $divisionId = 0;
        foreach ($divisions as $divisionName => $userAgents) {
            if ($divisionName == 'Browscap Version') {
                continue;
            }

            $this->saveDivision($divisionId++, $divisionName, $userAgents);
        }
    }

    /**
     * @param array $data
     * @param array $collectionData
     *
     * @return array
     */
    public function processArrayToDivisions(array $data, array $collectionData = array())
    {
        $divisions = array();

        foreach ($data as $section => $properties) {
            if (isset($collectionData[$section])) {
                continue;
            }

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

        $jsonEncoded = str_replace(array('    ', '"\"', '\""'), array('  ', '"', '"'), $jsonEncoded);

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

        $msg = sprintf('Written %d user agents to JSON: %s', count($jsonData['userAgents']), $filename);
        $this->logger->info($msg);
    }
}
