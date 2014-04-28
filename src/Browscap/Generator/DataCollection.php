<?php

namespace Browscap\Generator;

use Psr\Log\LoggerInterface;

/**
 * Class DataCollection
 *
 * @package Browscap\Generator
 */
class DataCollection
{
    /**
     * @var array
     */
    private $platforms = array();

    /**
     * @var array
     */
    private $divisions = array();

    /**
     * @var boolean
     */
    private $divisionsHaveBeenSorted = false;

    /**
     * @var string
     */
    private $version;

    /**
     * @var \DateTime
     */
    private $generationDate;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * Create a new data collection for the specified version
     *
     * @param string $version
     */
    public function __construct($version)
    {
        $this->version        = $version;
        $this->generationDate = new \DateTime();
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Generator\DataCollection
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Load a platforms.json file and parse it into the platforms data array
     *
     * @param string $src Name of the file
     *
     * @return \Browscap\Generator\DataCollection
     * @throws \Exception if the file does not exist or has invalid JSON
     */
    public function addPlatformsFile($src)
    {
        $json = $this->loadFile($src);

        $this->platforms = $json['platforms'];

        $this->divisionsHaveBeenSorted = false;

        return $this;
    }

    /**
     * Load a JSON file, parse it's JSON and add it to our divisions list
     *
     * @param string $src Name of the file
     *
     * @return \Browscap\Generator\DataCollection
     * @throws \Exception if the file does not exist or has invalid JSON
     */
    public function addSourceFile($src)
    {
        $this->divisions[] = $this->loadFile($src);

        $this->divisionsHaveBeenSorted = false;

        return $this;
    }

    /**
     * @param string $src
     *
     * @return array
     * @throws \RuntimeException
     */
    private function loadFile($src)
    {
        if (!file_exists($src)) {
            throw new \RuntimeException('File "' . $src . '" does not exist.');
        }

        $fileContent = file_get_contents($src);
        $json        = json_decode($fileContent, true);

        if (is_null($json)) {
            throw new \RuntimeException('File "' . $src . '" had invalid JSON.');
        }

        return $json;
    }

    /**
     * Sort the divisions (if they haven't already been sorted)
     */
    public function sortDivisions()
    {
        if (!$this->divisionsHaveBeenSorted) {
            $sortIndex    = array();
            $sortPosition = array();

            foreach ($this->divisions as $key => $division) {
                $sortIndex[$key]    = (isset($division['sortIndex']) ? $division['sortIndex'] : 0);
                $sortPosition[$key] = $key;
            }

            array_multisort(
                $sortIndex, SORT_ASC, SORT_NUMERIC,
                $sortPosition, SORT_DESC, SORT_NUMERIC, // if the sortIndex is identical the later added file comes first
                $this->divisions
            );

            $this->divisionsHaveBeenSorted = true;
        }
    }

    /**
     * Get the divisions array containing UA data
     *
     * @return array
     */
    public function getDivisions()
    {
        $this->sortDivisions();

        return $this->divisions;
    }

    /**
     * Get the array of platform data
     *
     * @return array
     */
    public function getPlatforms()
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
     * @return array
     */
    public function getPlatform($platform)
    {
        if (!array_key_exists($platform, $this->platforms)) {
            throw new \OutOfBoundsException(
                'Platform "' . $platform . '" does not exist in data, available platforms: '
                . serialize(array_keys($this->platforms))
            );
        }

        /** @var array $platformData */
        $platformData = $this->platforms[$platform];

        if (array_key_exists('inherits', $platformData)) {
            $parentPlatformData = $this->getPlatform($platformData['inherits']);

            if (array_key_exists('properties', $platformData)) {
                $inheritedPlatformProperties = $platformData['properties'];

                foreach ($inheritedPlatformProperties as $name => $value) {
                    if (isset($parentPlatformData['properties'][$name])
                        && $parentPlatformData['properties'][$name] == $value
                    ) {
                        throw new \UnexpectedValueException(
                            'the value for property "' . $name .'" has the same value in the keys "' . $platform
                            . '" and its parent "' . $platformData['inherits'] . '"'
                        );
                    }
                }

                $platformData['properties'] = array_merge(
                    $parentPlatformData['properties'],
                    $inheritedPlatformProperties
                );
            } else {
                $platformData['properties'] = $parentPlatformData['properties'];
            }

            unset($platformData['inherits']);
        }

        return $platformData;
    }

    /**
     * Get the version string identifier
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get the generation DateTime object
     *
     * @return \DateTime
     */
    public function getGenerationDate()
    {
        return $this->generationDate;
    }
}
