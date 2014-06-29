<?php

namespace Browscap\Generator;

use Browscap\Data\DataCollection;
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

        $this->getLogger()->info('started creating a data collection');

        $collection = new DataCollection($version);

        $this->collectionCreator
            ->setLogger($this->getLogger())
            ->setDataCollection($collection)
            ->createDataCollection($this->resourceFolder)
        ;

        $this->getLogger()->info('finished creating a data collection');

        $this->getLogger()->info('started initialisation of writers');

        $aspFormatter = new \Browscap\Formatter\AspFormatter();
        $phpFormatter = new \Browscap\Formatter\PhpFormatter();
        $csvFormatter = new \Browscap\Formatter\CsvFormatter();
        $xmlFormatter = new \Browscap\Formatter\XmlFormatter();

        $fullFilter = new \Browscap\Filter\FullFilter();
        $stdFilter  = new \Browscap\Filter\StandartFilter();
        $liteFilter = new \Browscap\Filter\LiteFilter();

        $fullAspWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/full_asp_browscap.ini');
        $fullAspWriter
            ->setLogger($this->getLogger())
            ->setFormatter($aspFormatter)
            ->setFilter($fullFilter)
        ;

        $fullPhpWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/full_php_browscap.ini');
        $fullPhpWriter
            ->setLogger($this->getLogger())
            ->setFormatter($phpFormatter)
            ->setFilter($fullFilter)
        ;

        $stdAspWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/browscap.ini');
        $stdAspWriter
            ->setLogger($this->getLogger())
            ->setFormatter($aspFormatter)
            ->setFilter($stdFilter)
        ;

        $stdPhpWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/php_browscap.ini');
        $stdPhpWriter
            ->setLogger($this->getLogger())
            ->setFormatter($phpFormatter)
            ->setFilter($stdFilter)
        ;

        $liteAspWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/lite_asp_browscap.ini');
        $liteAspWriter
            ->setLogger($this->getLogger())
            ->setFormatter($aspFormatter)
            ->setFilter($liteFilter)
        ;

        $litePhpWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/lite_php_browscap.ini');
        $litePhpWriter
            ->setLogger($this->getLogger())
            ->setFormatter($phpFormatter)
            ->setFilter($liteFilter)
        ;

        $csvWriter = new \Browscap\Writer\CsvWriter($this->buildFolder . '/browscap.csv');
        $csvWriter
            ->setLogger($this->getLogger())
            ->setFormatter($csvFormatter)
            ->setFilter($stdFilter)
        ;

        $xmlWriter = new \Browscap\Writer\XmlWriter($this->buildFolder . '/browscap.xml');
        $xmlWriter
            ->setLogger($this->getLogger())
            ->setFormatter($xmlFormatter)
            ->setFilter($stdFilter)
        ;

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

        $this->getLogger()->info('finished initialisation of writers');

        $this->getLogger()->info('started output of header and version');

        $comments = array(
            'Provided courtesy of http://browscap.org/',
            'Created on ' . $this->collection->getGenerationDate()->format('l, F j, Y \a\t h:i A T'),
            'Keep up with the latest goings-on with the project:',
            'Follow us on Twitter <https://twitter.com/browscap>, or...',
            'Like us on Facebook <https://facebook.com/browscap>, or...',
            'Collaborate on GitHub <https://github.com/browscap>, or...',
            'Discuss on Google Groups <https://groups.google.com/forum/#!forum/browscap>.'
        );

        $versionData = array(
            'version' => $version,
            'released' => $this->collection->getGenerationDate()->format('r')
        );

        foreach ($writers as $writer) {
            /** @var \Browscap\Writer\WriterInterface $writer */
            $writer
                ->renderHeader($comments)
                ->renderVersion(
                    array(
                        'version'  => $version,
                        'released' => $this->collection->getGenerationDate()->format('r'),
                        'format'   => $writer->getFormatter()->getType(),
                        'type'     => $writer->getFilter()->getType(),
                    )
                )
            ;
        }

        $this->getLogger()->info('finished output of header and version');

        $this->getLogger()->info('started output of divisions');

        foreach ($collection->getDivisions() as $division) {
            foreach ($writers as $writer) {
                /** @var \Browscap\Writer\WriterInterface $writer */
                $writer
                    ->renderDivisionHeader($division)
                    ->renderDivisionBody($division)
                ;
            }
        }

        $this->getLogger()->info('finished output of divisions');

        $this->getLogger()->info('started closing writers');

        foreach ($writers as $writer) {
            /** @var \Browscap\Writer\WriterInterface $writer */
            $writer->close();
        }

        $this->getLogger()->info('finished closing writers');

        $this->getLogger()->info('started creating the zip archive');

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

        $this->getLogger()->info('finished creating the zip archive');
    }
}
