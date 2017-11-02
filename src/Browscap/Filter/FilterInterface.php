<?php
declare(strict_types = 1);
namespace Browscap\Filter;

use Browscap\Data\Division;
use Browscap\Writer\WriterInterface;

interface FilterInterface
{
    public const TYPE_FULL     = 'FULL';
    public const TYPE_STANDARD = '';
    public const TYPE_LITE     = 'LITE';
    public const TYPE_CUSTOM   = 'CUSTOM';

    /**
     * returns the Type of the filter
     *
     * @return string
     */
    public function getType() : string;

    /**
     * checks if a division should be in the output
     *
     * @param Division $division
     *
     * @return bool
     */
    public function isOutput(Division $division) : bool;

    /**
     * checks if a section should be in the output
     *
     * @param bool[] $section
     *
     * @return bool
     */
    public function isOutputSection(array $section) : bool;

    /**
     * checks if a property should be in the output
     *
     * @param string          $property
     * @param WriterInterface $writer
     *
     * @return bool
     */
    public function isOutputProperty(string $property, WriterInterface $writer) : bool;
}
