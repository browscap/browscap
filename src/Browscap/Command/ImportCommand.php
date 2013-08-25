<?php

namespace Browscap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Browscap\Parser\IniParser;
use Browscap\Generator\BrowscapIniGenerator;
use Browscap\Entity\RenderingEngine;
use Browscap\Entity\Device;
use Browscap\Entity\Platform;

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

    protected $propMap;

    /**
     * @var \Browscap\Generator\BrowscapIniGenerator
     */
    protected $generator;

    /**
     * @var \Browscap\Entity\RenderingEngine[]
     */
    protected $renderingEngines = array();

    protected $lastRenderingEngineId = 1;

    /**
     * @var \Browscap\Entity\Device[]
     */
    protected $devices = array();

    protected $lastDeviceId = 1;

    /**
     * @var \Browscap\Entity\Platform[]
     */
    protected $platforms = array();

    protected $lastPlatformId = 1;

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
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('iniFile');
        $fileContents = file_get_contents($filename);

        $this->outputDirectory = './resources_test';

        if (!file_exists($this->outputDirectory . '/user-agents')) {
            mkdir($this->outputDirectory . '/user-agents', 0755, true);
        }

        $this->propMap = array_flip(BrowscapIniGenerator::$propMap);
        $this->generator = new BrowscapIniGenerator();

        $commentDivisions = explode(';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;', $fileContents);

        $parser = new IniParser('');

        $skippedFirst = false;
        foreach ($commentDivisions as $division) {
            if (!$skippedFirst) {
                $skippedFirst = true;
                continue;
            }

            $lines = explode("\n", $division);

            $divisionName = trim($lines[0]);

            array_shift($lines);

            $parser->setFileLines($lines);
            $data = $parser->parse();

            if ($divisionName == 'Browscap Version') {
                $output->writeln(sprintf('<info>Parsing browscap version %s (released %s)</info>', $data['GJK_Browscap_Version']['Version'], $data['GJK_Browscap_Version']['Released']));
                continue;
            }

            $this->parseDivison($divisionName, $data);
        }

        $this->saveRenderingEngines();
        $this->saveDevices();
        $this->savePlatforms();
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

    public function saveRenderingEngines()
    {
        $jsonData = array();
        $jsonData['comment'] = 'Registry of rendering engines';
        $jsonData['renderingEngines'] = array();

        foreach ($this->renderingEngines as $renderingEngine) {
            $jsonData['renderingEngines'][] = $renderingEngine;
        }

        $this->writeJsonData('/rendering-engines.json', $jsonData);
    }

    public function saveDevices()
    {
        $jsonData = array();
        $jsonData['comment'] = 'Registry of devices';
        $jsonData['devices'] = array();

        foreach ($this->devices as $device) {
            $jsonData['devices'][] = $device;
        }

        $this->writeJsonData('/devices.json', $jsonData);
    }

    public function savePlatforms()
    {
        $jsonData = array();
        $jsonData['comment'] = 'Registry of platforms';
        $jsonData['platforms'] = array();

        foreach ($this->platforms as $platform) {
            $jsonData['platforms'][] = $platform;
        }

        $this->writeJsonData('/platforms.json', $jsonData);
    }

    public function parseDivison($divisionName, $divisonData)
    {
        $jsonData = array();

        foreach ($divisonData as $sectionName => $sectionProperties) {
            #var_dump($sectionName, $sectionProperties);

            $this->safeCopyValue($sectionProperties, 'Browser', $jsonData, 'name');

            $this->safeCopyValue($sectionProperties, 'Comment', $jsonData, 'comment');
            $this->safeCopyValue($sectionProperties, 'Parent', $jsonData, 'parent');
            $this->safeCopyValue($sectionProperties, 'Browser', $jsonData, 'browser');

            if (isset($sectionProperties['Version']) && $sectionProperties['Version'] != '0.0') {
                $jsonData['versions'][] = $sectionProperties['Version'];
            }

            foreach ($this->propMap as $newProp => $oldProp) {
                if (!isset($sectionProperties[$oldProp])) {
                    continue;
                }

                $type = $this->generator->propertyType($newProp);

                switch ($type) {
                    case 'boolean':
                        $jsonData['properties'][$newProp] = ($sectionProperties[$oldProp] == 'true');
                        break;

                    case 'string':
                        $jsonData['properties'][$newProp] = (string)$sectionProperties[$oldProp];
                        break;
                }
            }

            if (isset($sectionProperties['RenderingEngine_Name']) && $sectionProperties['RenderingEngine_Name'] != 'unknown') {
                $renderingEngineKey = $sectionProperties['RenderingEngine_Name'];

                if (!isset($this->renderingEngines[$renderingEngineKey])) {
                    $renderingEngine = new RenderingEngine();
                    $renderingEngine->renderingEngineId = $this->lastRenderingEngineId++;
                    $renderingEngine->name = $sectionProperties['RenderingEngine_Name'];
                    $renderingEngine->description = $sectionProperties['RenderingEngine_Description'];
                    $renderingEngine->match = '';

                    $this->renderingEngines[$renderingEngineKey] = $renderingEngine;
                }

                $jsonData['renderingEngines'] = array($this->renderingEngines[$renderingEngineKey]->renderingEngineId);
            }

            if (isset($sectionProperties['Device_Name']) && $sectionProperties['Device_Name'] != 'unknown') {
                $deviceKey = $sectionProperties['Device_Name'];

                if (!isset($this->devices[$deviceKey])) {
                    $device = new Device();
                    $device->deviceId = $this->lastDeviceId++;
                    $device->name = $sectionProperties['Device_Name'];
                    $device->maker = $sectionProperties['Device_Maker'];
                    $device->match = '';

                    $this->devices[$deviceKey] = $device;
                }

                $jsonData['devices'] = array($this->devices[$deviceKey]->deviceId);
            }

            if (isset($sectionProperties['Platform']) && $sectionProperties['Platform'] != 'unknown') {
                if (isset($sectionProperties['Platform_Version'])) {
                    $platformKey = $sectionProperties['Platform'] . '__' . $sectionProperties['Platform_Version'];
                } else {
                    $platformKey = $sectionProperties['Platform'] . '__*';
                }

                if (!isset($this->platforms[$platformKey])) {
                    $platform = new Platform();
                    $platform->platformId = $this->lastPlatformId++;
                    $platform->name = $sectionProperties['Platform'];

                    if (isset($sectionProperties['Platform_Description'])) {
                        $platform->description = $sectionProperties['Platform_Description'];
                    } else {
                        $platform->description = '';
                    }

                    if (isset($sectionProperties['Platform_Version'])) {
                        $platform->version = $sectionProperties['Platform_Version'];
                    } else {
                        $platform->version = '';
                    }

                    $platform->match = '';

                    $this->platforms[$platformKey] = $platform;
                }

                $jsonData['platforms'] = array($this->platforms[$platformKey]->platformId);
            }
        }

        $filename = $this->getJsonFilenameFromDivisionName($divisionName);
        $this->writeJsonData('/user-agents/' . $filename, $jsonData);
    }

    public function safeCopyValue($sourceProps, $sourcePropName, &$destProps, $destPropName)
    {
        if (isset($sourceProps[$sourcePropName])) {
            $destProps[$destPropName] = $sourceProps[$sourcePropName];
        }
    }
}
