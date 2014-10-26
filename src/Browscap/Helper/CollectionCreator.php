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
 * @package    Helper
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Helper;

use Browscap\Data\DataCollection;
use Psr\Log\LoggerInterface;

/**
 * Class CollectionCreator
 *
 * @category   Browscap
 * @package    Helper
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
            ->addDevicesFile($resourceFolder . '/devices.json')
            ->addDefaultProperties($resourceFolder . '/core/default-properties.json')
            ->addDefaultBrowser($resourceFolder . '/core/default-browser.json')
        ;

        $uaSourceDirectory = $resourceFolder . '/user-agents';

        $iterator = new \RecursiveDirectoryIterator($uaSourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || $file->getExtension() != 'json') {
                continue;
            }

            $this->getLogger()->debug('add source file ' . $file->getPathname());
            $this->collection->addSourceFile($file->getPathname());
        }

        return $this->collection;
    }
}
