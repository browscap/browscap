<?php
declare(strict_types = 1);
namespace Browscap\Filter;

use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;
use Browscap\Writer\WriterInterface;

/**
 * with this filter is possible to create a custom browscap file
 */
class CustomFilter implements FilterInterface
{
    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var string
     */
    private $outputType = FilterInterface::TYPE_FULL;

    /**
     * @var \Browscap\Data\PropertyHolder
     */
    private $propertyHolder;

    /**
     * @param PropertyHolder $propertyHolder
     * @param array          $fields
     * @param string         $outputType
     */
    public function __construct(PropertyHolder $propertyHolder, array $fields, $outputType = FilterInterface::TYPE_FULL)
    {
        $this->fields         = $fields;
        $this->outputType     = $outputType;
        $this->propertyHolder = $propertyHolder;
    }

    /**
     * returns the Type of the filter
     *
     * @return string
     */
    public function getType() : string
    {
        return FilterInterface::TYPE_CUSTOM;
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
        switch ($this->outputType) {
            case FilterInterface::TYPE_STANDARD:
                return $division->isStandard();

            case FilterInterface::TYPE_LITE:
                return $division->isLite();

            case FilterInterface::TYPE_FULL:
            default:
                return true;
        }
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
        switch ($this->outputType) {
            case FilterInterface::TYPE_STANDARD:
                return !isset($section['standard']) || $section['standard'];

            case FilterInterface::TYPE_LITE:
                return isset($section['lite']) && $section['lite'];

            case FilterInterface::TYPE_FULL:
            default:
                return true;
        }
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
        if (!$this->propertyHolder->isOutputProperty($property, $writer)) {
            return false;
        }

        return in_array($property, $this->fields);
    }
}
