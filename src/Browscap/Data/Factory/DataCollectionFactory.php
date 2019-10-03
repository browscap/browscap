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
     * @throws \Assert\AssertionFailedException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
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

        $browserDirectory = $resourceFolder . '/browsers';

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($browserDirectory)) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || 'json' !== $file->getExtension()) {
                continue;
            }

            $this->logger->debug('add browser file ' . $file->getPathname());
            $this->addBrowserFile($file->getPathname());
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
     * @throws \RuntimeException                if the file does not exist or has invalid JSON
     * @throws \UnexpectedValueException
     * @throws \Assert\AssertionFailedException
     */
    public function addPlatformsFile(string $filename) : void
    {
        $decodedFileContent = $this->loadFile($filename);

        $platformFactory = new PlatformFactory();

        foreach (array_keys($decodedFileContent) as $platformName) {
            $platformName = (string) $platformName;
            $platformData = $decodedFileContent[$platformName];

            $this->collection->addPlatform($platformName, $platformFactory->build($platformData, $decodedFileContent, $platformName));
        }
    }

    /**
     * Load a engines.json file and parse it into the platforms data array, validates these data and creates a
     * collection of Engine objects
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException                if the file does not exist or has invalid JSON
     * @throws \Assert\AssertionFailedException
     */
    public function addEnginesFile(string $filename) : void
    {
        $decodedFileContent = $this->loadFile($filename);

        $engineFactory = new EngineFactory();

        foreach (array_keys($decodedFileContent) as $engineName) {
            $engineName = (string) $engineName;
            $engineData = $decodedFileContent[$engineName];

            $this->collection->addEngine($engineName, $engineFactory->build($engineData, $decodedFileContent, $engineName));
        }
    }

    /**
     * Load a browsers.json file and parse it into the browsers data array, validates these data and creates a
     * collection of Browser objects
     *
     * @param string $filename Name of the file
     *
     * @throws \RuntimeException                if the file does not exist or has invalid JSON
     * @throws \Assert\AssertionFailedException
     */
    public function addBrowserFile(string $filename) : void
    {
        $decodedFileContent = $this->loadFile($filename);

        Assertion::isArray($decodedFileContent, 'required "browsers" structure has to be an array');

        $browserFactory = new BrowserFactory();

        foreach (array_keys($decodedFileContent) as $browserName) {
            $browserData = $decodedFileContent[$browserName];
            $browserName = (string) $browserName;

            $this->collection->addBrowser($browserName, $browserFactory->build($browserData, $browserName));
        }
    }

    /**
     * Load a devices.json file and parse it into the platforms data array, validates these data and creates a
     * collection of Device objects
     *
     * @param string $filename Name of the file
     *
     * @throws \Assert\AssertionFailedException
     * @throws \RuntimeException                if the file does not exist or has invalid JSON
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
     * @throws \Assert\AssertionFailedException
     * @throws \RuntimeException                If the file does not exist or has invalid JSON
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
     * @throws \Assert\AssertionFailedException
     * @throws \RuntimeException                if the file does not exist or has invalid JSON
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
     * @throws \Assert\AssertionFailedException
     * @throws \RuntimeException                if the file does not exist or has invalid JSON
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
     * @throws \Assert\AssertionFailedException
     *
     * @return array
     */
    private function loadFile(string $filename) : array
    {
        Assertion::file($filename, 'File "' . $filename . '" does not exist.');
        Assertion::readable($filename, 'File "' . $filename . '" is not readable.');

        /** @var string $fileContent */
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
                'File "' . $filename . '" had invalid JSON.',
                0,
                $e
            );
        }
    }
}
