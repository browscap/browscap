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
use Browscap\Formatter\AspFormatter;
use Browscap\Formatter\CsvFormatter;
use Browscap\Formatter\JsonFormatter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Formatter\XmlFormatter;
use Browscap\Writer\CsvWriter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\JsonWriter;
use Browscap\Writer\WriterCollection;
use Browscap\Writer\XmlWriter;
use Psr\Log\LoggerInterface;

/**
 * Class FullCollectionFactory
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class FullCollectionFactory
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

        $fullAspWriter = new IniWriter($buildFolder . '/full_asp_browscap.ini', $logger);
        $formatter     = new AspFormatter();
        $formatter->setFilter($fullFilter);
        $fullAspWriter
            ->setFormatter($formatter)
            ->setFilter($fullFilter);
        $writerCollection->addWriter($fullAspWriter);

        $fullPhpWriter = new IniWriter($buildFolder . '/full_php_browscap.ini', $logger);
        $formatter     = new PhpFormatter();
        $formatter->setFilter($fullFilter);
        $fullPhpWriter
            ->setFormatter($formatter)
            ->setFilter($fullFilter);
        $writerCollection->addWriter($fullPhpWriter);

        $stdAspWriter = new IniWriter($buildFolder . '/browscap.ini', $logger);
        $formatter    = new AspFormatter();
        $formatter->setFilter($stdFilter);
        $stdAspWriter
            ->setFormatter($formatter)
            ->setFilter($stdFilter);
        $writerCollection->addWriter($stdAspWriter);

        $stdPhpWriter = new IniWriter($buildFolder . '/php_browscap.ini', $logger);
        $formatter    = new PhpFormatter();
        $formatter->setFilter($stdFilter);
        $stdPhpWriter
            ->setFormatter($formatter)
            ->setFilter($stdFilter);
        $writerCollection->addWriter($stdPhpWriter);

        $liteAspWriter = new IniWriter($buildFolder . '/lite_asp_browscap.ini', $logger);
        $formatter     = new AspFormatter();
        $formatter->setFilter($liteFilter);
        $liteAspWriter
            ->setFormatter($formatter)
            ->setFilter($liteFilter);
        $writerCollection->addWriter($liteAspWriter);

        $litePhpWriter = new IniWriter($buildFolder . '/lite_php_browscap.ini', $logger);
        $formatter     = new PhpFormatter();
        $formatter->setFilter($liteFilter);
        $litePhpWriter
            ->setFormatter($formatter)
            ->setFilter($liteFilter);
        $writerCollection->addWriter($litePhpWriter);

        $csvWriter = new CsvWriter($buildFolder . '/browscap.csv', $logger);
        $formatter = new CsvFormatter();
        $formatter->setFilter($fullFilter);
        $csvWriter
            ->setFormatter($formatter)
            ->setFilter($fullFilter);
        $writerCollection->addWriter($csvWriter);

        $xmlWriter = new XmlWriter($buildFolder . '/browscap.xml', $logger);
        $formatter = new XmlFormatter();
        $formatter->setFilter($stdFilter);
        $xmlWriter
            ->setFormatter($formatter)
            ->setFilter($stdFilter);
        $writerCollection->addWriter($xmlWriter);

        $jsonWriter = new JsonWriter($buildFolder . '/browscap.json', $logger);
        $formatter  = new JsonFormatter();
        $formatter->setFilter($fullFilter);
        $jsonWriter
            ->setFormatter($formatter)
            ->setFilter($fullFilter);
        $writerCollection->addWriter($jsonWriter);

        $liteJsonWriter = new JsonWriter($buildFolder . '/lite_browscap.json', $logger);
        $formatter      = new JsonFormatter();
        $formatter->setFilter($liteFilter);

        $liteJsonWriter
          ->setFormatter($formatter)
          ->setFilter($liteFilter);
        $writerCollection->addWriter($liteJsonWriter);

        return $writerCollection;
    }
}
