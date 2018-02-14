<?php
declare(strict_types = 1);
namespace Browscap\Writer\Factory;

use Browscap\Data\PropertyHolder;
use Browscap\Filter\CustomFilter;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter;
use Browscap\Formatter\FormatterInterface;
use Browscap\Writer;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

/**
 * a factory to create a writer collection to write a custom browscap file
 */
class CustomWriterFactory
{
    /**
     * @param LoggerInterface $logger
     * @param string          $buildFolder
     * @param string|null     $file
     * @param array           $fields
     * @param string          $format
     * @param string          $outputType
     *
     * @return WriterCollection
     */
    public function createCollection(
        LoggerInterface $logger,
        string $buildFolder,
        ?string $file = null,
        array $fields = [],
        string $format = FormatterInterface::TYPE_PHP,
        string $outputType = FilterInterface::TYPE_FULL
    ) : WriterCollection {
        $writerCollection = new WriterCollection();
        $propertyHolder   = new PropertyHolder();

        if (null === $file) {
            switch ($format) {
                case FormatterInterface::TYPE_ASP:
                    $file = $buildFolder . '/full_browscap.ini';

                    break;
                case FormatterInterface::TYPE_CSV:
                    $file = $buildFolder . '/browscap.csv';

                    break;
                case FormatterInterface::TYPE_XML:
                    $file = $buildFolder . '/browscap.xml';

                    break;
                case FormatterInterface::TYPE_JSON:
                    $file = $buildFolder . '/browscap.json';

                    break;
                case FormatterInterface::TYPE_PHP:
                default:
                    $file = $buildFolder . '/full_php_browscap.ini';

                    break;
            }
        }

        $filter = new CustomFilter($propertyHolder, $fields, $outputType);

        switch ($format) {
            case FormatterInterface::TYPE_ASP:
                $writer    = new Writer\IniWriter($file, $logger);
                $formatter = new Formatter\AspFormatter($propertyHolder);

                break;
            case FormatterInterface::TYPE_CSV:
                $writer    = new Writer\CsvWriter($file, $logger);
                $formatter = new Formatter\CsvFormatter($propertyHolder);

                break;
            case FormatterInterface::TYPE_XML:
                $writer    = new Writer\XmlWriter($file, $logger);
                $formatter = new Formatter\XmlFormatter($propertyHolder);

                break;
            case FormatterInterface::TYPE_JSON:
                $writer    = new Writer\JsonWriter($file, $logger);
                $formatter = new Formatter\JsonFormatter($propertyHolder);

                break;
            case FormatterInterface::TYPE_PHP:
            default:
                $writer    = new Writer\IniWriter($file, $logger);
                $formatter = new Formatter\PhpFormatter($propertyHolder);

                break;
        }

        $writer->setFormatter($formatter);
        $writer->setFilter($filter);

        $writerCollection->addWriter($writer);

        return $writerCollection;
    }
}
