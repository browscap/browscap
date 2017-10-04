<?php
declare(strict_types = 1);
namespace Browscap\Writer\Factory;

use Browscap\Data\PropertyHolder;
use Browscap\Filter\FullFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

/**
 * Class FullPhpWriterFactory
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
    public function createCollection(LoggerInterface $logger, string $buildFolder, ?string $file = null) : WriterCollection
    {
        $writerCollection = new WriterCollection();
        $propertyHolder   = new PropertyHolder();

        if (null === $file) {
            $file = $buildFolder . '/full_php_browscap.ini';
        }

        $fullFilter    = new FullFilter($propertyHolder);
        $fullPhpWriter = new IniWriter($file, $logger);
        $formatter     = new PhpFormatter($propertyHolder);
        $fullPhpWriter->setFormatter($formatter);
        $fullPhpWriter->setFilter($fullFilter);

        $writerCollection->addWriter($fullPhpWriter);

        return $writerCollection;
    }
}
