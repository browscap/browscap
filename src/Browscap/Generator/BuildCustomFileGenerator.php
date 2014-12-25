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

use Browscap\Filter\CustomFilter;
use Browscap\Formatter;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer;
use Psr\Log\LoggerInterface;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 * @package    Generator
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
     * @param array       $fields
     * @param string      $format
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function getWriterCollection(
        $file = null,
        $fields = array(),
        $format = self::OUTPUT_FORMAT_PHP
    )
    {
        if (null === $this->writerCollection) {
            if (null === $file) {
                $file = $this->buildFolder . '/full_php_browscap.ini';
            }

            $this->writerCollection = new Writer\WriterCollection();
            $filter                 = new CustomFilter($fields);

            switch ($format) {
                case self::OUTPUT_FORMAT_ASP:
                    $writer    = new Writer\IniWriter($file);
                    $formatter = new Formatter\AspFormatter();
                    break;
                case self::OUTPUT_FORMAT_CSV:
                    $writer    = new Writer\CsvWriter($file);
                    $formatter = new Formatter\CsvFormatter();
                    break;
                case self::OUTPUT_FORMAT_XML:
                    $writer    = new Writer\XmlWriter($file);
                    $formatter = new Formatter\XmlFormatter();
                    break;
                case self::OUTPUT_FORMAT_JSON:
                    $writer    = new Writer\JsonWriter($file);
                    $formatter = new Formatter\JsonFormatter();
                    break;
                case self::OUTPUT_FORMAT_PHP:
                default:
                    $writer    = new Writer\IniWriter($file);
                    $formatter = new Formatter\PhpFormatter();
                    break;
            }

            $writer
                ->setLogger($this->getLogger())
                ->setFormatter($formatter->setFilter($filter))
                ->setFilter($filter)
            ;

            $this->writerCollection->addWriter($writer);
        }

        return $this->writerCollection;
    }

    /**
     * Entry point for generating builds for a specified version
     *
     * @param string      $version
     * @param array       $fields
     * @param string|null $file
     * @param string      $format
     */
    public function run(
        $version,
        $fields = array(),
        $file = null,
        $format = self::OUTPUT_FORMAT_PHP
    ) {
        $this->getLogger()->info('Resource folder: ' . $this->resourceFolder . '');
        $this->getLogger()->info('Build folder: ' . $this->buildFolder . '');

        $this->getLogger()->info('started creating the custom output file');

        Helper\BuildHelper::run(
            $version,
            $this->resourceFolder,
            $this->getLogger(),
            $this->getWriterCollection($file, $fields, $format),
            $this->getCollectionCreator()
        );

        $this->getLogger()->info('finished creating the custom output file');
    }
}
