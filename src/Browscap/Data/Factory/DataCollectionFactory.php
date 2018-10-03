<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Assert\Assertion;
use Browscap\Data\DataCollection;
use Browscap\Data\Validator\DivisionDataValidator;
use ExceptionalJSON\DecodeErrorException;
use JsonClass\Json;
use Psr\Log\LoggerInterface;

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
     *
     * @throws \Exception
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
     * @throws \Assert\AssertionFailedException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return DataCollection
     */
    public function createDataCollection(string $resourceFolder) : DataCollection
    {
        $iterator = static function (string $directory, LoggerInterface $logger, callable $function) : void {
            if (!file_exists($directory)) {
                throw new \RuntimeException('Directory "' . $directory . '" does not exist.');
            }

            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
                /** @var $file \SplFileInfo */
                if (!$file->isFile() || 'json' !== $file->getExtension()) {
                    continue;
                }

                $logger->debug('add file ' . $file->getPathname());
                $function($file->getPathname());
            }
        };

        $this->logger->debug('add platform file');

        $iterator(
            $resourceFolder . '/platforms',
            $this->logger,
            function ($file) : void {
                $this->addPlatformsFile($file);
            }
        );

        $this->logger->debug('add engine file');

        $iterator(
            $resourceFolder . '/engines',
            $this->logger,
            function ($file) : void {
                $this->addEnginesFile($file);
            }
        );

        $this->logger->debug('add file for default properties');
        $this->setDefaultProperties($resourceFolder . '/core/default-properties.json');

        $this->logger->debug('add file for default browser');
        $this->setDefaultBrowser($resourceFolder . '/core/default-browser.json');

        $iterator(
            $resourceFolder . '/devices',
            $this->logger,
            function ($file) : void {
                $this->addDevicesFile($file);
            }
        );

        $iterator(
            $resourceFolder . '/browsers',
            $this->logger,
            function ($file) : void {
                $this->addBrowserFile($file);
            }
        );

        $iterator(
            $resourceFolder . '/user-agents',
            $this->logger,
            function ($file) : void {
                $this->addSourceFile($file);
            }
        );

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
            $platformData = $decodedFileContent[$platformName];

            $this->collection->addPlatform(
                (string) $platformName,
                $platformFactory->build($platformData, $decodedFileContent, (string) $platformName)
            );
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
            $engineData = $decodedFileContent[$engineName];

            $this->collection->addEngine(
                (string) $engineName,
                $engineFactory->build($engineData, $decodedFileContent, (string) $engineName)
            );
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
        if (!file_exists($filename)) {
            throw new \RuntimeException('File "' . $filename . '" does not exist.');
        }

        Assertion::readable($filename, 'File "' . $filename . '" is not readable.');

        $fileContent = file_get_contents($filename);

        Assertion::string($fileContent);

        if (preg_match('/[^ -~\s]/', (string) $fileContent)) {
            throw new \RuntimeException('File "' . $filename . '" contains Non-ASCII-Characters.');
        }

        $json = new Json();

        try {
            return $json->decode((string) $fileContent, true);
        } catch (DecodeErrorException $e) {
            throw new \RuntimeException(
                'File "' . $filename . '" had invalid JSON.',
                0,
                $e
            );
        }
    }
}
