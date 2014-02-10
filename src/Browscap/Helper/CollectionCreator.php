<?php

namespace Browscap\Helper;

use Browscap\Generator\DataCollection;

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
     * Create and populate a data collection object from a resource folder
     *
     * @param string $version
     * @param string $resourceFolder
     *
     * @return \Browscap\Generator\DataCollection
     */
    public function createDataCollection($version, $resourceFolder)
    {
        if (null === $this->collection) {
            throw new \LogicException(
                'An instance of \\Browscap\\Generator\\DataCollection is required for this function. '
                . 'Please set it with setDataCollection'
            );
        }
        
        $this->collection->addPlatformsFile($resourceFolder . '/platforms.json');

        $uaSourceDirectory = $resourceFolder . '/user-agents';

        $iterator = new \RecursiveDirectoryIterator($uaSourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || $file->getExtension() != 'json') {
                continue;
            }

            $this->collection->addSourceFile($file->getPathname());
        }

        return $this->collection;
    }
}
