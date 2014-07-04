<?php
/**
 * Created by PhpStorm.
 * User: Thomas Müller2
 * Date: 29.06.14
 * Time: 00:13
 */

namespace Browscap\Filter;


use Browscap\Data\Division;

interface FilterInterface
{
    /**
     * returns the Type of the filter
     *
     * @return string
     */
    public function getType();

    /**
     * checks if a division should be in the output
     *
     * @param \Browscap\Data\Division $division
     *
     * @return boolean
     */
    public function isOutput(Division $division);

    /**
     * checks if a property should be in the output
     *
     * @param string $property
     *
     * @return boolean
     */
    public function isOutputProperty($property);
}
