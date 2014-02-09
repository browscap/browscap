<?php

namespace Browscap\Generator;

use Browscap\Helper\CollectionCreator;
use Browscap\Helper\Generator;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use ZipArchive;

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
    private $logger = null;

    /**
     * @param string $resourceFolder
     * @param string $buildFolder
     */
    public function __construct($resourceFolder, $buildFolder)
    {
        $this->resourceFolder = $this->checkDirectoryExists($resourceFolder, 'resource');
        $this->buildFolder = $this->checkDirectoryExists($buildFolder, 'build');
    }

    /**
     * @param string $directory
     * @param string $type
     *
     * @return string
     * @throws \Exception
     */
    private function checkDirectoryExists($directory, $type)
    {
        if (!isset($directory)) {
            throw new \Exception("You must specify a {$type} folder");
        }

        $realDirectory = realpath($directory);

        if ($realDirectory === false) {
            throw new \Exception("The directory '{$directory}' does not exist, or we cannot access it");
        }

        if (!is_dir($realDirectory)) {
            throw new \Exception("The path '{$realDirectory}' did not resolve to a directory");
        }

        return $realDirectory;
    }

    /**
     * Entry point for generating builds for a specified version
     *
     * @param string $version
     */
    public function generateBuilds($version)
    {
        $this->logger->log(Logger::INFO, 'Resource folder: ' . $this->resourceFolder . '');
        $this->logger->log(Logger::INFO, 'Build folder: ' . $this->buildFolder . '');

        $this->writeFiles($version, $this->buildFolder);
    }

    /**
     * Write out the various INI file formats, the XML file format, the CSV file format and packs all files to a
     * zip archive
     *
     * @param string $version
     * @param string $buildFolder
     */
    private function writeFiles($version, $buildFolder)
    {
        $collectionCreator = new CollectionCreator();
        $collectionParser = new CollectionParser();

        $generatorHelper = new Generator();
        $generatorHelper
            ->setVersion($version)
            ->setResourceFolder($this->resourceFolder)
            ->setCollectionCreator($collectionCreator)
            ->setCollectionParser($collectionParser)
            ->createCollection()
            ->parseCollection()
        ;

        $formats = array(
            ['full_asp_browscap.ini', 'ASP/FULL', false, true, false],
            ['full_php_browscap.ini', 'PHP/FULL', true, true, false],
            ['browscap.ini', 'ASP', false, false, false],
            ['php_browscap.ini', 'PHP', true, false, false],
            ['lite_asp_browscap.ini', 'ASP/LITE', false, false, true],
            ['lite_php_browscap.ini', 'PHP/LITE', true, false, true],
        );

        $iniGenerator = new BrowscapIniGenerator();

        foreach ($formats as $format) {
            $this->logger->log(Logger::INFO, 'Generating ' . $format[0] . ' [' . $format[1] . ']');

            $iniGenerator->setOptions($format[2], $format[3], $format[4]);

            $generatorHelper->setGenerator($iniGenerator);

            file_put_contents($buildFolder . '/' . $format[0], $generatorHelper->create());
        }

        $this->logger->log(Logger::INFO, 'Generating browscap.xml [XML]');

        $xmlGenerator = new BrowscapXmlGenerator();
        $generatorHelper->setGenerator($xmlGenerator);
        file_put_contents($buildFolder . '/browscap.xml', $generatorHelper->create());

        $this->logger->log(Logger::INFO, 'Generating browscap.csv [CSV]');

        $csvGenerator = new BrowscapCsvGenerator();
        $generatorHelper->setGenerator($csvGenerator);
        file_put_contents($buildFolder . '/browscap.csv', $generatorHelper->create());

        $this->logger->log(Logger::INFO, 'Generating browscap.zip [ZIP]');

        $zip = new ZipArchive();
        $zip->open($buildFolder . '/browscap.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFile($buildFolder . '/full_asp_browscap.ini', 'full_asp_browscap.ini');
        $zip->addFile($buildFolder . '/full_php_browscap.ini', 'full_php_browscap.ini');
        $zip->addFile($buildFolder . '/browscap.ini', 'browscap.ini');
        $zip->addFile($buildFolder . '/php_browscap.ini', 'php_browscap.ini');
        $zip->addFile($buildFolder . '/lite_asp_browscap.ini', 'lite_asp_browscap.ini');
        $zip->addFile($buildFolder . '/lite_php_browscap.ini', 'lite_php_browscap.ini');
        $zip->addFile($buildFolder . '/browscap.xml', 'browscap.xml');
        $zip->addFile($buildFolder . '/browscap.csv', 'browscap.csv');

        $zip->close();
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
