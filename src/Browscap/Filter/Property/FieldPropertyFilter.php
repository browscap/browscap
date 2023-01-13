<?php

declare(strict_types=1);

namespace Browscap\Filter\Property;

use Browscap\Data\PropertyHolder;
use Browscap\Writer\WriterInterface;

use function in_array;

/**
 * this filter is responsible to select properties and sections for the "full" version of the browscap files
 */
trait FieldPropertyFilter
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
