<?php

namespace Browscap\Generator;

class DataCollection
{
    /**
     * @var array
     */
    protected $platforms;

    /**
     * @var array
     */
    protected $divisions;

    /**
     * @var boolean
     */
    protected $divisionsHaveBeenSorted = false;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var \DateTime
     */
    protected $generationDate;

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
     * Load a platforms.json file and parse it into the platforms data array
     *
     * @param  string $src Name of the file
     *
     * @throws \Exception if the file does not exist or has invalid JSON
     */
    public function addPlatformsFile($src)
    {
        if (!file_exists($src)) {
            throw new \RuntimeException("File {$src} does not exist.");
        }

        $fileContent = file_get_contents($src);
        $json        = json_decode($fileContent, true);

        $this->platforms = $json['platforms'];

        if (is_null($this->platforms)) {
            throw new \RuntimeException('File "' . $src . '" had invalid JSON.');
        }
    }

    /**
     * Load a JSON file, parse it's JSON and add it to our divisions list
     *
     * @param  string $src Name of the file
     *
     * @throws \Exception if the file does not exist or has invalid JSON
     */
    public function addSourceFile($src)
    {
        if (!file_exists($src)) {
            throw new \RuntimeException("File {$src} does not exist.");
        }

        $fileContent = file_get_contents($src);
        $json        = json_decode($fileContent, true);

        $this->divisions[] = $json;

        if (is_null($json)) {
            throw new \RuntimeException('File "' . $src . '" had invalid JSON.');
        }
    }

    /**
     * Sort the divisions (if they haven't already been sorted)
     *
     * @return boolean
     */
    public function sortDivisions()
    {
        if (!$this->divisionsHaveBeenSorted) {
            usort(
                $this->divisions,
                function ($arrayA, $arrayB) {
                    $a = $arrayA['sortIndex'];
                    $b = $arrayB['sortIndex'];

                    if ($a < $b) {
                        return -1;
                    } elseif ($a > $b) {
                        return +1;
                    } else {
                        return 0;
                    }
                }
            );
        }

        return false;
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
     * @param  string $platform
     *
     * @throws \OutOfBoundsException
     * @return array
     */
    public function getPlatform($platform)
    {
        if (!array_key_exists($platform, $this->platforms)) {
            throw new \OutOfBoundsException("Platform '{$platform}' does not exist in data");
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
}
