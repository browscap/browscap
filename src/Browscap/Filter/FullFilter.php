<?php
declare(strict_types = 1);
namespace Browscap\Filter;

use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;
use Browscap\Writer\WriterInterface;

/**
 * this filter is responsible to select properties and sections for the "full" version of the browscap files
 */
class FullFilter implements FilterInterface
{
    /**
     * @var PropertyHolder
     */
    private $propertyHolder;

    /**
     * @param PropertyHolder $propertyHolder
     */
    public function __construct(PropertyHolder $propertyHolder)
    {
        $this->propertyHolder = $propertyHolder;
    }

    /**
     * returns the Type of the filter
     *
     * @return string
     */
    public function getType() : string
    {
        return FilterInterface::TYPE_FULL;
    }

    /**
     * checks if a division should be in the output
     *
     * @param Division $division
     *
     * @return bool
     */
    public function isOutput(Division $division) : bool
    {
        return true;
    }

    /**
     * checks if a section should be in the output
     *
     * @param bool[] $section
     *
     * @return bool
     */
    public function isOutputSection(array $section) : bool
    {
        return true;
    }

    /**
     * checks if a property should be in the output
     *
     * @param string          $property
     * @param WriterInterface $writer
     *
     * @return bool
     */
    public function isOutputProperty(string $property, WriterInterface $writer) : bool
    {
        return $this->propertyHolder->isOutputProperty($property, $writer);
    }
}
