<?php
declare(strict_types = 1);
namespace Browscap\Formatter;

use Browscap\Data\PropertyHolder;

/**
 * Class JsonFormatter
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class JsonFormatter implements FormatterInterface
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
        return FormatterInterface::TYPE_JSON;
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
        return json_encode($name);
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
        switch ($this->propertyHolder->getPropertyType($property)) {
            case PropertyHolder::TYPE_STRING:
                $valueOutput = json_encode(trim((string) $value));

                break;
            case PropertyHolder::TYPE_BOOLEAN:
                if (true === $value || 'true' === $value) {
                    $valueOutput = 'true';
                } elseif (false === $value || 'false' === $value) {
                    $valueOutput = 'false';
                } else {
                    $valueOutput = '""';
                }

                break;
            case PropertyHolder::TYPE_IN_ARRAY:
                try {
                    $valueOutput = json_encode($this->propertyHolder->checkValueInArray($property, (string) $value));
                } catch (\InvalidArgumentException $ex) {
                    $valueOutput = '""';
                }

                break;
            default:
                $valueOutput = json_encode($value);

                break;
        }

        if ('unknown' === $valueOutput || '"unknown"' === $valueOutput) {
            $valueOutput = '""';
        }

        return $valueOutput;
    }
}
