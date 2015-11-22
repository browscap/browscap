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
 * @package    Data\Factory
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Writer\Factory;

use Browscap\Filter\FullFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;
use Browscap\Filter\LiteFilter;
use Browscap\Filter\StandardFilter;

/**
 * Class FullPhpWriterFactory
 *
 * @category   Browscap
 * @package    Data\Factory
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class PhpWriterFactory
{
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param string                   $buildFolder
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function createCollection(LoggerInterface $logger, $buildFolder)
    {
        $writerCollection = new WriterCollection();

        $fullFilter = new FullFilter();
        $stdFilter  = new StandardFilter();
        $liteFilter = new LiteFilter();

        $fullPhpWriter = new IniWriter($buildFolder . '/full_php_browscap.ini');
        $formatter     = new PhpFormatter();
        $fullPhpWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter)
        ;
        $writerCollection->addWriter($fullPhpWriter);

        $stdPhpWriter = new IniWriter($buildFolder . '/php_browscap.ini');
        $formatter    = new PhpFormatter();
        $stdPhpWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter)
        ;
        $writerCollection->addWriter($stdPhpWriter);

        $litePhpWriter = new IniWriter($buildFolder . '/lite_php_browscap.ini');
        $formatter     = new PhpFormatter();
        $litePhpWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($liteFilter))
            ->setFilter($liteFilter)
        ;
        $writerCollection->addWriter($litePhpWriter);

        return $writerCollection;
    }
}
