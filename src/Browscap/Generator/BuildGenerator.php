<?php

declare(strict_types=1);

namespace Browscap\Generator;

use Assert\AssertionFailedException;
use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Writer\WriterCollection;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ZipArchive;

use function file_exists;
use function is_dir;
use function is_readable;
use function realpath;

final class BuildGenerator implements GeneratorInterface
{
    private string $resourceFolder;

    private string $buildFolder;

    private bool $collectPatternIds = false;

    /** @throws InvalidArgumentException */
    public function __construct(
        string $resourceFolder,
        string $buildFolder,
        private LoggerInterface $logger,
        private WriterCollection $writerCollection,
        private DataCollectionFactory $dataCollectionFactory,
    ) {
        $this->resourceFolder = $this->checkDirectoryExists($resourceFolder);
        $this->buildFolder    = $this->checkDirectoryExists($buildFolder);
    }

    /**
     * Entry point for generating builds for a specified version
     *
     * @throws Exception
     * @throws AssertionFailedException
     */
    public function run(string $buildVersion, DateTimeImmutable $generationDate, bool $createZipFile = true): void
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
            $this->collectPatternIds,
        );

        if (! $createZipFile) {
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

            if (! file_exists($filePath) || ! is_readable($filePath)) {
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
     * @throws void
     */
    public function setCollectPatternIds(bool $value): void
    {
        $this->collectPatternIds = $value;
    }

    /** @throws InvalidArgumentException */
    private function checkDirectoryExists(string $directory): string
    {
        $realDirectory = realpath($directory);

        if ($realDirectory === false) {
            throw new DirectoryMissingException('The directory "' . $directory . '" does not exist, or we cannot access it');
        }

        if (! is_dir($realDirectory)) {
            throw new NotADirectoryException('The path "' . $realDirectory . '" did not resolve to a directory');
        }

        return $realDirectory;
    }
}
