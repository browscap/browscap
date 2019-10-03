<?php
declare(strict_types = 1);
namespace Browscap\Generator;

use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Writer\WriterCollection;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use ZipArchive;

final class BuildGenerator implements GeneratorInterface
{
    /**
     * @var string
     */
    private $resourceFolder;

    /**
     * @var string
     */
    private $buildFolder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DataCollectionFactory
     */
    private $dataCollectionFactory;

    /**
     * @var WriterCollection
     */
    private $writerCollection;

    /**
     * @var bool
     */
    private $collectPatternIds = false;

    /**
     * @param string                $resourceFolder
     * @param string                $buildFolder
     * @param LoggerInterface       $logger
     * @param WriterCollection      $writerCollection
     * @param DataCollectionFactory $dataCollectionFactory
     */
    public function __construct(
        string $resourceFolder,
        string $buildFolder,
        LoggerInterface $logger,
        WriterCollection $writerCollection,
        DataCollectionFactory $dataCollectionFactory
    ) {
        $this->resourceFolder        = $this->checkDirectoryExists($resourceFolder);
        $this->buildFolder           = $this->checkDirectoryExists($buildFolder);
        $this->logger                = $logger;
        $this->writerCollection      = $writerCollection;
        $this->dataCollectionFactory = $dataCollectionFactory;
    }

    /**
     * Entry point for generating builds for a specified version
     *
     * @param string            $buildVersion
     * @param DateTimeImmutable $generationDate
     * @param bool              $createZipFile
     *
     * @throws \Exception
     * @throws \Assert\AssertionFailedException
     */
    public function run(string $buildVersion, DateTimeImmutable $generationDate, bool $createZipFile = true) : void
    {
        $this->logger->info('Resource folder: ' . $this->resourceFolder . '');
        $this->logger->info('Build folder: ' . $this->buildFolder . '');

        Helper\BuildHelper::run(
            $buildVersion,
            $generationDate,
            $this->resourceFolder,
            $this->logger,
            $this->writerCollection,
            $this->dataCollectionFactory,
            $this->collectPatternIds
        );

        if (!$createZipFile) {
            return;
        }

        $this->logger->info('started creating the zip archive');

        $zip = new ZipArchive();
        $zip->open($this->buildFolder . '/browscap.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = [
            'full_asp_browscap.ini',
            'full_php_browscap.ini',
            'browscap.ini',
            'php_browscap.ini',
            'lite_asp_browscap.ini',
            'lite_php_browscap.ini',
            'browscap.xml',
            'browscap.csv',
            'browscap.json',
        ];

        foreach ($files as $file) {
            $filePath = $this->buildFolder . '/' . $file;

            if (!file_exists($filePath) || !is_readable($filePath)) {
                continue;
            }

            $zip->addFile($filePath, $file);
        }

        $zip->close();

        $this->logger->info('finished creating the zip archive');
    }

    /**
     * Sets the flag to collect pattern ids during this build
     *
     * @param bool $value
     */
    public function setCollectPatternIds(bool $value) : void
    {
        $this->collectPatternIds = $value;
    }

    /**
     * @param string $directory
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    private function checkDirectoryExists(string $directory) : string
    {
        $realDirectory = realpath($directory);

        if (false === $realDirectory) {
            throw new DirectoryMissingException('The directory "' . $directory . '" does not exist, or we cannot access it');
        }

        if (!is_dir($realDirectory)) {
            throw new NotADirectoryException('The path "' . $realDirectory . '" did not resolve to a directory');
        }

        return $realDirectory;
    }
}
