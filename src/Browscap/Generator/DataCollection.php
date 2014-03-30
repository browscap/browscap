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
     * @throws \Exception if the file does not exist or has invalid JSON
     */
    public function addPlatformsFile($src)
    {
        $json = $this->loadFile($src);

        $this->platforms = $json['platforms'];

        $this->divisionsHaveBeenSorted = false;
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
        $this->divisions[] = $this->loadFile($src);

        $this->divisionsHaveBeenSorted = false;
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
            $sortCategory     = array();
            $sortParents      = array();
            $sortParentsFirst = array();
            $sortName         = array();
            $sortVersion      = array();
            $sortIndex        = array();
            $sortPosition     = array();

            $groups = array();

            foreach ($this->divisions as $key => $properties) {
                if (!empty($properties['Parents'])) {
                    $groups[$properties['Parents']][] = $key;
                }
            }


            foreach ($this->divisions as $key => $properties) {
                $category = 0;

                if (!empty($properties['Category'])) {
                    switch ($properties['Category']) {
                        case 'Bot/Crawler':
                            $category = 1;
                            break;
                        case 'Application':
                            $category = 2;
                            break;
                        case 'Email Client':
                            $category = 3;
                            break;
                        case 'Library':
                            $category = 4;
                            break;
                        case 'Browser':
                            $category = 8;
                            break;
                            break;
                        case 'all':
                            $category = 10;
                            break;
                        case 'unknown':
                        default:
                            // nothing to do here
                            break;
                    }
                }

                if ('DefaultProperties' === $key) {
                    $category = -1;
                }

                if ('*' === $key) {
                    $category = 11;
                }
                $sortCategory[$key] = $category;

                $parents = (empty($properties['Parents']) ? '' : $properties['Parents'] . ',') . $key;

                if (!empty($groups[$parents])) {
                    $group    = $parents;
                    $subgroup = 0;
                } elseif (!empty($properties['Parents'])) {
                    $group    = $properties['Parents'];
                    $subgroup = 1;
                } else {
                    $group    = '';
                    $subgroup = 2;
                }

                $sortParents[$key]      = strtolower($group);
                $sortParentsFirst[$key] = strtolower($subgroup);

                if (!empty($properties['Browser_Name'])) {
                    $sortName[$key] = strtolower($properties['Browser_Name']);
                } elseif (!empty($properties['Browser'])) {
                    $sortName[$key] = strtolower($properties['Browser']);
                } else {
                    $sortName[$key] = '';
                }

                if (!empty($properties['Browser_Version'])) {
                    $version = $properties['Browser_Version'];
                } elseif (!empty($properties['Version'])) {
                    $version = $properties['Version'];
                } else {
                    $version = 0.0;
                }

                switch ($version) {
                    case '3.1':
                        $version = 3.1;
                        break;
                    case '95':
                        $version = 3.2;
                        break;
                    case 'NT':
                        $version = 4.0;
                        break;
                    case '98':
                        $version = 4.1;
                        break;
                    case 'ME':
                        $version = 4.2;
                        break;
                    case '2000':
                        $version = 4.3;
                        break;
                    case 'XP':
                        $version = 4.4;
                        break;
                    case '2003':
                        $version = 4.5;
                        break;
                    case 'Vista':
                        $version = 6.0;
                        break;
                    case '7':
                        $version = 7.0;
                        break;
                    case '8':
                        $version = 8.0;
                        break;
                    default:
                        $version = (float)$version;
                        break;
                }

                $sortVersion[$key] = $version;


                $sortIndex[$key]    = (isset($properties['sortIndex']) ? $properties['sortIndex'] : 0);
                $sortPosition[$key] = $key;
            }

            array_multisort(
                $sortCategory, SORT_ASC, SORT_NUMERIC,
                $sortParents, SORT_ASC, SORT_STRING,
                $sortParentsFirst, SORT_ASC, SORT_NUMERIC,
                $sortName, SORT_ASC, SORT_STRING,
                $sortVersion, SORT_ASC, SORT_NUMERIC,
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
