<?php

namespace Browscap\Helper;

use Browscap\Generator\DataCollection;

class CollectionCreator
{
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
        $collection = new DataCollection($version);
        $collection->addPlatformsFile($resourceFolder . '/platforms.json');

        $uaSourceDirectory = $resourceFolder . '/user-agents';

        $iterator = new \RecursiveDirectoryIterator($uaSourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || $file->getExtension() != 'json') {
                continue;
            }

            $collection->addSourceFile($file->getPathname());
        }

        return $collection;
    }
}
