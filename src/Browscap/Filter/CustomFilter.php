<?php

declare(strict_types=1);

namespace Browscap\Filter;

use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;
use Browscap\Writer\WriterInterface;

use function in_array;

/**
 * with this filter it is possible to create a custom browscap file
 */
class CustomFilter implements FilterInterface
{
    /**
     * @param array<string> $fields
     *
     * @throws void
     */
    public function __construct(private PropertyHolder $propertyHolder, private array $fields = [])
    {
    }

    /**
     * returns the Type of the filter
     *
     * @throws void
     */
    public function getType(): string
    {
        return FilterInterface::TYPE_CUSTOM;
    }

    /**
     * checks if a division should be in the output
     *
     * @throws void
     */
    public function isOutput(Division $division): bool
    {
        return true;
    }

    /**
     * checks if a section should be in the output
     *
     * @param bool[] $section
     *
     * @throws void
     */
    public function isOutputSection(array $section): bool
    {
        return true;
    }

    /**
     * checks if a property should be in the output
     *
     * @throws void
     */
    public function isOutputProperty(string $property, WriterInterface $writer): bool
    {
        if (! $this->propertyHolder->isOutputProperty($property, $writer)) {
            return false;
        }

        return in_array($property, $this->fields);
    }
}
