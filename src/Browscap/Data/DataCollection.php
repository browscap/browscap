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
    private $defaultProperties = [];

    /**
     * @var \Browscap\Data\Division
     */
    private $defaultBrowser = [];

    /**
     * @var bool
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
     * @var string[]
     */
    private $allDivision = [];

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
     * @return \Browscap\Data\DataCollection
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return \Psr\Log\LoggerInterface $logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Load a platforms.json file and parse it into the platforms data array
     *
     * @param string $src Name of the file
     *
     * @throws \RuntimeException         if the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException
     *
     * @return \Browscap\Data\DataCollection
     */
    public function addPlatformsFile($src)
    {
        $json = $this->loadFile($src);

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

        return $this;
    }

    /**
     * Load a engines.json file and parse it into the platforms data array
     *
     * @param string $src Name of the file
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return \Browscap\Data\DataCollection
     */
    public function addEnginesFile($src)
    {
        $json = $this->loadFile($src);

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

        return $this;
    }

    /**
     * Load a devices.json file and parse it into the platforms data array
     *
     * @param string $src Name of the file
     *
     * @throws \RuntimeException         if the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException if the properties and the inherits kyewords are missing
     *
     * @return \Browscap\Data\DataCollection
     */
    public function addDevicesFile($src)
    {
        $json          = $this->loadFile($src);
        $deviceFactory = new Factory\DeviceFactory();

        foreach ($json as $deviceName => $deviceData) {
            if (!isset($deviceData['properties']) && !isset($deviceData['inherits'])) {
                throw new \UnexpectedValueException('required attibute "properties" is missing');
            }

            $this->devices[$deviceName] = $deviceFactory->build($deviceData, $json, $deviceName);
        }

        $this->divisionsHaveBeenSorted = false;

        return $this;
    }

    /**
     * Load a JSON file, parse it's JSON and add it to our divisions list
     *
     * @param string $src Name of the file
     *
     * @throws \RuntimeException         If the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException If required attibutes are missing in the division
     * @throws \LogicException
     *
     * @return \Browscap\Data\DataCollection
     */
    public function addSourceFile($src)
    {
        $divisionData = $this->loadFile($src);

        if (!array_key_exists('division', $divisionData)) {
            throw new \UnexpectedValueException('required attibute "division" is missing in File ' . $src);
        }

        if (!array_key_exists('sortIndex', $divisionData)) {
            throw new \UnexpectedValueException('required attibute "sortIndex" is missing in File ' . $src);
        }

        if (!array_key_exists('lite', $divisionData)) {
            throw new \UnexpectedValueException('required attibute "lite" is missing in File ' . $src);
        }

        if (!array_key_exists('standard', $divisionData)) {
            throw new \UnexpectedValueException('required attibute "standard" is missing in File ' . $src);
        }

        if (isset($divisionData['versions']) && is_array($divisionData['versions'])) {
            $versions = $divisionData['versions'];
        } else {
            $versions = ['0.0'];
        }

        if (isset($divisionData['userAgents']) && is_array($divisionData['userAgents'])) {
            foreach ($divisionData['userAgents'] as $useragent) {
                if (!isset($useragent['userAgent'])) {
                    throw new \UnexpectedValueException('Name for Division is missing');
                }

                if (preg_match('/[\[\]]/', $useragent['userAgent'])) {
                    throw new \UnexpectedValueException(
                        'Name of Division "' . $useragent['userAgent'] . '" includes invalid characters'
                    );
                }

                if (false === mb_strpos($useragent['userAgent'], '#')
                    && in_array($useragent['userAgent'], $this->allDivision)
                ) {
                    throw new \UnexpectedValueException('Division "' . $useragent['userAgent'] . '" is defined twice');
                }

                if ((false !== mb_strpos($useragent['userAgent'], '#MAJORVER#')
                        || false !== mb_strpos($useragent['userAgent'], '#MINORVER#'))
                    && ['0.0'] === $versions
                ) {
                    throw new \UnexpectedValueException(
                        'Division "' . $useragent['userAgent']
                        . '" is defined with version placeholders, but no versions are set'
                    );
                }

                if (!isset($useragent['properties'])) {
                    throw new \UnexpectedValueException(
                        'the properties entry is missing for key "' . $useragent['userAgent'] . '"'
                    );
                }

                if (!is_array($useragent['properties'])) {
                    throw new \UnexpectedValueException(
                        'the properties entry has to be an array for key "' . $useragent['userAgent'] . '"'
                    );
                }

                if (!isset($useragent['properties']['Parent'])) {
                    throw new \UnexpectedValueException(
                        'the "Parent" property is missing for key "' . $useragent['userAgent'] . '"'
                    );
                }

                if ('DefaultProperties' !== $useragent['properties']['Parent']) {
                    throw new \UnexpectedValueException(
                        'the "Parent" property is not linked to the "DefaultProperties" for key "'
                        . $useragent['userAgent'] . '"'
                    );
                }

                if (!isset($useragent['properties']['Comment'])) {
                    throw new \UnexpectedValueException(
                        'the "Comment" property is missing for key "' . $useragent['userAgent'] . '"'
                    );
                }

                if (isset($useragent['properties']['Version']) && ['0.0'] === $versions) {
                    throw new \UnexpectedValueException(
                        'the "Version" property is set for key "' . $useragent['userAgent']
                        . '", but no versions are defined'
                    );
                }

                if (!isset($useragent['properties']['Version']) && ['0.0'] !== $versions) {
                    throw new \UnexpectedValueException(
                        'the "Version" property is missing for key "' . $useragent['userAgent']
                        . '", but there are defined versions'
                    );
                }

                if (!isset($useragent['children'])) {
                    throw new \UnexpectedValueException(
                        'the children property is missing for key "' . $useragent['userAgent'] . '"'
                    );
                }

                if (!is_array($useragent['children'])) {
                    throw new \UnexpectedValueException(
                        'the children property has to be an array for key "' . $useragent['userAgent'] . '"'
                    );
                }

                if (isset($useragent['children']['match'])) {
                    throw new \UnexpectedValueException(
                        'the children property shall not have the "match" entry for key "' . $useragent['userAgent'] . '"'
                    );
                }

                $this->checkPlatformData(
                    $useragent['properties'],
                    'the properties array contains platform data for key "' . $useragent['userAgent']
                    . '", please use the "platform" keyword'
                );

                $this->checkEngineData(
                    $useragent['properties'],
                    'the properties array contains engine data for key "' . $useragent['userAgent']
                    . '", please use the "engine" keyword'
                );

                $this->checkDeviceData(
                    $useragent['properties'],
                    'the properties array contains device data for key "' . $useragent['userAgent']
                    . '", please use the "device" keyword'
                );

                foreach ($useragent['children'] as $child) {
                    if (!is_array($child)) {
                        throw new \UnexpectedValueException(
                            'each entry of the children property has to be an array for key "'
                            . $useragent['userAgent'] . '"'
                        );
                    }

                    if (isset($child['device']) && isset($child['devices'])) {
                        throw new \LogicException(
                            'a child may not define both the "device" and the "devices" entries for key "'
                            . $useragent['userAgent'] . '", for child data: ' . json_encode($child)
                        );
                    }

                    if (isset($child['devices']) && !is_array($child['devices'])) {
                        throw new \UnexpectedValueException(
                            'the "devices" entry has to be an array for key "'
                            . $useragent['userAgent'] . '", for child data: ' . json_encode($child)
                        );
                    }

                    if (!isset($child['match'])) {
                        throw new \UnexpectedValueException(
                            'each entry of the children property requires an "match" entry for key "'
                            . $useragent['userAgent'] . '", missing for child data: ' . json_encode($child)
                        );
                    }

                    if (isset($child['platforms'])
                        && count($child['platforms']) > 1
                        && mb_strpos($child['match'], '#PLATFORM#') === false
                    ) {
                        throw new \LogicException(
                            'the "platforms" entry contains multiple platforms but there is no #PLATFORM# token for key "'
                            . $useragent['userAgent'] . '", for child data: ' . json_encode($child)
                        );
                    }

                    if (isset($child['devices'])
                        && count($child['devices']) > 1
                        && mb_strpos($child['match'], '#DEVICE#') === false
                    ) {
                        throw new \LogicException(
                            'the "devices" entry contains multiple devices but there is no #DEVICE# token for key "'
                            . $useragent['userAgent'] . '", for child data: ' . json_encode($child)
                        );
                    }

                    if (preg_match('/[\[\]]/', $child['match'])) {
                        throw new \UnexpectedValueException(
                            'key "' . $child['match'] . '" includes invalid characters'
                        );
                    }

                    if ((false !== mb_strpos($child['match'], '#MAJORVER#')
                            || false !== mb_strpos($child['match'], '#MINORVER#'))
                        && ['0.0'] === $versions
                    ) {
                        throw new \UnexpectedValueException(
                            'the key "' . $child['match']
                            . '" is defined with version placeholders, but no versions are set'
                        );
                    }

                    if (false !== mb_strpos($child['match'], '#PLATFORM#')
                        && !isset($child['platforms'])
                    ) {
                        throw new \UnexpectedValueException(
                            'the key "' . $child['match']
                            . '" is defined with platform placeholder, but no platforms are assigned'
                        );
                    }

                    if (false !== mb_strpos($child['match'], '#DEVICE#')
                        && !isset($child['devices'])
                    ) {
                        throw new \UnexpectedValueException(
                            'the key "' . $child['match']
                            . '" is defined with device placeholder, but no devices are assigned'
                        );
                    }

                    if (isset($child['properties'])) {
                        if (!is_array($child['properties'])) {
                            throw new \UnexpectedValueException(
                                'the properties entry has to be an array for key "' . $child['match'] . '"'
                            );
                        }

                        if (isset($child['properties']['Parent'])) {
                            throw new \UnexpectedValueException(
                                'the Parent property must not set inside the children array for key "'
                                . $child['match'] . '"'
                            );
                        }

                        if (isset($useragent['properties']['Version'])
                            && isset($child['properties']['Version'])
                            && $useragent['properties']['Version'] === $child['properties']['Version']
                        ) {
                            $this->logger->warning(
                                'the "Version" property is set for key "' . $child['match']
                                . '", but was already set for its parent "' . $useragent['userAgent'] . '"'
                            );
                        }

                        $this->checkPlatformData(
                            $child['properties'],
                            'the properties array contains platform data for key "' . $child['match']
                            . '", please use the "platforms" keyword'
                        );

                        $this->checkEngineData(
                            $child['properties'],
                            'the properties array contains engine data for key "' . $child['match']
                            . '", please use the "engine" keyword'
                        );

                        $this->checkDeviceData(
                            $child['properties'],
                            'the properties array contains device data for key "' . $child['match']
                            . '", please use the "device" keyword'
                        );
                    }
                }

                $this->allDivision[] = $useragent['userAgent'];
            }

            $userAgents = $divisionData['userAgents'];
        } else {
            $userAgents = [];
        }

        $this->divisions[] = new Division(
            $divisionData['division'],
            (int) $divisionData['sortIndex'],
            $userAgents,
            (bool) $divisionData['lite'],
            (bool) $divisionData['standard'],
            $versions,
            mb_substr($src, (int) mb_strpos($src, 'resources/'))
        );

        $this->divisionsHaveBeenSorted = false;

        return $this;
    }

    /**
     * @param string $src
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    private function loadFile($src)
    {
        if (!file_exists($src)) {
            throw new \RuntimeException('File "' . $src . '" does not exist.');
        }

        if (!is_readable($src)) {
            throw new \RuntimeException('File "' . $src . '" is not readable.');
        }

        $fileContent = file_get_contents($src);

        if (preg_match('/[^ -~\s]/', $fileContent)) {
            throw new \RuntimeException('File "' . $src . '" contains Non-ASCII-Characters.');
        }

        $json        = json_decode($fileContent, true);

        if (null === $json) {
            throw new \RuntimeException('File "' . $src . '" had invalid JSON.');
        }

        return $json;
    }

    /**
     * checks if platform properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     */
    private function checkPlatformData(array $properties, $message)
    {
        if (array_key_exists('Platform', $properties)
            || array_key_exists('Platform_Description', $properties)
            || array_key_exists('Platform_Maker', $properties)
            || array_key_exists('Platform_Bits', $properties)
            || array_key_exists('Platform_Version', $properties)
            || array_key_exists('Win16', $properties)
            || array_key_exists('Win32', $properties)
            || array_key_exists('Win64', $properties)
            || array_key_exists('Browser_Bits', $properties)
        ) {
            throw new \LogicException($message);
        }
    }

    /**
     * checks if platform properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     */
    private function checkEngineData(array $properties, $message)
    {
        if (array_key_exists('RenderingEngine_Name', $properties)
            || array_key_exists('RenderingEngine_Version', $properties)
            || array_key_exists('RenderingEngine_Description', $properties)
            || array_key_exists('RenderingEngine_Maker', $properties)
            || array_key_exists('VBScript', $properties)
            || array_key_exists('ActiveXControls', $properties)
            || array_key_exists('BackgroundSounds', $properties)
        ) {
            throw new \LogicException($message);
        }
    }

    /**
     * checks if device properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     */
    private function checkDeviceData(array $properties, $message)
    {
        if (array_key_exists('Device_Name', $properties)
            || array_key_exists('Device_Maker', $properties)
            || array_key_exists('Device_Type', $properties)
            || array_key_exists('Device_Pointing_Method', $properties)
            || array_key_exists('Device_Code_Name', $properties)
            || array_key_exists('Device_Brand_Name', $properties)
            || array_key_exists('isMobileDevice', $properties)
            || array_key_exists('isTablet', $properties)
        ) {
            throw new \LogicException($message);
        }
    }

    /**
     * Load the file for the default properties
     *
     * @param string $src Name of the file
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return \Browscap\Data\DataCollection
     */
    public function addDefaultProperties($src)
    {
        $divisionData = $this->loadFile($src);

        $this->defaultProperties = new Division(
            $divisionData['division'],
            (int) $divisionData['sortIndex'],
            $divisionData['userAgents'],
            true
        );

        $this->divisionsHaveBeenSorted = false;

        return $this;
    }

    /**
     * Load the file for the default browser
     *
     * @param string $src Name of the file
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return \Browscap\Data\DataCollection
     */
    public function addDefaultBrowser($src)
    {
        $divisionData = $this->loadFile($src);

        $this->defaultBrowser = new Division(
            $divisionData['division'],
            (int) $divisionData['sortIndex'],
            $divisionData['userAgents'],
            true
        );

        $this->divisionsHaveBeenSorted = false;

        return $this;
    }

    /**
     * Get the divisions array containing UA data
     *
     * @return \Browscap\Data\Division[]
     */
    public function getDivisions()
    {
        $this->sortDivisions();

        return $this->divisions;
    }

    /**
     * Sort the divisions (if they haven't already been sorted)
     *
     * @return \Browscap\Data\DataCollection
     */
    public function sortDivisions()
    {
        if (!$this->divisionsHaveBeenSorted) {
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

        return $this;
    }

    /**
     * Get the divisions array containing UA data
     *
     * @return \Browscap\Data\Division
     */
    public function getDefaultProperties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the divisions array containing UA data
     *
     * @return \Browscap\Data\Division
     */
    public function getDefaultBrowser()
    {
        return $this->defaultBrowser;
    }

    /**
     * Get the array of platform data
     *
     * @return \Browscap\Data\Platform[]
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
     *
     * @return \Browscap\Data\Platform
     */
    public function getPlatform($platform)
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
    public function getEngines()
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
    public function getEngine($engine)
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
    public function getDevices()
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
    public function getDevice($device)
    {
        if (!array_key_exists($device, $this->devices)) {
            throw new \OutOfBoundsException(
                'Device "' . $device . '" does not exist in data'
            );
        }

        return $this->devices[$device];
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
     * @param string $key
     * @param array  $properties
     *
     * @throws \UnexpectedValueException
     *
     * @return bool
     */
    public function checkProperty($key, array $properties)
    {
        $this->getLogger()->debug('check if all required propeties are available');

        if (!isset($properties['Version'])) {
            throw new \UnexpectedValueException('Version property not found for key "' . $key . '"');
        }

        if (!isset($properties['Parent']) && !in_array($key, ['DefaultProperties', '*'])) {
            throw new \UnexpectedValueException('Parent property is missing for key "' . $key . '"');
        }

        if (!isset($properties['Device_Type'])) {
            throw new \UnexpectedValueException('property "Device_Type" is missing for key "' . $key . '"');
        }

        if (!isset($properties['isTablet'])) {
            throw new \UnexpectedValueException('property "isTablet" is missing for key "' . $key . '"');
        }

        if (!isset($properties['isMobileDevice'])) {
            throw new \UnexpectedValueException('property "isMobileDevice" is missing for key "' . $key . '"');
        }

        switch ($properties['Device_Type']) {
            case 'Tablet':
                if (true !== $properties['isTablet']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type'] . '" is NOT marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true !== $properties['isMobileDevice']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type']
                        . '" is NOT marked as Mobile Device for key "' . $key . '"'
                    );
                }
                break;
            case 'Mobile Phone':
            case 'Mobile Device':
            case 'Ebook Reader':
            case 'Console':
            case 'Digital Camera':
                if (true === $properties['isTablet']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true !== $properties['isMobileDevice']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type']
                        . '" is NOT marked as Mobile Device for key "' . $key . '"'
                    );
                }
                break;
            case 'TV Device':
            case 'Desktop':
            default:
                if (true === $properties['isTablet']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true === $properties['isMobileDevice']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Mobile Device for key "'
                        . $key . '"'
                    );
                }
                break;
        }

        return true;
    }
}
