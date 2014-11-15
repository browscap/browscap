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
use Browscap\Filter\FullFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 * @package    Generator
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class BuildFullFileOnlyGenerator
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
        $this->buildFolder    = $this->checkDirectoryExists($buildFolder, 'build');
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return BuildFullFileOnlyGenerator
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
     * @param string $iniFile
     */
    public function run($version, $iniFile = null)
    {
        $this->getLogger()->info('Resource folder: ' . $this->resourceFolder . '');
        $this->getLogger()->info('Build folder: ' . $this->buildFolder . '');

        $this->getLogger()->info('full ini file for php');

        $collectionCreator = new CollectionCreator();

        if (null === $iniFile) {
            $iniFile = $this->buildFolder . '/full_php_browscap.ini';
        }

        $collection = new DataCollection($version);
        $collection->setLogger($this->logger);

        $collectionCreator
            ->setLogger($this->logger)
            ->setDataCollection($collection)
            ->createDataCollection($this->resourceFolder)
        ;

        $expander = new Expander();
        $expander
            ->setDataCollection($collection)
            ->setLogger($this->logger)
        ;

        $writerCollection = new WriterCollection();
        $fullFilter       = new FullFilter();

        $fullPhpWriter = new IniWriter($iniFile);
        $formatter     = new PhpFormatter();
        $fullPhpWriter
            ->setLogger($this->logger)
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter)
        ;
        $writerCollection->addWriter($fullPhpWriter);

        $comments = array(
            'Provided courtesy of http://browscap.org/',
            'Created on ' . $collection->getGenerationDate()->format('l, F j, Y \a\t h:i A T'),
            'Keep up with the latest goings-on with the project:',
            'Follow us on Twitter <https://twitter.com/browscap>, or...',
            'Like us on Facebook <https://facebook.com/browscap>, or...',
            'Collaborate on GitHub <https://github.com/browscap>, or...',
            'Discuss on Google Groups <https://groups.google.com/forum/#!forum/browscap>.'
        );

        $writerCollection
            ->fileStart()
            ->renderHeader($comments)
            ->renderVersion('test', $collection)
        ;

        $output = array();

        $writerCollection->renderAllDivisionsHeader($collection);

        $division = $collection->getDefaultProperties();

        $writerCollection->renderDivisionHeader($division->getName());

        $ua       = $division->getUserAgents();
        $sections = array($ua[0]['userAgent'] => $ua[0]['properties']);

        foreach ($sections as $sectionName => $section) {
            $writerCollection
                ->renderSectionHeader($sectionName)
                ->renderSectionBody($section, $collection, $sections, $sectionName)
                ->renderSectionFooter($sectionName)
            ;
        }

        $writerCollection->renderDivisionFooter();

        foreach ($collection->getDivisions() as $division) {
            /** @var \Browscap\Data\Division $division */
            $writerCollection->setSilent($division);

            $versions = $division->getVersions();

            foreach ($versions as $version) {
                list($majorVer, $minorVer) = $expander->getVersionParts($version);

                $divisionName = $expander->parseProperty($division->getName(), $majorVer, $minorVer);

                $writerCollection->renderDivisionHeader($divisionName);

                $sections     = $expander->expand($division, $majorVer, $minorVer, $divisionName);
                $firstElement = current($sections);

                $writerCollection->renderDivisionHeader($divisionName, $firstElement['Parent']);

                foreach ($sections as $sectionName => $section) {
                    if (in_array($sectionName, $output)) {
                        throw new \UnexpectedValueException(
                            'tried to add section "' . $sectionName . '" more than once'
                        );
                    }

                    $collection->checkProperty($sectionName, $section);

                    $writerCollection
                        ->renderSectionHeader($sectionName)
                        ->renderSectionBody($section, $collection, $sections, $sectionName)
                        ->renderSectionFooter($sectionName)
                    ;

                    $output[] = $sectionName;
                }

                $writerCollection->renderDivisionFooter();

                unset($divisionName, $majorVer, $minorVer);
            }
        }

        $division = $collection->getDefaultBrowser();

        $writerCollection->renderDivisionHeader($division->getName());

        $ua       = $division->getUserAgents();
        $sections = array(
            $ua[0]['userAgent'] => array_merge(
                array('Parent' => 'DefaultProperties'),
                $ua[0]['properties']
            )
        );

        foreach ($sections as $sectionName => $section) {
            $writerCollection
                ->renderSectionHeader($sectionName)
                ->renderSectionBody($section, $collection, $sections, $sectionName)
                ->renderSectionFooter($sectionName)
            ;
        }

        $writerCollection
            ->renderDivisionFooter()
            ->renderAllDivisionsFooter()
            ->fileEnd()
            ->close()
        ;

        $this->getLogger()->info('finished creating the full ini file for php');
    }
}
