<?php

declare(strict_types=1);

namespace Browscap\Filter;

/**
 * this filter is responsible to select properties and sections for the "full" version of the browscap files
 */
class FullFilter implements FilterInterface
{
    use Division\FullDivisionFilter;
    use Property\FullPropertyFilter;
    use Section\FullSectionFilter;

    /**
     * returns the Type of the filter
     *
     * @throws void
     */
    public function getType(): string
    {
        return FilterInterface::TYPE_FULL;
    }
}
