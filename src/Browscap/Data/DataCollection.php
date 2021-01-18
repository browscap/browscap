<?php

declare(strict_types=1);

namespace Browscap\Data;

use OutOfBoundsException;
use UnexpectedValueException;

use function array_key_exists;
use function array_multisort;
use function assert;
use function sprintf;

use const SORT_ASC;
use const SORT_DESC;
use const SORT_NUMERIC;

class DataCollection
{
    /** @var Platform[] */
    private $platforms = [];

    /** @var Engine[] */
    private $engines = [];

    /** @var Browser[] */
    private $browsers = [];

    /** @var Device[] */
    private $devices = [];

    /** @var Division[] */
    private $divisions = [];

    /** @var Division */
    private $defaultProperties;

    /** @var Division */
    private $defaultBrowser;

    /** @var bool */
    private $divisionsHaveBeenSorted = false;

    /**
     * @param string $platformName Name of the platform
     */
    public function addPlatform(string $platformName, Platform $platform): void
    {
        $this->platforms[$platformName] = $platform;

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * @param string $engineName Name of the engine
     */
    public function addEngine(string $engineName, Engine $engine): void
    {
        $this->engines[$engineName] = $engine;

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * @param string $browserName Name of the browser
     */
    public function addBrowser(string $browserName, Browser $browser): void
    {
        if (array_key_exists($browserName, $this->browsers)) {
            throw new DuplicateDataException(
                sprintf('it was tried to add browser "%s", but this was already added before', $browserName)
            );
        }

        $this->browsers[$browserName] = $browser;

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * @param string $deviceName Name of the device
     */
    public function addDevice(string $deviceName, Device $device): void
    {
        if (array_key_exists($deviceName, $this->devices)) {
            throw new DuplicateDataException(
                sprintf('it was tried to add device "%s", but this was already added before', $deviceName)
            );
        }

        $this->devices[$deviceName] = $device;

        $this->divisionsHaveBeenSorted = false;
    }

    public function addDivision(Division $division): void
    {
        $this->divisions[] = $division;

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * Load the file for the default properties
     */
    public function setDefaultProperties(Division $division): void
    {
        $this->defaultProperties = $division;

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * Load the file for the default browser
     */
    public function setDefaultBrowser(Division $division): void
    {
        $this->defaultBrowser = $division;

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * Get the divisions array containing UA data
     *
     * @return Division[]
     */
    public function getDivisions(): array
    {
        $this->sortDivisions();

        return $this->divisions;
    }

    /**
     * Sort the divisions (if they haven't already been sorted)
     */
    private function sortDivisions(): void
    {
        if ($this->divisionsHaveBeenSorted) {
            return;
        }

        $sortIndex    = [];
        $sortPosition = [];

        foreach ($this->divisions as $key => $division) {
            assert($division instanceof Division);
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
     */
    public function getDefaultProperties(): Division
    {
        return $this->defaultProperties;
    }

    /**
     * Get the divisions array containing UA data
     */
    public function getDefaultBrowser(): Division
    {
        return $this->defaultBrowser;
    }

    /**
     * Get a single platform data array
     *
     * @throws OutOfBoundsException
     * @throws UnexpectedValueException
     */
    public function getPlatform(string $platform): Platform
    {
        if (! array_key_exists($platform, $this->platforms)) {
            throw new OutOfBoundsException(
                'Platform "' . $platform . '" does not exist in data'
            );
        }

        return $this->platforms[$platform];
    }

    /**
     * Get a single engine data array
     *
     * @throws OutOfBoundsException
     * @throws UnexpectedValueException
     */
    public function getEngine(string $engine): Engine
    {
        if (! array_key_exists($engine, $this->engines)) {
            throw new OutOfBoundsException(
                'Rendering Engine "' . $engine . '" does not exist in data'
            );
        }

        return $this->engines[$engine];
    }

    /**
     * Get a single browser data array
     *
     * @throws OutOfBoundsException
     * @throws UnexpectedValueException
     */
    public function getBrowser(string $browser): Browser
    {
        if (! array_key_exists($browser, $this->browsers)) {
            throw new OutOfBoundsException(
                'Browser "' . $browser . '" does not exist in data'
            );
        }

        return $this->browsers[$browser];
    }

    /**
     * Get a single engine data array
     *
     * @throws OutOfBoundsException
     * @throws UnexpectedValueException
     */
    public function getDevice(string $device): Device
    {
        if (! array_key_exists($device, $this->devices)) {
            throw new OutOfBoundsException(
                'Device "' . $device . '" does not exist in data'
            );
        }

        return $this->devices[$device];
    }
}
