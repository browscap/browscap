<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   Browscap
 * @copyright  1998-2017 Browser Capabilities Project
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
     * @var bool
     */
    protected $collectPatternIds = false;

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
     * Sets the flag to collect pattern ids during this build
     *
     * @param bool $value
     *
     * @return \Browscap\Generator\AbstractBuildGenerator
     */
    public function setCollectPatternIds(bool $value) : self
    {
        $this->collectPatternIds = (bool) $value;

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
