<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace Browscap\Generator;

use Browscap\Helper\CollectionCreator;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <mimmi20@live.de>
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
     * @var bool
     */
    protected $collectPatternIds = false;

    /**
     * @param string                   $resourceFolder
     * @param string                   $buildFolder
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(string $resourceFolder, string $buildFolder, LoggerInterface $logger)
    {
        $this->resourceFolder = $this->checkDirectoryExists($resourceFolder, 'resource');
        $this->buildFolder    = $this->checkDirectoryExists($buildFolder, 'build');
        $this->logger         = $logger;
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
     * @param \Browscap\Helper\CollectionCreator $collectionCreator
     */
    public function setCollectionCreator(CollectionCreator $collectionCreator) : void
    {
        $this->collectionCreator = $collectionCreator;
    }

    /**
     * @return \Browscap\Helper\CollectionCreator
     */
    public function getCollectionCreator() : CollectionCreator
    {
        return $this->collectionCreator;
    }

    /**
     * @param \Browscap\Writer\WriterCollection $writerCollection
     */
    public function setWriterCollection(WriterCollection $writerCollection) : void
    {
        $this->writerCollection = $writerCollection;
    }

    /**
     * @return \Browscap\Writer\WriterCollection
     */
    public function getWriterCollection() : WriterCollection
    {
        return $this->writerCollection;
    }

    /**
     * @param string $directory
     * @param string $type
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function checkDirectoryExists($directory, $type) : string
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
    public function run(string $version) : void
    {
        $this->preBuild();
        $this->build($version);
        $this->postBuild();
    }

    /**
     * runs before the build
     */
    protected function preBuild() : void
    {
        $this->logger->info('Resource folder: ' . $this->resourceFolder . '');
        $this->logger->info('Build folder: ' . $this->buildFolder . '');
    }

    /**
     * runs the build
     *
     * @param string $version
     */
    protected function build(string $version) : void
    {
        Helper\BuildHelper::run(
            $version,
            $this->resourceFolder,
            $this->logger,
            $this->getWriterCollection(),
            $this->getCollectionCreator(),
            $this->collectPatternIds
        );
    }

    /**
     * runs after the build
     */
    protected function postBuild() : void
    {
        // do nothing here
    }
}
