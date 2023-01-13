<?php

declare(strict_types=1);

namespace Browscap\Filter;

/**
 * with this filter it is possible to create a custom browscap file
 */
class CustomFilter implements FilterInterface
{
    use Division\FullDivisionFilter;
    use Property\FieldPropertyFilter;
    use Section\FullSectionFilter;

    /**
     * returns the Type of the filter
     *
     * @throws void
     */
    public function getType(): string
    {
        return FilterInterface::TYPE_CUSTOM;
    }
}
