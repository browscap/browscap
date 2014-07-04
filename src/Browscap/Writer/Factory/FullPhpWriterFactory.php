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
class FullPhpWriterFactory
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

        $fullFilter    = new FullFilter();
        $fullPhpWriter = new IniWriter($buildFolder . '/full_php_browscap.ini');
        $formatter     = new PhpFormatter();
        $fullPhpWriter->setLogger($logger)
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter);
        $writerCollection->addWriter($fullPhpWriter);

        return $writerCollection;
    }
}
