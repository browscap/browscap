<?php

declare(strict_types=1);

namespace Browscap\Filter\Section;

/**
 * this filter is responsible to select properties and sections for the "full" version of the browscap files
 */
trait FullSectionFilter
{
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
}
