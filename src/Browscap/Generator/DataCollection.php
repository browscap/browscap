<?php

namespace Browscap\Generator;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

class DataCollection
{
    /**
     * @var array
     */
    private $platforms;

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
     * @return \Browscap\Generator\BuildGenerator
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
     * @throws \Exception if the file does not exist or has invalid JSON
     */
    public function addPlatformsFile($src)
    {
        if (!file_exists($src)) {
            throw new \RuntimeException('File "' . $src . '" does not exist.');
        }

        $fileContent = file_get_contents($src);
        $json        = json_decode($fileContent, true);

        if (is_null($json)) {
            throw new \RuntimeException('File "' . $src . '" had invalid JSON.');
        }

        $this->platforms = $json['platforms'];
    }

    /**
     * Load a JSON file, parse it's JSON and add it to our divisions list
     *
     * @param string $src Name of the file
     *
     * @throws \Exception if the file does not exist or has invalid JSON
     */
    public function addSourceFile($src)
    {
        if (!file_exists($src)) {
            throw new \RuntimeException('File "' . $src . '" does not exist.');
        }

        $fileContent = file_get_contents($src);
        $json        = json_decode($fileContent, true);

        if (is_null($json)) {
            throw new \RuntimeException('File "' . $src . '" had invalid JSON.');
        }

        $this->divisions[] = $json;

        $this->divisionsHaveBeenSorted = false;
    }

    /**
     * Sort the divisions (if they haven't already been sorted)
     *
     * @return boolean
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
                $sortIndex, SORT_ASC,
                $sortPosition, SORT_DESC, // if the sortIndex is identical the later added file comes first
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
     * @return array
     */
    public function getPlatform($platform)
    {
        if (!array_key_exists($platform, $this->platforms)) {
            throw new \OutOfBoundsException('Platform "' . $platform . '" does not exist in data');
        }

        return $this->platforms[$platform];
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

    /**
     * @param string $message
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    private function log($message)
    {
        if (null === $this->logger) {
            return $this;
        }

        $this->logger->log(Logger::DEBUG, $message);

        return $this;
    }
}
