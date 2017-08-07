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
    public function createCollection(LoggerInterface $logger, $buildFolder)
    {
        $writerCollection = new WriterCollection();

        $fullFilter = new FullFilter();
        $stdFilter  = new StandardFilter();
        $liteFilter = new LiteFilter();

        $fullAspWriter = new IniWriter($buildFolder . '/full_asp_browscap.ini');
        $formatter     = new AspFormatter();
        $fullAspWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter);
        $writerCollection->addWriter($fullAspWriter);

        $fullPhpWriter = new IniWriter($buildFolder . '/full_php_browscap.ini');
        $formatter     = new PhpFormatter();
        $fullPhpWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter);
        $writerCollection->addWriter($fullPhpWriter);

        $stdAspWriter = new IniWriter($buildFolder . '/browscap.ini');
        $formatter    = new AspFormatter();
        $stdAspWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter);
        $writerCollection->addWriter($stdAspWriter);

        $stdPhpWriter = new IniWriter($buildFolder . '/php_browscap.ini');
        $formatter    = new PhpFormatter();
        $stdPhpWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter);
        $writerCollection->addWriter($stdPhpWriter);

        $liteAspWriter = new IniWriter($buildFolder . '/lite_asp_browscap.ini');
        $formatter     = new AspFormatter();
        $liteAspWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($liteFilter))
            ->setFilter($liteFilter);
        $writerCollection->addWriter($liteAspWriter);

        $litePhpWriter = new IniWriter($buildFolder . '/lite_php_browscap.ini');
        $formatter     = new PhpFormatter();
        $litePhpWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($liteFilter))
            ->setFilter($liteFilter);
        $writerCollection->addWriter($litePhpWriter);

        $csvWriter = new CsvWriter($buildFolder . '/browscap.csv');
        $formatter = new CsvFormatter();
        $csvWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter);
        $writerCollection->addWriter($csvWriter);

        $xmlWriter = new XmlWriter($buildFolder . '/browscap.xml');
        $formatter = new XmlFormatter();
        $xmlWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter);
        $writerCollection->addWriter($xmlWriter);

        $jsonWriter = new JsonWriter($buildFolder . '/browscap.json');
        $formatter  = new JsonFormatter();
        $jsonWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter);
        $writerCollection->addWriter($jsonWriter);

        $liteJsonWriter = new JsonWriter($buildFolder . '/lite_browscap.json');
        $formatter      = new JsonFormatter();
        $liteJsonWriter
          ->setLogger($logger)
          ->setFormatter($formatter->setFilter($liteFilter))
          ->setFilter($liteFilter);
        $writerCollection->addWriter($liteJsonWriter);

        return $writerCollection;
    }
}
