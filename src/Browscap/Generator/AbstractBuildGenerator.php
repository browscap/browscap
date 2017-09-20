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
    protected $logger;

    /**
     * @var \Browscap\Helper\CollectionCreator
     */
    protected $collectionCreator;

    /**
     * @var \Browscap\Writer\WriterCollection
     */
    protected $writerCollection;

    /**
     * @var bool
     */
    protected $collectPatternIds = false;

    /**
     * @param string $resourceFolder
     * @param string $buildFolder
     *
     * @throws \Exception
     */
    public function __construct(string $resourceFolder, string $buildFolder)
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
     * Sets the flag to collect pattern ids during this build
     *
     * @param bool $value
     *
     * @return \Browscap\Generator\AbstractBuildGenerator
     */
    public function setCollectPatternIds(bool $value) : self
    {
        $this->collectPatternIds = $value;

        return $this;
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
     *
     * @return string
     */
    protected function checkDirectoryExists($directory, $type)
    {
        if (!isset($directory)) {
            throw new \Exception('You must specify a ' . $type . ' folder');
        }

        $realDirectory = realpath($directory);

        if (false === $realDirectory) {
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
     * @param string $buildVersion
     *
     * @return \Browscap\Generator\AbstractBuildGenerator
     */
    public function run($buildVersion)
    {
        return $this
            ->preBuild()
            ->build($buildVersion)
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
     * @param string $buildVersion
     *
     * @return \Browscap\Generator\AbstractBuildGenerator
     */
    protected function build($buildVersion)
    {
        Helper\BuildHelper::run(
            $buildVersion,
            $this->resourceFolder,
            $this->getLogger(),
            $this->getWriterCollection(),
            $this->getCollectionCreator(),
            $this->collectPatternIds
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
