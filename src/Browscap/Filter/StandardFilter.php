<?php

declare(strict_types=1);

namespace Browscap\Filter;

/**
 * this filter is responsible to select properties and sections for the "standard" version of the browscap files
 */
class StandardFilter implements FilterInterface
{
    use Division\StandardDivisionFilter;
    use Property\StandardPropertyFilter;
    use Section\StandardSectionFilter;

    /**
     * returns the Type of the filter
     *
     * @throws void
     */
    public function getType(): string
    {
        return FilterInterface::TYPE_STANDARD;
    }
}
