<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Assert\Assertion;
use Browscap\Data\DataCollection;
use Browscap\Data\Validator\DivisionDataValidator;
use Psr\Log\LoggerInterface;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;

class DataCollectionFactory
{
    /**
     * @var DataCollection
     */
    private $collection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DivisionFactory
     */
    private $divisionFactory;

    /**
     * @var DeviceFactory
     */
    private $deviceFactory;

    /**
     * @var DivisionDataValidator
     */
    private $divisionDataValidator;

    /**
     * @var string[]
     */
    private $allDivisions = [];

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger                = $logger;
        $useragentFactory            = new UserAgentFactory();
        $this->divisionFactory       = new DivisionFactory($logger, $useragentFactory);
        $this->deviceFactory         = new DeviceFactory();
        $this->divisionDataValidator = new DivisionDataValidator();
        $this->collection            = new DataCollection($logger);
    }

    /**
     * Create and populate a data collection object from a resource folder
     *
     * @param string $resourceFolder
     *
     * @throws \LogicException
     *
     * @return DataCollection
     */
    public function createDataCollection(string $resourceFolder) : DataCollection
    {
        $this->logger->debug('add platform file');
        $this->addPlatformsFile($resourceFolder . '/platforms.json');

        $this->logger->debug('add engine file');
        $this->addEnginesFile($resourceFolder . '/engines.json');

        $this->logger->debug('add file for default properties');
        $this->setDefaultProperties($resourceFolder . '/core/default-properties.json');

        $this->logger->debug('add file for default browser');
        $this->setDefaultBrowser($resourceFolder . '/core/default-browser.json');

        $deviceDirectory = $resourceFolder . '/devices';

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($deviceDirectory)) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || 'json' !== $file->getExtension()) {
                continue;
            }

            $this->logger->debug('add device file ' . $file->getPathname());
            $this->addDevicesFile($file->getPathname());
        }

        $uaSourceDirectory = $resourceFolder . '/user-agents';

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($uaSourceDirectory)) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || 'json' !== $file->getExtension()) {
                continue;
            }

            $this->logger->debug('add source file ' . $file->getPathname());
            $this->addSourceFile($file->getPathname());
        }

        return $this->collection;
    }

    /**
     * Load a platforms.json file and parse it into the platforms data array, validates these data and creates a
     * collection of Platform objects
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException         if the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException
     */
    public function addPlatformsFile(string $filename) : void
    {
        $decodedFileContent = $this->loadFile($filename);

        Assertion::keyExists($decodedFileContent, 'platforms', 'required "platforms" structure is missing');
        Assertion::isArray($decodedFileContent['platforms'], 'required "platforms" structure has to be an array');

        $platformFactory = new PlatformFactory();

        foreach (array_keys($decodedFileContent['platforms']) as $platformName) {
            $platformData = $decodedFileContent['platforms'][$platformName];

            $this->collection->addPlatform($platformName, $platformFactory->build($platformData, $decodedFileContent['platforms'], $platformName));
        }
    }

    /**
     * Load a engines.json file and parse it into the platforms data array, validates these data and creates a
     * collection of Engine objects
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     */
    public function addEnginesFile(string $filename) : void
    {
        $decodedFileContent = $this->loadFile($filename);

        Assertion::keyExists($decodedFileContent, 'engines', 'required "engines" structure is missing');
        Assertion::isArray($decodedFileContent['engines'], 'required "engines" structure has to be an array');

        $engineFactory = new EngineFactory();

        foreach (array_keys($decodedFileContent['engines']) as $engineName) {
            $engineData = $decodedFileContent['engines'][$engineName];

            $this->collection->addEngine($engineName, $engineFactory->build($engineData, $decodedFileContent['engines'], $engineName));
        }
    }

    /**
     * Load a devices.json file and parse it into the platforms data array, validates these data and creates a
     * collection of Device objects
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException         if the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException if the properties and the inherits kyewords are missing
     */
    public function addDevicesFile(string $filename) : void
    {
        $decodedFileContent = $this->loadFile($filename);

        foreach ($decodedFileContent as $deviceName => $deviceData) {
            $this->collection->addDevice($deviceName, $this->deviceFactory->build($deviceData, $deviceName));
        }
    }

    /**
     * Load a JSON file, parse it's JSON to arrays, validates these data and creates a
     * collection of Division objects
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException         If the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException If required attibutes are missing in the division
     * @throws \LogicException
     */
    public function addSourceFile(string $filename) : void
    {
        $divisionData = $this->loadFile($filename);

        $this->divisionDataValidator->validate($divisionData, $filename, $this->allDivisions, false);

        $this->collection->addDivision(
            $this->divisionFactory->build(
                $divisionData,
                $filename,
                false
            )
        );
    }

    /**
     * Load the file for the default properties
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     */
    public function setDefaultProperties(string $filename) : void
    {
        $divisionData = $this->loadFile($filename);
        $this->divisionDataValidator->validate($divisionData, $filename, $this->allDivisions, true);

        $this->collection->setDefaultProperties(
            $this->divisionFactory->build(
                $divisionData,
                $filename,
                true
            )
        );
    }

    /**
     * Load the file for the default browser
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     */
    public function setDefaultBrowser(string $filename) : void
    {
        $divisionData = $this->loadFile($filename);
        $this->divisionDataValidator->validate($divisionData, $filename, $this->allDivisions, true);

        $this->collection->setDefaultBrowser(
            $this->divisionFactory->build(
                $divisionData,
                $filename,
                true
            )
        );
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

        Assertion::readable($filename, 'File "' . $filename . '" is not readable.');

        $fileContent = file_get_contents($filename);

        Assertion::string($fileContent);

        if (preg_match('/[^ -~\s]/', $fileContent)) {
            throw new \RuntimeException('File "' . $filename . '" contains Non-ASCII-Characters.');
        }

        $jsonParser = new JsonParser();

        try {
            return $jsonParser->parse($fileContent, JsonParser::DETECT_KEY_CONFLICTS | JsonParser::PARSE_TO_ASSOC);
        } catch (ParsingException $e) {
            throw new \RuntimeException(
                'File "' . $filename . '" had invalid JSON. [JSON error: ' . json_last_error_msg() . ']',
                0,
                $e
            );
        }
    }
}
