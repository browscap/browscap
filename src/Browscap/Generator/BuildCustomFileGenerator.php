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
use Browscap\Writer;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <mimmi20@live.de>
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
     */
    public function run(
        string $version,
        array $fields = [],
        $file = null,
        string $format = self::OUTPUT_FORMAT_PHP
    ) : void {
        $this->preBuild($fields, $file, $format);
        $this->build($version);
        $this->postBuild();
    }

    /**
     * runs before the build
     *
     * @param array       $fields
     * @param string|null $file
     * @param string      $format
     */
    protected function preBuild(
        array $fields = [],
        $file = null,
        string $format = self::OUTPUT_FORMAT_PHP
    ) : void {
        parent::preBuild();

        $this->logger->info('started creating the custom output file');

        if (null === $this->collectionCreator) {
            $this->setCollectionCreator(new CollectionCreator($this->logger));
        }

        if (null === $this->writerCollection) {
            $factory = new Writer\Factory\CustomWriterFactory();

            $this->setWriterCollection(
                $factory->createCollection(
                    $this->logger,
                    $this->buildFolder,
                    $file,
                    $fields,
                    $format
                )
            );
        }
    }

    /**
     * runs after the build
     */
    protected function postBuild() : void
    {
        $this->logger->info('finished creating the custom output file');
    }
}
