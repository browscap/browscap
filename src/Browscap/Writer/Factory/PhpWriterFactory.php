<?php

declare(strict_types=1);

namespace Browscap\Writer\Factory;

use Browscap\Data\PropertyHolder;
use Browscap\Filter\FullFilter;
use Browscap\Filter\LiteFilter;
use Browscap\Filter\StandardFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

/**
 * a factory to create a writer collection to write all php browscap files at once
 */
class PhpWriterFactory
{
    public function createCollection(LoggerInterface $logger, string $buildFolder): WriterCollection
    {
        $writerCollection = new WriterCollection();
        $propertyHolder   = new PropertyHolder();

        $fullFilter = new FullFilter($propertyHolder);
        $stdFilter  = new StandardFilter($propertyHolder);
        $liteFilter = new LiteFilter($propertyHolder);

        $formatter = new PhpFormatter($propertyHolder);

        $fullPhpWriter = new IniWriter($buildFolder . '/full_php_browscap.ini', $logger);
        $fullPhpWriter->setFormatter($formatter);
        $fullPhpWriter->setFilter($fullFilter);
        $writerCollection->addWriter($fullPhpWriter);

        $stdPhpWriter = new IniWriter($buildFolder . '/php_browscap.ini', $logger);
        $stdPhpWriter->setFormatter($formatter);
        $stdPhpWriter->setFilter($stdFilter);
        $writerCollection->addWriter($stdPhpWriter);

        $litePhpWriter = new IniWriter($buildFolder . '/lite_php_browscap.ini', $logger);
        $litePhpWriter->setFormatter($formatter);
        $litePhpWriter->setFilter($liteFilter);
        $writerCollection->addWriter($litePhpWriter);

        return $writerCollection;
    }
}
