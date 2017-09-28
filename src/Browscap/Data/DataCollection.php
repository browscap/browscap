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
namespace Browscap\Data;

use Browscap\Data\Factory\DivisionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class DataCollection
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class DataCollection
{
    /**
     * @var \Browscap\Data\Platform[]
     */
    private $platforms = [];

    /**
     * @var \Browscap\Data\Engine[]
     */
    private $engines = [];

    /**
     * @var \Browscap\Data\Device[]
     */
    private $devices = [];

    /**
     * @var \Browscap\Data\Division[]
     */
    private $divisions = [];

    /**
     * @var \Browscap\Data\Division
     */
    private $defaultProperties;

    /**
     * @var \Browscap\Data\Division
     */
    private $defaultBrowser;

    /**
     * @var bool
     */
    private $divisionsHaveBeenSorted = false;

    /**
     * @var \DateTime
     */
    private $generationDate;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var string[]
     */
    private $allDivisions = [];

    /**
     * @var DivisionFactory
     */
    private $divisionFactory;

    /**
     * Create a new data collection for the specified version
     *
     * @param string                   $version
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger          = $logger;
        $this->generationDate  = new \DateTime();
        $this->divisionFactory = new DivisionFactory($logger);
    }

    /**
     * Load a platforms.json file and parse it into the platforms data array
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException         if the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException
     *
     * @return void
     */
    public function addPlatformsFile(string $filename) : void
    {
        $json = $this->loadFile($filename);

        if (!isset($json['platforms'])) {
            throw new \UnexpectedValueException('required "platforms" structure is missing');
        }

        $platformFactory = new Factory\PlatformFactory();

        foreach (array_keys($json['platforms']) as $platformName) {
            $platformData = $json['platforms'][$platformName];

            if (!isset($platformData['match'])) {
                throw new \UnexpectedValueException('required attibute "match" is missing');
            }

            if (!isset($platformData['properties']) && !isset($platformData['inherits'])) {
                throw new \UnexpectedValueException('required attibute "properties" is missing');
            }

            $this->platforms[$platformName] = $platformFactory->build($platformData, $json, $platformName);
        }

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * Load a engines.json file and parse it into the platforms data array
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return void
     */
    public function addEnginesFile(string $filename) : void
    {
        $json = $this->loadFile($filename);

        if (!isset($json['engines'])) {
            throw new \UnexpectedValueException('required "engines" structure is missing');
        }

        $engineFactory = new Factory\EngineFactory();

        foreach (array_keys($json['engines']) as $engineName) {
            $engineData = $json['engines'][$engineName];

            if (!isset($engineData['properties']) && !isset($engineData['inherits'])) {
                throw new \UnexpectedValueException('required attibute "properties" is missing');
            }

            $this->engines[$engineName] = $engineFactory->build($engineData, $json, $engineName);
        }

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * Load a devices.json file and parse it into the platforms data array
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException         if the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException if the properties and the inherits kyewords are missing
     *
     * @return void
     */
    public function addDevicesFile(string $filename) : void
    {
        $json          = $this->loadFile($filename);
        $deviceFactory = new Factory\DeviceFactory();

        foreach ($json as $deviceName => $deviceData) {
            if (!isset($deviceData['properties']) && !isset($deviceData['inherits'])) {
                throw new \UnexpectedValueException('required attibute "properties" is missing');
            }

            $this->devices[$deviceName] = $deviceFactory->build($deviceData, $json, $deviceName);
        }

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * Load a JSON file, parse it's JSON and add it to our divisions list
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException         If the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException If required attibutes are missing in the division
     * @throws \LogicException
     *
     * @return void
     */
    public function addSourceFile(string $filename) : void
    {
        $divisionData = $this->loadFile($filename);

        $this->divisions[] = $this->divisionFactory->build(
            $divisionData,
            $filename,
            $this->allDivisions,
            false
        );

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * Load the file for the default properties
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return void
     */
    public function addDefaultProperties(string $filename) : void
    {
        $divisionData = $this->loadFile($filename);
        $allDivisions = [];

        $this->defaultProperties = $this->divisionFactory->build(
            $divisionData,
            $filename,
            $allDivisions,
            true
        );

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * Load the file for the default browser
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return void
     */
    public function addDefaultBrowser(string $filename) : void
    {
        $divisionData = $this->loadFile($filename);
        $allDivisions = [];

        $this->defaultBrowser = $this->divisionFactory->build(
            $divisionData,
            $filename,
            $allDivisions,
            true
        );

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * @param string $filename
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    private function loadFile(string $filename) : array
    {
        if (!file_exists($filename)) {
            throw new \RuntimeException('File "' . $filename . '" does not exist.');
        }

        if (!is_readable($filename)) {
            throw new \RuntimeException('File "' . $filename . '" is not readable.');
        }

        $fileContent = file_get_contents($filename);

        if (preg_match('/[^ -~\s]/', $fileContent)) {
            throw new \RuntimeException('File "' . $filename . '" contains Non-ASCII-Characters.');
        }

        $json = json_decode($fileContent, true);

        if (null === $json) {
            throw new \RuntimeException(
                'File "' . $filename . '" had invalid JSON. [JSON error: ' . json_last_error_msg() . ']'
            );
        }

        return $json;
    }

    /**
     * Get the divisions array containing UA data
     *
     * @return \Browscap\Data\Division[]
     */
    public function getDivisions() : array
    {
        $this->sortDivisions();

        return $this->divisions;
    }

    /**
     * Sort the divisions (if they haven't already been sorted)
     *
     * @return void
     */
    public function sortDivisions() : void
    {
        if ($this->divisionsHaveBeenSorted) {
            return;
        }

        $sortIndex    = [];
        $sortPosition = [];

        foreach ($this->divisions as $key => $division) {
            /* @var \Browscap\Data\Division $division */
            $sortIndex[$key]    = $division->getSortIndex();
            $sortPosition[$key] = $key;
        }

        array_multisort(
            $sortIndex,
            SORT_ASC,
            SORT_NUMERIC,
            $sortPosition,
            SORT_DESC,
            SORT_NUMERIC, // if the sortIndex is identical the later added file comes first
            $this->divisions
        );

        $this->divisionsHaveBeenSorted = true;
    }

    /**
     * Get the divisions array containing UA data
     *
     * @return \Browscap\Data\Division
     */
    public function getDefaultProperties() : Division
    {
        return $this->defaultProperties;
    }

    /**
     * Get the divisions array containing UA data
     *
     * @return \Browscap\Data\Division
     */
    public function getDefaultBrowser() : Division
    {
        return $this->defaultBrowser;
    }

    /**
     * Get the array of platform data
     *
     * @return \Browscap\Data\Platform[]
     */
    public function getPlatforms() : array
    {
        return $this->platforms;
    }

    /**
     * Get a single platform data array
     *
     * @param string $platform
     *
     * @throws \OutOfBoundsException
     * @throws \UnexpectedValueException
     *
     * @return \Browscap\Data\Platform
     */
    public function getPlatform(string $platform) : Platform
    {
        if (!array_key_exists($platform, $this->platforms)) {
            throw new \OutOfBoundsException(
                'Platform "' . $platform . '" does not exist in data'
            );
        }

        return $this->platforms[$platform];
    }

    /**
     * Get the array of engine data
     *
     * @return \Browscap\Data\Engine[]
     */
    public function getEngines() : array
    {
        return $this->engines;
    }

    /**
     * Get a single engine data array
     *
     * @param string $engine
     *
     * @throws \OutOfBoundsException
     * @throws \UnexpectedValueException
     *
     * @return \Browscap\Data\Engine
     */
    public function getEngine(string $engine) : Engine
    {
        if (!array_key_exists($engine, $this->engines)) {
            throw new \OutOfBoundsException(
                'Rendering Engine "' . $engine . '" does not exist in data'
            );
        }

        return $this->engines[$engine];
    }

    /**
     * Get the array of engine data
     *
     * @return \Browscap\Data\Device[]
     */
    public function getDevices() : array
    {
        return $this->devices;
    }

    /**
     * Get a single engine data array
     *
     * @param string $device
     *
     * @throws \OutOfBoundsException
     * @throws \UnexpectedValueException
     *
     * @return \Browscap\Data\Device
     */
    public function getDevice(string $device) : Device
    {
        if (!array_key_exists($device, $this->devices)) {
            throw new \OutOfBoundsException(
                'Device "' . $device . '" does not exist in data'
            );
        }

        return $this->devices[$device];
    }

    /**
     * Get the generation DateTime object
     *
     * @return \DateTime
     */
    public function getGenerationDate() : \DateTime
    {
        return $this->generationDate;
    }
}
