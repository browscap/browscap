<?php

declare(strict_types=1);

namespace Browscap\Data\Factory;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Browscap\Data\Browser;
use Browscap\Data\DataCollection;
use Browscap\Data\Device;
use Browscap\Data\Division;
use Browscap\Data\DuplicateDataException;
use Browscap\Data\Engine;
use Browscap\Data\Platform;
use Browscap\Data\Validator\DivisionDataValidator;
use JsonException;
use LogicException;
use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use UnexpectedValueException;

use function array_keys;
use function assert;
use function file_exists;
use function file_get_contents;
use function is_array;
use function is_string;
use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * @phpstan-import-type DivisionData from Division
 * @phpstan-import-type DeviceData from Device
 * @phpstan-import-type BrowserData from Browser
 * @phpstan-import-type EngineData from Engine
 * @phpstan-import-type PlatformData from Platform
 */
class DataCollectionFactory
{
    private DataCollection $collection;

    private DivisionFactory $divisionFactory;

    private DeviceFactory $deviceFactory;

    private DivisionDataValidator $divisionDataValidator;

    /** @var array<string> */
    private array $allDivisions = [];

    /** @throws void */
    public function __construct(private LoggerInterface $logger)
    {
        $useragentFactory            = new UserAgentFactory();
        $this->divisionFactory       = new DivisionFactory($useragentFactory);
        $this->deviceFactory         = new DeviceFactory();
        $this->divisionDataValidator = new DivisionDataValidator();
        $this->collection            = new DataCollection();
    }

    /**
     * Create and populate a data collection object from a resource folder
     *
     * @throws AssertionFailedException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     * @throws LogicException
     */
    public function createDataCollection(string $resourceFolder): DataCollection
    {
        $iterator = static function (string $directory, LoggerInterface $logger, callable $function): void {
            if (! file_exists($directory)) {
                throw new RuntimeException('Directory "' . $directory . '" does not exist.');
            }

            $addedFiles = 0;

            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
                assert($file instanceof SplFileInfo);
                if (! $file->isFile() || $file->getExtension() !== 'json') {
                    continue;
                }

                $logger->debug('add file ' . $file->getPathname());
                $function($file->getPathname());
                ++$addedFiles;
            }

            if (! $addedFiles) {
                throw new RuntimeException('Directory "' . $directory . '" was empty.');
            }
        };

        $this->logger->debug('add platform file');

        $iterator(
            $resourceFolder . '/platforms',
            $this->logger,
            function (string $file): void {
                $this->addPlatformsFile($file);
            },
        );

        $this->logger->debug('add engine file');

        $iterator(
            $resourceFolder . '/engines',
            $this->logger,
            function (string $file): void {
                $this->addEnginesFile($file);
            },
        );

        $this->logger->debug('add file for default properties');
        $this->setDefaultProperties($resourceFolder . '/core/default-properties.json');

        $this->logger->debug('add file for default browser');
        $this->setDefaultBrowser($resourceFolder . '/core/default-browser.json');

        $iterator(
            $resourceFolder . '/devices',
            $this->logger,
            function (string $file): void {
                $this->addDevicesFile($file);
            },
        );

        $iterator(
            $resourceFolder . '/browsers',
            $this->logger,
            function (string $file): void {
                $this->addBrowserFile($file);
            },
        );

        $iterator(
            $resourceFolder . '/user-agents',
            $this->logger,
            function (string $file): void {
                $this->addSourceFile($file);
            },
        );

