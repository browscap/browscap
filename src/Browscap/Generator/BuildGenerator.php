<?php
declare(strict_types = 1);
namespace Browscap\Generator;

use Browscap\Helper\CollectionCreator;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;
use ZipArchive;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <mimmi20@live.de>
 */
class BuildGenerator
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
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Browscap\Helper\CollectionCreator
     */
    private $collectionCreator;

    /**
     * @var \Browscap\Writer\WriterCollection
     */
    private $writerCollection;

    /**
     * @var bool
     */
    private $collectPatternIds = false;

    /**
     * @param string                            $resourceFolder
     * @param string                            $buildFolder
     * @param \Psr\Log\LoggerInterface          $logger
     * @param \Browscap\Writer\WriterCollection $writerCollection
     */
    public function __construct(
        string $resourceFolder,
        string $buildFolder,
        LoggerInterface $logger,
        WriterCollection $writerCollection
    ) {
        $this->resourceFolder    = $this->checkDirectoryExists($resourceFolder);
        $this->buildFolder       = $this->checkDirectoryExists($buildFolder);
        $this->logger            = $logger;
        $this->writerCollection  = $writerCollection;
        $this->collectionCreator = new CollectionCreator($logger);
    }

    /**
     * Entry point for generating builds for a specified version
     *
     * @param string $buildVersion
     * @param bool   $createZipFile
     *
     * @return void
     */
    public function run(string $buildVersion, bool $createZipFile = true) : void
    {
        $this->preBuild();
        $this->build($buildVersion);
        $this->postBuild($createZipFile);
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
     * runs before the build
     */
    private function preBuild() : void
    {
        $this->logger->info('Resource folder: ' . $this->resourceFolder . '');
        $this->logger->info('Build folder: ' . $this->buildFolder . '');
    }

    /**
     * runs the build
     *
     * @param string $buildVersion
     */
    private function build(string $buildVersion) : void
    {
        Helper\BuildHelper::run(
            $buildVersion,
            $this->resourceFolder,
            $this->logger,
            $this->writerCollection,
            $this->collectionCreator,
            $this->collectPatternIds
        );
    }

    /**
     * runs after the build
     *
     * @param bool $createZipFile
     *
     * @return void
     */
    private function postBuild(bool $createZipFile = true) : void
    {
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
     * @param string $directory
     *
     * @throws \Exception
     *
     * @return string
     */
    private function checkDirectoryExists(string $directory) : string
    {
        $realDirectory = realpath($directory);

        if (false === $realDirectory) {
            throw new \Exception('The directory "' . $directory . '" does not exist, or we cannot access it');
        }

        if (!is_dir($realDirectory)) {
            throw new \Exception('The path "' . $realDirectory . '" did not resolve to a directory');
        }

        return $realDirectory;
    }
}
