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
class FullPhpWriterFactory
{
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param string                   $buildFolder
     * @param string|null              $file
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function createCollection(LoggerInterface $logger, $buildFolder, $file = null)
    {
        $writerCollection = new WriterCollection();

        if (null === $file) {
            $file = $buildFolder . '/full_php_browscap.ini';
        }

        $fullFilter    = new FullFilter();
        $fullPhpWriter = new IniWriter($file, $logger);
        $formatter     = new PhpFormatter();
        $formatter->setFilter($fullFilter);
        $fullPhpWriter
            ->setFormatter($formatter)
            ->setFilter($fullFilter);

        $writerCollection->addWriter($fullPhpWriter);

        return $writerCollection;
    }
}
