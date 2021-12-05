<?php

declare(strict_types=1);

namespace Browscap\Formatter;

use Browscap\Data\PropertyHolder;
use Exception;
use InvalidArgumentException;
use JsonException;

use function json_encode;
use function trim;

use const JSON_THROW_ON_ERROR;

/**
 * this formatter is responsible to format the output into the "json" version of the browscap files
 */
class JsonFormatter implements FormatterInterface
{
    private PropertyHolder $propertyHolder;

    /**
     * @throws void
     */
    public function __construct(PropertyHolder $propertyHolder)
    {
        $this->propertyHolder = $propertyHolder;
    }

    /**
     * returns the Type of the formatter
     *
     * @throws void
     */
    public function getType(): string
    {
        return FormatterInterface::TYPE_JSON;
    }

    /**
     * formats the name of a property
     *
     * @throws JsonException
     */
    public function formatPropertyName(string $name): string
    {
        return json_encode($name, JSON_THROW_ON_ERROR);
    }

    /**
     * formats the name of a property
     *
     * @param bool|int|string $value
     *
     * @throws Exception
     * @throws JsonException
     */
    public function formatPropertyValue($value, string $property): string
    {
        switch ($this->propertyHolder->getPropertyType($property)) {
            case PropertyHolder::TYPE_STRING:
                $valueOutput = json_encode(trim((string) $value), JSON_THROW_ON_ERROR);

                break;
            case PropertyHolder::TYPE_BOOLEAN:
                if ($value === true || $value === 'true') {
                    $valueOutput = 'true';
                } elseif ($value === false || $value === 'false') {
                    $valueOutput = 'false';
                } else {
                    $valueOutput = '""';
                }

                break;
            case PropertyHolder::TYPE_IN_ARRAY:
                try {
                    $valueOutput = json_encode($this->propertyHolder->checkValueInArray($property, (string) $value), JSON_THROW_ON_ERROR);
                } catch (InvalidArgumentException $ex) {
                    $valueOutput = '""';
                }

                break;
            default:
                $valueOutput = json_encode($value, JSON_THROW_ON_ERROR);

                break;
        }

        if ($valueOutput === 'unknown' || $valueOutput === '"unknown"') {
            $valueOutput = '""';
        }

        return $valueOutput;
    }
}
