<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Browscap\Generator;

use Browscap\Helper\CollectionCreator;
use Browscap\Writer;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class BuildCustomFileGenerator extends AbstractBuildGenerator
{
    /**@+
     * @var string
     */
    const OUTPUT_FORMAT_PHP  = 'php';
    const OUTPUT_FORMAT_ASP  = 'asp';
    const OUTPUT_FORMAT_CSV  = 'csv';
    const OUTPUT_FORMAT_XML  = 'xml';
    const OUTPUT_FORMAT_JSON = 'json';
    /**@-*/

    /**@+
     * @var string
     */
    const OUTPUT_TYPE_FULL    = 'full';
    const OUTPUT_TYPE_DEFAULT = 'normal';
    const OUTPUT_TYPE_LITE    = 'lite';
    /**@-*/

    /**
     * Entry point for generating builds for a specified version
     *
     * @param string      $version
     * @param array       $fields
     * @param string|null $file
     * @param string      $format
     *
     * @return \Browscap\Generator\BuildCustomFileGenerator
     */
    public function run(
        $version,
        $fields = [],
        $file = null,
        $format = self::OUTPUT_FORMAT_PHP
    ) {
        return $this
            ->preBuild($fields, $file, $format)
            ->build($version)
            ->postBuild();
    }

    /**
     * runs before the build
     *
     * @param array       $fields
     * @param string|null $file
     * @param string      $format
     *
     * @return \Browscap\Generator\BuildCustomFileGenerator
     */
    protected function preBuild(
        $fields = [],
        $file = null,
        $format = self::OUTPUT_FORMAT_PHP
    ) {
        parent::preBuild();

        $this->getLogger()->info('started creating the custom output file');

        if (null === $this->collectionCreator) {
            $this->setCollectionCreator(new CollectionCreator());
        }

        if (null === $this->writerCollection) {
            $factory = new Writer\Factory\CustomWriterFactory();

            $this->setWriterCollection(
                $factory->createCollection(
                    $this->getLogger(),
                    $this->buildFolder,
                    $file,
                    $fields,
                    $format
                )
            );
        }

        return $this;
    }

    /**
     * runs after the build
     *
     * @return \Browscap\Generator\BuildCustomFileGenerator
     */
    protected function postBuild()
    {
        $this->getLogger()->info('finished creating the custom output file');

        return $this;
    }
}
