<?php

declare(strict_types=1);

namespace Browscap\Filter;

/**
 * this filter is responsible to select properties and sections for the "lite" version of the browscap files
 */
class LiteFilter implements FilterInterface
{
    use Division\LiteDivisionFilter;
    use Property\LitePropertyFilter;
    use Section\LiteSectionFilter;

    /**
     * returns the Type of the filter
     *
     * @throws void
     */
    public function getType(): string
    {
        return FilterInterface::TYPE_LITE;
    }
}
