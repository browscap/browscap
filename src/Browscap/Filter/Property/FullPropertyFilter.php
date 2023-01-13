<?php

declare(strict_types=1);

namespace Browscap\Filter\Property;

use Browscap\Data\PropertyHolder;
use Browscap\Writer\WriterInterface;

/**
 * this filter is responsible to select properties for the "full" version of the browscap files
 */
trait FullPropertyFilter
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
        return $this->propertyHolder->isOutputProperty($property, $writer);
    }
}
