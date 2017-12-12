<?php
declare(strict_types = 1);
namespace Browscap\Formatter;

use Browscap\Data\PropertyHolder;

/**
 * this formatter is responsible to format the output into the "php" ini version of the browscap files
 */
class PhpFormatter implements FormatterInterface
{
    /**
     * @var PropertyHolder
     */
    private $propertyHolder;

    /**
     * @param PropertyHolder $propertyHolder
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
        return FormatterInterface::TYPE_PHP;
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
        return $name;
    }

    /**
     * formats the name of a property
     *
     * @param bool|int|string $value
     * @param string          $property
     *
     * @throws \Exception
     *
     * @return string
     */
    public function formatPropertyValue($value, string $property) : string
    {
        $valueOutput = (string) $value;

        switch ($this->propertyHolder->getPropertyType($property)) {
            case PropertyHolder::TYPE_STRING:
                $valueOutput = '"' . trim((string) $value) . '"';

                break;
            case PropertyHolder::TYPE_BOOLEAN:
                if (true === $value || 'true' === $value) {
                    $valueOutput = '"true"';
                } elseif (false === $value || 'false' === $value) {
                    $valueOutput = '"false"';
                } else {
                    $valueOutput = '';
                }

                break;
            case PropertyHolder::TYPE_IN_ARRAY:
                try {
                    $valueOutput = '"' . $this->propertyHolder->checkValueInArray($property, (string) $value) . '"';
                } catch (\InvalidArgumentException $ex) {
                    $valueOutput = '';
                }

                break;
            default:
                if (preg_match('/[^a-zA-Z0-9]/', $valueOutput)) {
                    $valueOutput = '"' . $valueOutput . '"';
                }
                // nothing t do here
                break;
        }

        return $valueOutput;
    }
}
