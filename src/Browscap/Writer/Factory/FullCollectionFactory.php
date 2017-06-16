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
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
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
