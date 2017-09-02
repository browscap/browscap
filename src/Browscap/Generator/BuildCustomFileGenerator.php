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
use Browscap\Writer\Factory\CustomWriterFactory;

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
    /**
     * Entry point for generating builds for a specified version
     *
     * @param string      $buildVersion
     * @param array       $fields
     * @param string|null $file
     * @param string      $format
     */
    public function run(
        string $buildVersion,
        array $fields = [],
        ?string $file = null,
        string $format = CustomWriterFactory::OUTPUT_FORMAT_PHP
    ) : void {
        $this->preBuild($fields, $file, $format);
        $this->build($buildVersion);
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
        $fields = [],
        $file = null,
        $format = CustomWriterFactory::OUTPUT_FORMAT_PHP
    ) : void {
        parent::preBuild();

        $this->logger->info('started creating the custom output file');

        if (null === $this->collectionCreator) {
            $this->setCollectionCreator(new CollectionCreator($this->logger));
        }

        if (null === $this->writerCollection) {
            $factory = new CustomWriterFactory();

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
