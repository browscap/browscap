<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   Browscap
 * @package    Generator
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Generator;

use Browscap\Data\DataCollection;
use Browscap\Data\Expander;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;
use ZipArchive;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 * @package    Generator
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
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

    /** @var \Browscap\Writer\WriterCollection */
    private $writerCollection = null;

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
    public function setCollectionCreator(CollectionCreator $collectionCreator)
    {
        $this->collectionCreator = $collectionCreator;

        return $this;
    }

    /**
     * @param \Browscap\Writer\WriterCollection $writerCollection
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    public function setWriterCollection(WriterCollection $writerCollection)
    {
        $this->writerCollection = $writerCollection;

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
     * @param string  $version
     * @param boolean $createZipFile
     */
    public function run($version, $createZipFile = true)
    {
        $this->getLogger()->info('Resource folder: ' . $this->resourceFolder . '');
        $this->getLogger()->info('Build folder: ' . $this->buildFolder . '');

        $this->getLogger()->info('started creating a data collection');

        $dataCollection = new DataCollection($version);
        $dataCollection->setLogger($this->getLogger());

        $this->collectionCreator
            ->setLogger($this->getLogger())
            ->setDataCollection($dataCollection)
        ;

        $collection = $this->collectionCreator->createDataCollection($this->resourceFolder);

        $this->getLogger()->info('finished creating a data collection');

        $this->getLogger()->info('started initialisation of writers');

        $expander = new Expander();
        $expander
            ->setDataCollection($collection)
            ->setLogger($this->getLogger())
        ;

        $this->getLogger()->info('finished initialisation of writers');

        $this->getLogger()->info('started output of header and version');

        $comments = array(
            'Provided courtesy of http://browscap.org/',
            'Created on ' . $collection->getGenerationDate()->format('l, F j, Y \a\t h:i A T'),
            'Keep up with the latest goings-on with the project:',
            'Follow us on Twitter <https://twitter.com/browscap>, or...',
            'Like us on Facebook <https://facebook.com/browscap>, or...',
            'Collaborate on GitHub <https://github.com/browscap>, or...',
            'Discuss on Google Groups <https://groups.google.com/forum/#!forum/browscap>.'
        );

        $this->writerCollection
            ->fileStart()
            ->renderHeader($comments)
            ->renderVersion($version, $collection)
        ;

        $this->getLogger()->info('finished output of header and version');

        $output = array();

        $this->getLogger()->info('started output of divisions');

        $division = $collection->getDefaultProperties();

        $this->getLogger()->info('handle division ' . $division->getName());

        $this->writerCollection
            ->renderAllDivisionsHeader($collection)
            ->renderDivisionHeader($division->getName())
        ;

        $ua       = $division->getUserAgents();
        $sections = array($ua[0]['userAgent'] => $ua[0]['properties']);

        foreach ($sections as $sectionName => $section) {
            $this->writerCollection
                ->renderSectionHeader($sectionName)
                ->renderSectionBody($section, $collection, $sections, $sectionName)
                ->renderSectionFooter($sectionName)
            ;
        }

        $this->writerCollection->renderDivisionFooter();

        foreach ($collection->getDivisions() as $division) {
            /** @var \Browscap\Data\Division $division */
            $this->writerCollection->setSilent($division);

            $versions = $division->getVersions();

            foreach ($versions as $version) {
                list($majorVer, $minorVer) = $expander->getVersionParts($version);

                $divisionName = $expander->parseProperty($division->getName(), $majorVer, $minorVer);

                $this->getLogger()->info('handle division ' . $divisionName);

                $sections     = $expander->expand($division, $majorVer, $minorVer, $divisionName);
                $firstElement = current($sections);

                $this->writerCollection->renderDivisionHeader($divisionName, $firstElement['Parent']);

                foreach ($sections as $sectionName => $section) {
                    if (in_array($sectionName, $output)) {
                        throw new \UnexpectedValueException(
                            'tried to add section "' . $sectionName . '" more than once'
                        );
                    }

                    $collection->checkProperty($sectionName, $section);

                    $this->writerCollection
                        ->renderSectionHeader($sectionName)
                        ->renderSectionBody($section, $collection, $sections, $sectionName)
                        ->renderSectionFooter($sectionName)
                    ;

                    $output[] = $sectionName;
                }

                $this->writerCollection->renderDivisionFooter();

                unset($divisionName, $majorVer, $minorVer);
            }
        }

        $division = $collection->getDefaultBrowser();

        $this->getLogger()->info('handle division ' . $division->getName());

        $this->writerCollection->renderDivisionHeader($division->getName());

        $ua       = $division->getUserAgents();
        $sections = array($ua[0]['userAgent'] => $ua[0]['properties']);

        foreach ($sections as $sectionName => $section) {
            $this->writerCollection
                ->renderSectionHeader($sectionName)
                ->renderSectionBody($section, $collection, $sections, $sectionName)
                ->renderSectionFooter($sectionName)
            ;
        }

        $this->writerCollection
            ->renderDivisionFooter()
            ->renderAllDivisionsFooter()
        ;

        $this->getLogger()->info('finished output of divisions');

        $this->getLogger()->info('started closing writers');

        $this->writerCollection
            ->fileEnd()
            ->close()
        ;

        $this->getLogger()->info('finished closing writers');

        if (!$createZipFile) {
            return;
        }

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
        $zip->addFile($this->buildFolder . '/browscap.json', 'browscap.json');

        $zip->close();

        $this->getLogger()->info('finished creating the zip archive');
    }
}
