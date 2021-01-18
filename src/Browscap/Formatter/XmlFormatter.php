<?php

declare(strict_types=1);

namespace Browscap\Formatter;

use Browscap\Data\PropertyHolder;
use Exception;
use InvalidArgumentException;

use function htmlentities;
use function trim;

/**
 * this formatter is responsible to format the output into the "xml" version of the browscap files
 */
class XmlFormatter implements FormatterInterface
{
    /** @var PropertyHolder */
    private $propertyHolder;

    public function __construct(PropertyHolder $propertyHolder)
    {
        $this->propertyHolder = $propertyHolder;
    }

    /**
     * returns the Type of the formatter
     */
    public function getType(): string
    {
        return FormatterInterface::TYPE_XML;
    }

    /**
     * formats the name of a property
     */
    public function formatPropertyName(string $name): string
    {
        return htmlentities($name);
    }

    /**
     * formats the name of a property
     *
     * @param bool|int|string $value
     *
     * @throws Exception
     */
    public function formatPropertyValue($value, string $property): string
    {
        switch ($this->propertyHolder->getPropertyType($property)) {
            case PropertyHolder::TYPE_STRING:
                $valueOutput = htmlentities(trim((string) $value));

                break;
            case PropertyHolder::TYPE_BOOLEAN:
                if ($value === true || $value === 'true') {
                    $valueOutput = 'true';
                } elseif ($value === false || $value === 'false') {
                    $valueOutput = 'false';
                } else {
                    $valueOutput = '';
                }

                break;
            case PropertyHolder::TYPE_IN_ARRAY:
                try {
                    $valueOutput = htmlentities($this->propertyHolder->checkValueInArray($property, (string) $value));
                } catch (InvalidArgumentException $ex) {
                    $valueOutput = '';
                }

                break;
            default:
                $valueOutput = htmlentities((string) $value);

                break;
        }

        if ($valueOutput === 'unknown') {
            $valueOutput = '';
        }

        return $valueOutput;
    }
}