        return $this->collection;
    }

    /**
     * Load a platforms.json file and parse it into the platforms data array, validates these data and creates a
     * collection of Platform objects
     *
     * @param string $filename Name of the file
     *
     * @throws RuntimeException if the file does not exist or has invalid JSON.
     * @throws UnexpectedValueException
     * @throws AssertionFailedException
     */
    public function addPlatformsFile(string $filename): void
    {
        /** @var mixed[][] $decodedFileContent */
        /** @phpstan-var array<PlatformData> $decodedFileContent */
        $decodedFileContent = $this->loadFile($filename);

        $platformFactory = new PlatformFactory();

        foreach (array_keys($decodedFileContent) as $platformName) {
            $platformName = (string) $platformName;

            /** @phpstan-var PlatformData $platformData */
            $platformData = $decodedFileContent[$platformName];

            $this->collection->addPlatform(
                $platformName,
                $platformFactory->build($platformData, $decodedFileContent, $platformName),
            );
        }
    }

    /**
     * Load a engines.json file and parse it into the platforms data array, validates these data and creates a
     * collection of Engine objects
     *
     * @param string $filename Name of the file
     *
     * @throws RuntimeException if the file does not exist or has invalid JSON.
     * @throws AssertionFailedException
     */
    public function addEnginesFile(string $filename): void
    {
        /** @var mixed[][] $decodedFileContent */
        /** @phpstan-var array<EngineData> $decodedFileContent */
        $decodedFileContent = $this->loadFile($filename);

        $engineFactory = new EngineFactory();

        foreach (array_keys($decodedFileContent) as $engineName) {
            $engineName = (string) $engineName;
            /** @phpstan-var EngineData $engineData */
            $engineData = $decodedFileContent[$engineName];

            $this->collection->addEngine(
                $engineName,
                $engineFactory->build($engineData, $decodedFileContent, $engineName),
            );
        }
    }

    /**
     * Load a browsers.json file and parse it into the browsers data array, validates these data and creates a
     * collection of Browser objects
     *
     * @param string $filename Name of the file
     *
     * @throws RuntimeException if the file does not exist or has invalid JSON.
     * @throws AssertionFailedException
     * @throws DuplicateDataException
     */
    public function addBrowserFile(string $filename): void
    {
        /** @var mixed[][] $decodedFileContent */
        /** @phpstan-var array<BrowserData> $decodedFileContent */
        $decodedFileContent = $this->loadFile($filename);

        Assertion::isArray($decodedFileContent, 'required "browsers" structure has to be an array');

        $browserFactory = new BrowserFactory();

        foreach (array_keys($decodedFileContent) as $browserName) {
            /** @phpstan-var BrowserData $browserData */
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
     * @throws AssertionFailedException
     * @throws RuntimeException if the file does not exist or has invalid JSON.
     * @throws DuplicateDataException
     */
    public function addDevicesFile(string $filename): void
    {
        /** @var mixed[][] $decodedFileContent */
        /** @phpstan-var array<DeviceData> $decodedFileContent */
        $decodedFileContent = $this->loadFile($filename);

        foreach ($decodedFileContent as $deviceName => $deviceData) {
            /** @phpstan-var DeviceData $deviceData */
            assert(is_array($deviceData));

            $this->collection->addDevice($deviceName, $this->deviceFactory->build($deviceData, $deviceName));
        }
    }

    /**
     * Load a JSON file, parse it's JSON to arrays, validates these data and creates a
     * collection of Division objects
     *
     * @param string $filename Name of the file
     *
     * @throws AssertionFailedException
     * @throws RuntimeException If the file does not exist or has invalid JSON.
     * @throws LogicException
     */
    public function addSourceFile(string $filename): void
    {
        /** @phpstan-var DivisionData $divisionData */
        $divisionData = $this->loadFile($filename);

        $this->allDivisions = $this->divisionDataValidator->validate($divisionData, $filename, $this->allDivisions, false);

        $this->collection->addDivision(
            $this->divisionFactory->build(
                $divisionData,
                $filename,
                false,
            ),
        );
    }

    /**
     * Load the file for the default properties
     *
     * @param string $filename Name of the file
     *
     * @throws AssertionFailedException
     * @throws RuntimeException if the file does not exist or has invalid JSON.
     * @throws LogicException
     */
    public function setDefaultProperties(string $filename): void
    {
        /** @phpstan-var DivisionData $divisionData */
        $divisionData = $this->loadFile($filename);

        $this->divisionDataValidator->validate($divisionData, $filename, $this->allDivisions, true);

        $this->collection->setDefaultProperties(
            $this->divisionFactory->build(
                $divisionData,
                $filename,
                true,
            ),
        );
    }

    /**
     * Load the file for the default browser
     *
     * @param string $filename Name of the file
     *
     * @throws AssertionFailedException
     * @throws RuntimeException if the file does not exist or has invalid JSON.
     * @throws LogicException
     */
    public function setDefaultBrowser(string $filename): void
    {
        /** @phpstan-var DivisionData $divisionData */
        $divisionData = $this->loadFile($filename);

        $this->divisionDataValidator->validate($divisionData, $filename, $this->allDivisions, true);

        $this->collection->setDefaultBrowser(
            $this->divisionFactory->build(
                $divisionData,
                $filename,
                true,
            ),
        );
    }

    /**
     * @return mixed[][]
     *
     * @throws RuntimeException
     * @throws AssertionFailedException
     */
    private function loadFile(string $filename): array
    {
        Assertion::file($filename, 'File "' . $filename . '" does not exist.');
        Assertion::readable($filename, 'File "' . $filename . '" is not readable.');

        $fileContent = file_get_contents($filename);
        assert(is_string($fileContent));

        Assertion::string($fileContent);
        Assertion::notRegex($fileContent, '/[^ -~\s]/', 'File "' . $filename . '" contains Non-ASCII-Characters.');

        try {
            $parsedContent = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException(
                'File "' . $filename . '" had invalid JSON.',
                0,
                $e,
            );
        }

        assert(is_array($parsedContent));

        return $parsedContent;
    }
}
