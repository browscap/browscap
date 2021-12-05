<?php

declare(strict_types=1);

namespace Browscap\Formatter;

use Browscap\Data\PropertyHolder;
use Exception;
use InvalidArgumentException;

use function trim;

/**
 * this formatter is responsible to format the output into the "asp" ini version of the browscap files
 */
class AspFormatter implements FormatterInterface
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
        return FormatterInterface::TYPE_ASP;
    }

    /**
     * formats the name of a property
     */
    public function formatPropertyName(string $name): string
    {
        return $name;
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
                $valueOutput = trim((string) $value);

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
                    $valueOutput = $this->propertyHolder->checkValueInArray($property, (string) $value);
                } catch (InvalidArgumentException $ex) {
                    $valueOutput = '';
                }

                break;
            default:
                $valueOutput = (string) $value;

                break;
        }

        return $valueOutput;
    }
}
