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
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Generator;

use Browscap\Helper\CollectionCreator;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
abstract class AbstractBuildGenerator
{
    /**
     * @var string
     */
    protected $resourceFolder;

    /**
     * @var string
     */
    protected $buildFolder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger = null;

    /**
     * @var \Browscap\Helper\CollectionCreator
     */
    protected $collectionCreator = null;

    /**
     * @var \Browscap\Writer\WriterCollection
     */
    protected $writerCollection = null;

    /**
     * @param string $resourceFolder
     * @param string $buildFolder
     *
     * @throws \Exception
     */
    public function __construct($resourceFolder, $buildFolder)
    {
        $this->resourceFolder = $this->checkDirectoryExists($resourceFolder, 'resource');
        $this->buildFolder    = $this->checkDirectoryExists($buildFolder, 'build');
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Generator\AbstractBuildGenerator
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
     * @return \Browscap\Generator\AbstractBuildGenerator
     */
    public function setCollectionCreator(CollectionCreator $collectionCreator)
    {
        $this->collectionCreator = $collectionCreator;

        return $this;
    }

    /**
     * @return \Browscap\Helper\CollectionCreator
     */
    public function getCollectionCreator()
    {
        return $this->collectionCreator;
    }

    /**
     * @param \Browscap\Writer\WriterCollection $writerCollection
     *
     * @return \Browscap\Generator\AbstractBuildGenerator
     */
    public function setWriterCollection(WriterCollection $writerCollection)
    {
        $this->writerCollection = $writerCollection;

        return $this;
    }

    /**
     * @return \Browscap\Writer\WriterCollection
     */
    public function getWriterCollection()
    {
        return $this->writerCollection;
    }

    /**
     * @param string $directory
     * @param string $type
     *
     * @throws \Exception
     * @return string
     */
    protected function checkDirectoryExists($directory, $type)
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
    /**
     * Entry point for generating builds for a specified version
     *
     * @param string $version
     *
     * @return \Browscap\Generator\AbstractBuildGenerator
     */
    public function run($version)
    {
        return $this
            ->preBuild()
            ->build($version)
            ->postBuild();
    }

    /**
     * runs before the build
     *
     * @return \Browscap\Generator\AbstractBuildGenerator
     */
    protected function preBuild()
    {
        $this->getLogger()->info('Resource folder: ' . $this->resourceFolder . '');
        $this->getLogger()->info('Build folder: ' . $this->buildFolder . '');

        return $this;
    }

    /**
     * runs the build
     *
     * @param string $version
     *
     * @return \Browscap\Generator\AbstractBuildGenerator
     */
    protected function build($version)
    {
        Helper\BuildHelper::run(
            $version,
            $this->resourceFolder,
            $this->getLogger(),
            $this->getWriterCollection(),
            $this->getCollectionCreator()
        );

        return $this;
    }

    /**
     * runs after the build
     *
     * @return \Browscap\Generator\AbstractBuildGenerator
     */
    protected function postBuild()
    {
        return $this;
    }
}
