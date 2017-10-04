<?php
declare(strict_types = 1);
namespace Browscap\Formatter;

use Browscap\Data\PropertyHolder;

/**
 * Class CsvFormatter
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class CsvFormatter implements FormatterInterface
{
    /**
     * @var \Browscap\Data\PropertyHolder
     */
    private $propertyHolder;

    /**
     * @param \Browscap\Data\PropertyHolder $propertyHolder
     */
    public function __construct(PropertyHolder $propertyHolder)
    {
        $this->propertyHolder = $propertyHolder;
    }

    /**
     * returns the Type of the formatter
     *
     * @return string
     */
    public function getType() : string
    {
        return FormatterInterface::TYPE_CSV;
    }

    /**
     * formats the name of a property
     *
     * @param string $name
     *
     * @return string
     */
    public function formatPropertyName(string $name) : string
    {
        return '"' . str_replace('"', '""', $name) . '"';
    }

    /**
     * formats the name of a property
     *
     * @param bool|string $value
     * @param string      $property
     *
     * @return string
     */
    public function formatPropertyValue($value, string $property) : string
    {
        $valueOutput = $value;

        switch ($this->propertyHolder->getPropertyType($property)) {
            case PropertyHolder::TYPE_STRING:
                $valueOutput = trim((string) $value);

                break;
            case PropertyHolder::TYPE_BOOLEAN:
                if (true === $value || 'true' === $value) {
                    $valueOutput = 'true';
                } elseif (false === $value || 'false' === $value) {
                    $valueOutput = 'false';
                } else {
                    $valueOutput = '';
                }

                break;
            case PropertyHolder::TYPE_IN_ARRAY:
                try {
                    $valueOutput = $this->propertyHolder->checkValueInArray($property, (string) $value);
                } catch (\InvalidArgumentException $ex) {
                    $valueOutput = '';
                }

                break;
            default:
                // nothing t do here
                break;
        }

        if ('unknown' === $valueOutput) {
            $valueOutput = '';
        }

        return '"' . str_replace('"', '""', $valueOutput) . '"';
    }
}
