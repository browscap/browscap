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
namespace Browscap\Helper;

use Browscap\Data\DataCollection;
use Psr\Log\LoggerInterface;

/**
 * Class CollectionCreator
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <mimmi20@live.de>
 */
class CollectionCreator
{
    /**
     * @var \Browscap\Data\DataCollection
     */
    private $collection;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Create a new collection creator
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Browscap\Data\DataCollection $collection
     */
    public function setDataCollection(DataCollection $collection) : void
    {
        $this->collection = $collection;
    }

    /**
     * Create and populate a data collection object from a resource folder
     *
     * @param string $resourceFolder
     *
     * @throws \LogicException
     *
     * @return \Browscap\Data\DataCollection
     */
    public function createDataCollection(string $resourceFolder) : DataCollection
    {
        if (null === $this->collection) {
            throw new \LogicException(
                'An instance of \Browscap\Data\DataCollection is required for this function. '
                . 'Please set it with setDataCollection'
            );
        }

        $this->logger->debug('add platform file');
        $this->collection->addPlatformsFile($resourceFolder . '/platforms.json');
        $this->collection->addEnginesFile($resourceFolder . '/engines.json');
        $this->collection->addDefaultProperties($resourceFolder . '/core/default-properties.json');
        $this->collection->addDefaultBrowser($resourceFolder . '/core/default-browser.json');

        $deviceDirectory = $resourceFolder . '/devices';

        $iterator = new \RecursiveDirectoryIterator($deviceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || 'json' !== $file->getExtension()) {
                continue;
            }

            $this->logger->debug('add device file ' . $file->getPathname());
            $this->collection->addDevicesFile($file->getPathname());
        }

        $uaSourceDirectory = $resourceFolder . '/user-agents';

        $iterator = new \RecursiveDirectoryIterator($uaSourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || 'json' !== $file->getExtension()) {
                continue;
            }

            $this->logger->debug('add source file ' . $file->getPathname());
            $this->collection->addSourceFile($file->getPathname());
        }

        return $this->collection;
    }
}
