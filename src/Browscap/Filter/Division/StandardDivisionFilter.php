<?php

declare(strict_types=1);

namespace Browscap\Filter\Division;

use Browscap\Data\Division as DataDivision;

/**
 * this filter is responsible to select divisions for the "standard" version of the browscap files
 */
trait StandardDivisionFilter
{
    /**
     * checks if a division should be in the output
     *
     * @throws void
     */
    public function isOutput(DataDivision $division): bool
    {
        return $division->isStandard();
    }
}
