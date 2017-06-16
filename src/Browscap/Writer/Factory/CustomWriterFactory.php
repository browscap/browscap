<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   Browscap
 * @copyright  1998-2017 Browser Capabilities Project
 * @license    MIT
 */

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
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class CustomWriterFactory
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
