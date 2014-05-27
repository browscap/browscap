<?php

namespace Browscap\Generator;

use Psr\Log\LoggerInterface;
use ZipArchive;

/**
 * Class BuildGenerator
 *
 * @package Browscap\Generator
 */
class BuildGenerator
{
    /**@+
     * @var string
     */
    const OUTPUT_FORMAT_PHP = 'php';
    const OUTPUT_FORMAT_ASP = 'asp';
    /**@-*/

    /**@+
     * @var string
     */
    const OUTPUT_TYPE_FULL    = 'full';
    const OUTPUT_TYPE_DEFAULT = 'normal';
    const OUTPUT_TYPE_LITE    = 'lite';
    /**@-*/

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
     * @var \Browscap\Helper\CollectionCreator
     */
    private $collectionCreator = null;

    /**
     * @var \Browscap\Generator\CollectionParser
     */
    private $collectionParser = null;

    /**
     * @var \Browscap\Helper\Generator
     */
    private $generatorHelper = null;

    /**
     * @param string $resourceFolder
     * @param string $buildFolder
     */
    public function __construct($resourceFolder, $buildFolder)
    {
        $this->resourceFolder = $this->checkDirectoryExists($resourceFolder, 'resource');
        $this->buildFolder    = $this->checkDirectoryExists($buildFolder, 'build');
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
            throw new \Exception('You must specify a ' . $type . ' folder');
        }

        $realDirectory = realpath($directory);

        if ($realDirectory === false) {
            throw new \Exception('The directory "' . $directory . '" does not exist, or we cannot access it');
        }

        if (!is_dir($realDirectory)) {
            throw new \Exception('The path "' . $realDirectory . '" did not resolve to a directory');
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
        $this->logger->info('Resource folder: ' . $this->resourceFolder . '');
        $this->logger->info('Build folder: ' . $this->buildFolder . '');

        $this->writeFiles($version);
    }

    /**
     * Write out the various INI file formats, the XML file format, the CSV file format and packs all files to a
     * zip archive
     *
     * @param string $version
     */
    private function writeFiles($version)
    {

        $this->generatorHelper
            ->setLogger($this->logger)
            ->setVersion($version)
            ->setResourceFolder($this->resourceFolder)
            ->setCollectionCreator($this->collectionCreator)
            ->setCollectionParser($this->collectionParser)
            ->createCollection()
            ->parseCollection()
        ;


        $iniGenerator = new BrowscapIniGenerator();
        $this->generatorHelper->setGenerator($iniGenerator);

        $formats = [
            [
                'file' => 'full_asp_browscap.ini',
                'info' => 'ASP/FULL',
                'format' => self::OUTPUT_FORMAT_ASP,
                'type' => self::OUTPUT_TYPE_FULL
            ],
            [
                'file' => 'full_php_browscap.ini',
                'info' => 'PHP/FULL',
                'format' => self::OUTPUT_FORMAT_PHP,
                'type' => self::OUTPUT_TYPE_FULL
            ],
            [
                'file' => 'browscap.ini',
                'info' => 'ASP',
                'format' => self::OUTPUT_FORMAT_ASP,
                'type' => self::OUTPUT_TYPE_DEFAULT
            ],
            [
                'file' => 'php_browscap.ini',
                'info' => 'PHP',
                'format' => self::OUTPUT_FORMAT_PHP,
                'type' => self::OUTPUT_TYPE_DEFAULT
            ],
            [
                'file' => 'lite_asp_browscap.ini',
                'info' => 'ASP/LITE',
                'format' => self::OUTPUT_FORMAT_ASP,
                'type' => self::OUTPUT_TYPE_LITE
            ],
            [
                'file' => 'lite_php_browscap.ini',
                'info' => 'PHP/LITE',
                'format' => self::OUTPUT_FORMAT_PHP,
                'type' => self::OUTPUT_TYPE_LITE
            ],
        ];

        foreach ($formats as $format) {
            $this->logger->info('Generating ' . $format['file'] . ' [' . $format['info'] . ']');

            file_put_contents(
                $this->buildFolder . '/' . $format['file'],
                $this->generatorHelper->create($format['format'], $format['type'])
            );
        }

        unset($iniGenerator);

        $this->logger->info('Generating browscap.xml [XML]');

        $xmlGenerator = new BrowscapXmlGenerator($this->buildFolder . '/browscap.xml');
        $this->generatorHelper->setGenerator($xmlGenerator);
        $this->generatorHelper->create();

        unset($xmlGenerator);

        $this->logger->info('Generating browscap.csv [CSV]');

        $csvGenerator = new BrowscapCsvGenerator();
        $this->generatorHelper->setGenerator($csvGenerator);
        file_put_contents($this->buildFolder . '/browscap.csv', $this->generatorHelper->create());

        unset($csvGenerator);

        $this->logger->info('Generating browscap.json [JSON]');

        $jsonGenerator = new BrowscapJsonGenerator();
        $this->generatorHelper->setGenerator($jsonGenerator);
        file_put_contents($this->buildFolder . '/browscap.json', $this->generatorHelper->create());

        unset($jsonGenerator);

        $this->logger->info('Generating browscap.preprocessed.json [preprocessed JSON]');

        $jsonGenerator = new BrowscapProcessedJsonGenerator();
        $this->generatorHelper->setGenerator($jsonGenerator);
        file_put_contents($this->buildFolder . '/browscap.preprocessed.json', $this->generatorHelper->create());

        unset($jsonGenerator);

        $this->logger->info('Generating browscap.zip [ZIP]');

        $zip = new ZipArchive();
        $zip->open($this->buildFolder . '/browscap.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFile($this->buildFolder . '/full_asp_browscap.ini', 'full_asp_browscap.ini');
        $zip->addFile($this->buildFolder . '/full_php_browscap.ini', 'full_php_browscap.ini');
        $zip->addFile($this->buildFolder . '/browscap.ini', 'browscap.ini');
        $zip->addFile($this->buildFolder . '/php_browscap.ini', 'php_browscap.ini');
        $zip->addFile($this->buildFolder . '/lite_asp_browscap.ini', 'lite_asp_browscap.ini');
        $zip->addFile($this->buildFolder . '/lite_php_browscap.ini', 'lite_php_browscap.ini');
        $zip->addFile($this->buildFolder . '/browscap.xml', 'browscap.xml');
        $zip->addFile($this->buildFolder . '/browscap.csv', 'browscap.csv');
        $zip->addFile($this->buildFolder . '/browscap.json', 'browscap.json');

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

    /**
     * @param \Browscap\Helper\CollectionCreator $collectionCreator
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    public function setCollectionCreator($collectionCreator)
    {
        $this->collectionCreator = $collectionCreator;

        return $this;
    }

    /**
     * @param \Browscap\Generator\CollectionParser $collectionParser
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    public function setCollectionParser($collectionParser)
    {
        $this->collectionParser = $collectionParser;

        return $this;
    }

    /**
     * @param \Browscap\Helper\Generator $generatorHelper
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    public function setGeneratorHelper($generatorHelper)
    {
        $this->generatorHelper = $generatorHelper;

        return $this;
    }
}
