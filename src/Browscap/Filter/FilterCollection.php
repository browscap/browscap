<?php

declare(strict_types=1);

namespace Browscap\Filter;

use Browscap\Data\Division;
use Browscap\Writer\WriterInterface;

/**
 * with this filter it is possible to combine other filters
 */
class FilterCollection implements FilterInterface
{
    /** @var FilterInterface[] */
    private array $filters = [];

    /**
     * returns the Type of the filter
     *
     * @throws void
     */
    public function getType(): string
    {
        return FilterInterface::TYPE_COLLECTION;
    }

    /**
     * add a new filter to the collection
     *
     * @throws void
     */
    public function addFilter(FilterInterface $writer): void
    {
        $this->filters[] = $writer;
    }

    /**
     * checks if a division should be in the output
     */
    public function isOutput(Division $division): bool
    {
        foreach ($this->filters as $filter) {
            if (! $filter->isOutput($division)) {
                return false;
            }
        }

        return true;
    }

    /**
     * checks if a section should be in the output
     *
     * @param bool[] $section
     */
    public function isOutputSection(array $section): bool
    {
        foreach ($this->filters as $filter) {
            if (! $filter->isOutputSection($section)) {
                return false;
            }
        }

        return true;
    }

    /**
     * checks if a property should be in the output
     */
    public function isOutputProperty(string $property, WriterInterface $writer): bool
    {
        foreach ($this->filters as $filter) {
            if (! $filter->isOutputProperty($property, $writer)) {
                return false;
            }
        }

        return true;
    }
}
