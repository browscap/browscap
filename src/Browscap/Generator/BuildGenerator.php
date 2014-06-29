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
     * @param string $resourceFolder
     * @param string $buildFolder
     */
    public function __construct($resourceFolder, $buildFolder)
    {
        $this->resourceFolder = $this->checkDirectoryExists($resourceFolder, 'resource');
        $this->buildFolder    = $this->checkDirectoryExists($buildFolder, 'build');
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
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
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
        $this->getLogger()->info('Resource folder: ' . $this->resourceFolder . '');
        $this->getLogger()->info('Build folder: ' . $this->buildFolder . '');

        $fullAspWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/full_asp_browscap.ini');
        $fullAspWriter->addFormatter(new \Browscap\Formatter\AspFormatter());
        $fullAspWriter->addFilter(new \Browscap\Filter\FullFilter());

        $fullPhpWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/full_php_browscap.ini');
        $fullPhpWriter->addFormatter(new \Browscap\Formatter\PhpFormatter());
        $fullPhpWriter->addFilter(new \Browscap\Filter\FullFilter());

        $stdAspWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/browscap.ini');
        $stdAspWriter->addFormatter(new \Browscap\Formatter\AspFormatter());
        $stdAspWriter->addFilter(new \Browscap\Filter\StandartFilter());

        $stdPhpWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/php_browscap.ini');
        $stdPhpWriter->addFormatter(new \Browscap\Formatter\PhpFormatter());
        $stdPhpWriter->addFilter(new \Browscap\Filter\StandartFilter());

        $liteAspWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/lite_asp_browscap.ini');
        $liteAspWriter->addFormatter(new \Browscap\Formatter\AspFormatter());
        $liteAspWriter->addFilter(new \Browscap\Filter\LiteFilter());

        $litePhpWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/lite_php_browscap.ini');
        $litePhpWriter->addFormatter(new \Browscap\Formatter\PhpFormatter());
        $litePhpWriter->addFilter(new \Browscap\Filter\LiteFilter());

        $csvWriter = new \Browscap\Writer\CsvWriter($this->buildFolder . '/browscap.csv');
        $csvWriter->addFormatter(new \Browscap\Formatter\CsvFormatter());
        $csvWriter->addFilter(new \Browscap\Filter\StandartFilter());

        $xmlWriter = new \Browscap\Writer\XmlWriter($this->buildFolder . '/browscap.xml');
        $xmlWriter->addFormatter(new \Browscap\Formatter\XmlFormatter());
        $xmlWriter->addFilter(new \Browscap\Filter\StandartFilter());

        $writers = array(
            $fullAspWriter,
            $fullPhpWriter,
            $stdAspWriter,
            $stdPhpWriter,
            $liteAspWriter,
            $litePhpWriter,
            $csvWriter,
            $xmlWriter,
        );

        foreach ($writers as $writer) {
            /** @var \Browscap\Writer\WriterInterface $writer */
            $writer
                ->renderHeader()
                ->renderVersion()
            ;
        }

        $this->getLogger()->info('started creating a data collection');

        $collection = new DataCollection($version);

        $this->collectionCreator
            ->setLogger($this->getLogger())
            ->setDataCollection($collection)
            ->createDataCollection($this->getResourceFolder())
        ;

        $this->getLogger()->info('finished creating a data collection');

        foreach ($collection->getDivisions() as $division) {
            foreach ($writers as $writer) {
                /** @var \Browscap\Writer\WriterInterface $writer */
                $writer
                    ->renderDivisionHeader($division)
                    ->renderDivisionBody($division)
                ;
            }
        }

        foreach ($writers as $writer) {
            /** @var \Browscap\Writer\WriterInterface $writer */
            unset($writer);
        }

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

        $zip->close();
    }
}
