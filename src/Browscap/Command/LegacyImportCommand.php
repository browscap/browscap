<?php

namespace Browscap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class LegacyImportCommand extends Command
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var \PDO
     */
    protected $db;

    /**
     * @var string
     */
    protected $outputDirectory;

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('legacy-import')
            ->setDescription('Import from the legacy browscap database into the new JSON format')
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $defaultDsn = 'mysql:host=localhost;dbname=browscap';
        $defaultUsername = 'root';
        $defaultOutputDirectory = './resources_test';

        $dsn = $dialog->ask($output, "<info>Please enter the dsn to connect to</info> [{$defaultDsn}]: ", $defaultDsn);
        $username = $dialog->ask($output, "<info>Please enter DB username</info> [{$defaultUsername}]: ", $defaultUsername);
        $passwd = $dialog->askHiddenResponse($output, '<info>Please enter DB password</info>: ', false);
        $this->outputDirectory = $dialog->ask($output, "<info>Please enter the output directory</info> [{$defaultOutputDirectory}]: ", $defaultOutputDirectory);

        if (!file_exists($this->outputDirectory))
        {
            mkdir($this->outputDirectory, 0755, true);
        }

        $this->db = new \PDO($dsn, $username, $passwd);

        $this->generateDevicesJson();
        $this->generatePlatformsJson();
        $this->generateRenderingEnginesJson();
    }

    public function writeJsonData($filename, $jsonData)
    {
        $jsonEncoded = json_encode($jsonData, JSON_PRETTY_PRINT);

        $jsonEncoded = str_replace('    ', '  ', $jsonEncoded);

        $fullpath = $this->outputDirectory . $filename;
        file_put_contents($fullpath, $jsonEncoded);
    }

    public function generateDevicesJson()
    {
        $stmt = $this->db->prepare('SELECT * FROM Devices');
        $stmt->execute();
        $originalDevices = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $jsonData = array();
        $jsonData['comment'] = 'Registry of devices';
        $jsonData['devices'] = array();

        foreach ($originalDevices as $originalDevice) {
            if ($originalDevice['DeviceID'] == 0) continue;

            $device = array();
            $device['deviceId'] = $originalDevice['DeviceID'];
            $device['name'] = $originalDevice['DeviceName'];
            $device['maker'] = $originalDevice['DeviceMaker'];

            $jsonData['devices'][] = $device;
        }

        $this->writeJsonData('/devices.json', $jsonData);
    }

    public function generatePlatformsJson()
    {
        $stmt = $this->db->prepare('SELECT * FROM Platforms');
        $stmt->execute();
        $originalPlatforms = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $jsonData = array();
        $jsonData['comment'] = 'Registry of platforms';
        $jsonData['platforms'] = array();

        foreach ($originalPlatforms as $originalPlatform) {
            if ($originalPlatform['PlatformID'] == 0) continue;

        	$platform = array();
        	$platform['platformId'] = $originalPlatform['PlatformID'];
        	$platform['name'] = $originalPlatform['PlatformName'];
        	$platform['description'] = $originalPlatform['PlatformDescription'];
        	$platform['match'] = '';

        	$jsonData['platforms'][] = $platform;
        }

        $this->writeJsonData('/platforms.json', $jsonData);
    }

    public function generateRenderingEnginesJson()
    {
        $stmt = $this->db->prepare('SELECT * FROM RenderingEngines');
        $stmt->execute();
        $originalEngines = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $jsonData = array();
        $jsonData['comment'] = 'Registry of rendering engines';
        $jsonData['renderingEngines'] = array();

        foreach ($originalEngines as $originalEngine) {
            if ($originalEngine['EngineID'] == 0) continue;

        	$engine = array();
        	$engine['renderingEngineId'] = $originalEngine['EngineID'];
        	$engine['name'] = $originalEngine['EngineName'];
        	$engine['description'] = $originalEngine['EngineDescription'];
        	$engine['match'] = '';

        	$jsonData['renderingEngines'][] = $engine;
        }

        $this->writeJsonData('/rendering-engines.json', $jsonData);
    }
}

