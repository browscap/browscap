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
 * @package    Generator
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Generator;

use Browscap\Filter\FullFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 * @package    Generator
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class BuildFullFileOnlyGenerator extends AbstractBuildGenerator
{
    /**
     * @return \Browscap\Helper\CollectionCreator
     */
    public function getCollectionCreator()
    {
        if (null === $this->collectionCreator) {
            $this->collectionCreator = new CollectionCreator();
        }

        return $this->collectionCreator;
    }

    /**
     * @param string|null $file
     *
*@return \Browscap\Writer\WriterCollection
     */
    public function getWriterCollection($file = null)
    {
        if (null === $this->writerCollection) {
            if (null === $file) {
                $file = $this->buildFolder . '/full_php_browscap.ini';
            }

            $this->writerCollection = new WriterCollection();
            $fullFilter       = new FullFilter();

            $fullPhpWriter = new IniWriter($file);
            $formatter     = new PhpFormatter();
            $fullPhpWriter
                ->setLogger($this->getLogger())
                ->setFormatter($formatter->setFilter($fullFilter))
                ->setFilter($fullFilter)
            ;
            $this->writerCollection->addWriter($fullPhpWriter);
        }

        return $this->writerCollection;
    }

    /**
     * Entry point for generating builds for a specified version
     *
     * @param string      $version
     * @param string|null $iniFile
     */
    public function run($version, $iniFile = null)
    {
        $this->getLogger()->info('Resource folder: ' . $this->resourceFolder . '');
        $this->getLogger()->info('Build folder: ' . $this->buildFolder . '');

        $this->getLogger()->info('started creating the full ini file for php');

        Helper\BuildHelper::run(
            $version,
            $this->resourceFolder,
            $this->getLogger(),
            $this->getWriterCollection($iniFile),
            $this->getCollectionCreator()
        );

        $this->getLogger()->info('finished creating the full ini file for php');
    }
}
