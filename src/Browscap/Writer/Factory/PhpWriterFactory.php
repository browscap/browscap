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
namespace Browscap\Writer\Factory;

use Browscap\Filter\FullFilter;
use Browscap\Filter\LiteFilter;
use Browscap\Filter\StandardFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

/**
 * Class FullPhpWriterFactory
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class PhpWriterFactory
{
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param string                   $buildFolder
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function createCollection(LoggerInterface $logger, string $buildFolder): WriterCollection
    {
        $writerCollection = new WriterCollection();

        $fullFilter = new FullFilter();
        $stdFilter  = new StandardFilter();
        $liteFilter = new LiteFilter();

        $fullPhpWriter = new IniWriter($buildFolder . '/full_php_browscap.ini', $logger);
        $formatter     = new PhpFormatter();
        $formatter->setFilter($fullFilter);
        $fullPhpWriter
            ->setFormatter($formatter)
            ->setFilter($fullFilter);
        $writerCollection->addWriter($fullPhpWriter);

        $stdPhpWriter = new IniWriter($buildFolder . '/php_browscap.ini', $logger);
        $formatter    = new PhpFormatter();
        $formatter->setFilter($stdFilter);
        $stdPhpWriter
            ->setFormatter($formatter)
            ->setFilter($stdFilter);
        $writerCollection->addWriter($stdPhpWriter);

        $litePhpWriter = new IniWriter($buildFolder . '/lite_php_browscap.ini', $logger);
        $formatter     = new PhpFormatter();
        $formatter->setFilter($liteFilter);
        $litePhpWriter
            ->setFormatter($formatter)
            ->setFilter($liteFilter);
        $writerCollection->addWriter($litePhpWriter);

        return $writerCollection;
    }
}
