<?php

namespace Browscap\Helper;

use Browscap\Generator\DataCollection;
use Psr\Log\LoggerInterface;

/**
 * Class CollectionCreator
 *
 * @package Browscap\Helper
 * @author Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class CollectionCreator
{
    /**
     * @var \Browscap\Generator\DataCollection
     */
    private $collection = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @param \Browscap\Generator\DataCollection $collection
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
     * @return \Browscap\Generator\DataCollection
     */
    public function createDataCollection($resourceFolder)
    {
        if (null === $this->collection) {
            throw new \LogicException(
                'An instance of \Browscap\Generator\DataCollection is required for this function. '
                . 'Please set it with setDataCollection'
            );
        }

        $this->getLogger()->debug('add platform file');
        $this->collection
            ->addPlatformsFile($resourceFolder . '/platforms.json')
            ->addEnginesFile($resourceFolder . '/engines.json')
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
