<?php

namespace Browscap\Writer\Factory;

use Browscap\Filter\FullFilter;
use Browscap\Filter\LiteFilter;
use Browscap\Filter\StandartFilter;
use Browscap\Formatter\AspFormatter;
use Browscap\Formatter\CsvFormatter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Formatter\XmlFormatter;
use Browscap\Writer\CsvWriter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use Browscap\Writer\XmlWriter;
use Psr\Log\LoggerInterface;

/**
 * Class BuildGenerator
 *
 * @package Browscap\Generator
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
        $stdFilter  = new StandartFilter();
        $liteFilter = new LiteFilter();

        $fullAspWriter = new IniWriter($buildFolder . '/full_asp_browscap.ini');
        $formatter     = new AspFormatter();
        $fullAspWriter->setLogger($logger)
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter);
        $writerCollection->addWriter($fullAspWriter);

        $fullPhpWriter = new IniWriter($buildFolder . '/full_php_browscap.ini');
        $formatter     = new PhpFormatter();
        $fullPhpWriter->setLogger($logger)
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter);
        $writerCollection->addWriter($fullPhpWriter);

        $stdAspWriter = new IniWriter($buildFolder . '/browscap.ini');
        $formatter    = new AspFormatter();
        $stdAspWriter->setLogger($logger)
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter);
        $writerCollection->addWriter($stdAspWriter);

        $stdPhpWriter = new IniWriter($buildFolder . '/php_browscap.ini');
        $formatter    = new PhpFormatter();
        $stdPhpWriter->setLogger($logger)
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter);
        $writerCollection->addWriter($stdPhpWriter);

        $liteAspWriter = new IniWriter($buildFolder . '/lite_asp_browscap.ini');
        $formatter     = new AspFormatter();
        $liteAspWriter->setLogger($logger)
            ->setFormatter($formatter->setFilter($liteFilter))
            ->setFilter($liteFilter);
        $writerCollection->addWriter($liteAspWriter);

        $litePhpWriter = new IniWriter($buildFolder . '/lite_php_browscap.ini');
        $formatter     = new PhpFormatter();
        $litePhpWriter->setLogger($logger)
            ->setFormatter($formatter->setFilter($liteFilter))
            ->setFilter($liteFilter);
        $writerCollection->addWriter($litePhpWriter);

        $csvWriter = new CsvWriter($buildFolder . '/browscap.csv');
        $formatter = new CsvFormatter();
        $csvWriter->setLogger($logger)
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter);
        $writerCollection->addWriter($csvWriter);

        $xmlWriter = new XmlWriter($buildFolder . '/browscap.xml');
        $formatter = new XmlFormatter();
        $xmlWriter->setLogger($logger)
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter);
        $writerCollection->addWriter($xmlWriter);

        return $writerCollection;
    }
}
