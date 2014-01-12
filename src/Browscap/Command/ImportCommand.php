<?php

namespace Browscap\Command;

use Browscap\Parser\IniParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
     * (non-PHPdoc)
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('import')
            ->setDescription('Import from the legacy browscap database into the new JSON format')
            ->addArgument(
                'iniFile',
                InputArgument::REQUIRED,
                'The INI file to import from - note you should parse the FULL browscap INI files'
            );
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->outputDirectory = __DIR__ . '/../../../resources';

        if (!file_exists($this->outputDirectory . '/user-agents')) {
            mkdir($this->outputDirectory . '/user-agents', 0755, true);
        }

        $filename = $input->getArgument('iniFile');

        $iniParser = new IniParser($filename);
        $data      = $iniParser->parse();

        $divisions = $this->processArrayToDivisions($data);

        $divisionId = 0;
        foreach ($divisions as $divisionName => $userAgents) {
            if ($divisionName == 'Browscap Version') {
                continue;
            }

            $this->saveDivision($divisionId++, $divisionName, $userAgents);
        }
    }

    public function processArrayToDivisions(array $data)
    {
        $divisions = array();

        foreach ($data as $section => $properties) {
            $divisions[$properties['Division']][$section] = $properties;
        }

        return $divisions;
    }

    public function getJsonFilenameFromDivisionName($divisionName)
    {
        return preg_replace('/[^a-z0-9]/', '-', strtolower($divisionName)) . '.json';
    }

    public function writeJsonData($filename, $jsonData)
    {
        $jsonEncoded = json_encode($jsonData, JSON_PRETTY_PRINT);

        $jsonEncoded = str_replace('    ', '  ', $jsonEncoded);

        $fullpath = $this->outputDirectory . $filename;
        file_put_contents($fullpath, $jsonEncoded);
    }

    public function saveDivision($divisionId, $divisionName, $userAgents)
    {
        $jsonData               = array();
        $jsonData['division']   = $divisionName;
        $jsonData['sortIndex']  = ($divisionId * 10);
        $jsonData['userAgents'] = array();

        foreach ($userAgents as $section => $userAgent) {
            $jsonUA               = array();
            $jsonUA['userAgent']  = $section;
            $jsonUA['properties'] = $userAgent;

            unset($jsonUA['properties']['Division']);

            $jsonData['userAgents'][] = $jsonUA;
        }

        $filename = $this->getJsonFilenameFromDivisionName($divisionName);
        $this->writeJsonData('/user-agents/' . $filename, $jsonData);

        $msg = sprintf('<info>Written %d user agents to JSON: %s', count($jsonData['userAgents']), $filename);
        $this->output->writeln($msg);
    }
}
