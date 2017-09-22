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

use Browscap\Data\PropertyHolder;
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
    public function createCollection(LoggerInterface $logger, string $buildFolder) : WriterCollection
    {
        $writerCollection = new WriterCollection();
        $propertyHolder   = new PropertyHolder();

        $fullFilter = new FullFilter($propertyHolder);
        $stdFilter  = new StandardFilter($propertyHolder);
        $liteFilter = new LiteFilter($propertyHolder);

        $aspFormatter  = new AspFormatter($propertyHolder);
        $phpFormatter  = new PhpFormatter($propertyHolder);
        $csvFormatter  = new CsvFormatter($propertyHolder);
        $xmlFormatter  = new XmlFormatter($propertyHolder);
        $jsonFormatter = new JsonFormatter($propertyHolder);

        $fullAspWriter = new IniWriter($buildFolder . '/full_asp_browscap.ini', $logger);
        $fullAspWriter->setFormatter($aspFormatter);
        $fullAspWriter->setFilter($fullFilter);
        $writerCollection->addWriter($fullAspWriter);

        $fullPhpWriter = new IniWriter($buildFolder . '/full_php_browscap.ini', $logger);
        $fullPhpWriter->setFormatter($phpFormatter);
        $fullPhpWriter->setFilter($fullFilter);
        $writerCollection->addWriter($fullPhpWriter);

        $stdAspWriter = new IniWriter($buildFolder . '/browscap.ini', $logger);
        $stdAspWriter->setFormatter($aspFormatter);
        $stdAspWriter->setFilter($stdFilter);
        $writerCollection->addWriter($stdAspWriter);

        $stdPhpWriter = new IniWriter($buildFolder . '/php_browscap.ini', $logger);
        $stdPhpWriter->setFormatter($phpFormatter);
        $stdPhpWriter->setFilter($stdFilter);
        $writerCollection->addWriter($stdPhpWriter);

        $liteAspWriter = new IniWriter($buildFolder . '/lite_asp_browscap.ini', $logger);
        $liteAspWriter->setFormatter($aspFormatter);
        $liteAspWriter->setFilter($liteFilter);
        $writerCollection->addWriter($liteAspWriter);

        $litePhpWriter = new IniWriter($buildFolder . '/lite_php_browscap.ini', $logger);
        $litePhpWriter->setFormatter($phpFormatter);
        $litePhpWriter->setFilter($liteFilter);
        $writerCollection->addWriter($litePhpWriter);

        $csvWriter = new CsvWriter($buildFolder . '/browscap.csv', $logger);
        $csvWriter->setFormatter($csvFormatter);
        $csvWriter->setFilter($fullFilter);
        $writerCollection->addWriter($csvWriter);

        $xmlWriter = new XmlWriter($buildFolder . '/browscap.xml', $logger);
        $xmlWriter->setFormatter($xmlFormatter);
        $xmlWriter->setFilter($stdFilter);
        $writerCollection->addWriter($xmlWriter);

        $jsonWriter = new JsonWriter($buildFolder . '/browscap.json', $logger);
        $jsonWriter->setFormatter($jsonFormatter);
        $jsonWriter->setFilter($fullFilter);
        $writerCollection->addWriter($jsonWriter);

        $liteJsonWriter = new JsonWriter($buildFolder . '/lite_browscap.json', $logger);
        $liteJsonWriter->setFormatter($jsonFormatter);
        $liteJsonWriter->setFilter($liteFilter);
        $writerCollection->addWriter($liteJsonWriter);

        return $writerCollection;
    }
}
