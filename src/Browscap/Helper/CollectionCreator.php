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

namespace Browscap\Helper;

use Browscap\Data\DataCollection;
use Psr\Log\LoggerInterface;

/**
 * Class CollectionCreator
 *
 * @category   Browscap
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class CollectionCreator
{
    /**
     * @var \Browscap\Data\DataCollection
     */
    private $collection = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @param \Browscap\Data\DataCollection $collection
     *
     * @return \Browscap\Helper\CollectionCreator
     */
    public function setDataCollection(DataCollection $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Helper\CollectionCreator
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return \Psr\Log\LoggerInterface $logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Create and populate a data collection object from a resource folder
     *
     * @param string $resourceFolder
     *
     * @throws \LogicException
     * @return \Browscap\Data\DataCollection
     */
    public function createDataCollection($resourceFolder)
    {
        if (null === $this->collection) {
            throw new \LogicException(
                'An instance of \Browscap\Data\DataCollection is required for this function. '
                . 'Please set it with setDataCollection'
            );
        }

        $this->getLogger()->debug('add platform file');
        $this->collection
            ->addPlatformsFile($resourceFolder . '/platforms.json')
            ->addEnginesFile($resourceFolder . '/engines.json')
            ->addDefaultProperties($resourceFolder . '/core/default-properties.json')
            ->addDefaultBrowser($resourceFolder . '/core/default-browser.json');

        $deviceDirectory = $resourceFolder . '/devices';

        $iterator = new \RecursiveDirectoryIterator($deviceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || $file->getExtension() !== 'json') {
                continue;
            }

            $this->getLogger()->debug('add device file ' . $file->getPathname());
            $this->collection->addDevicesFile($file->getPathname());
        }

        $uaSourceDirectory = $resourceFolder . '/user-agents';

        $iterator = new \RecursiveDirectoryIterator($uaSourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || $file->getExtension() !== 'json') {
                continue;
            }

            $this->getLogger()->debug('add source file ' . $file->getPathname());
            $this->collection->addSourceFile($file->getPathname());
        }

        return $this->collection;
    }
}
