<?php

declare(strict_types=1);

namespace Browscap\Filter\Property;

use Browscap\Data\PropertyHolder;
use Browscap\Writer\WriterInterface;

/**
 * this filter is responsible to select properties for the "lite" version of the browscap files
 */
trait LitePropertyFilter
{
    /** @throws void */
    public function __construct(private PropertyHolder $propertyHolder)
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

        return $this->propertyHolder->isLiteModeProperty($property, $writer);
    }
}
