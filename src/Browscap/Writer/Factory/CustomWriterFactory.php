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

use Browscap\Filter\CustomFilter;
use Browscap\Formatter;
use Browscap\Writer;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

/**
 * Class FullPhpWriterFactory
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class CustomWriterFactory
{
    /**@+
     * @var string
     */
    public const OUTPUT_FORMAT_PHP  = 'php';
    public const OUTPUT_FORMAT_ASP  = 'asp';
    public const OUTPUT_FORMAT_CSV  = 'csv';
    public const OUTPUT_FORMAT_XML  = 'xml';
    public const OUTPUT_FORMAT_JSON = 'json';
    /**@-*/

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param string                   $buildFolder
     * @param string|null              $file
     * @param array                    $fields
     * @param string                   $format
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function createCollection(
        LoggerInterface $logger,
        $buildFolder,
        $file = null,
        $fields = [],
        $format = self::OUTPUT_FORMAT_PHP
    ) {
        $writerCollection = new WriterCollection();

        if (null === $file) {
            switch ($format) {
                case self::OUTPUT_FORMAT_ASP:
                    $file = $buildFolder . '/full_browscap.ini';

                    break;
                case self::OUTPUT_FORMAT_CSV:
                    $file = $buildFolder . '/browscap.csv';

                    break;
                case self::OUTPUT_FORMAT_XML:
                    $file = $buildFolder . '/browscap.xml';

                    break;
                case self::OUTPUT_FORMAT_JSON:
                    $file = $buildFolder . '/browscap.json';

                    break;
                case self::OUTPUT_FORMAT_PHP:
                default:
                    $file = $buildFolder . '/full_php_browscap.ini';

                    break;
            }
        }

        $filter = new CustomFilter($fields);

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
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($filter))
            ->setFilter($filter);

        $writerCollection->addWriter($writer);

        return $writerCollection;
    }
}
