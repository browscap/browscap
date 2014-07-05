<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   Browscap
 * @package    Filter
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Filter;

use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;

/**
 * Class FullFilter
 *
 * @category   Browscap
 * @package    Filter
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class FullFilter implements FilterInterface
{
    /**
     * returns the Type of the filter
     *
     * @return string
     */
    public function getType()
    {
        return 'FULL';
    }

    /**
     * checks if a division should be in the output
     *
     * @param \Browscap\Data\Division $division
     *
     * @return boolean
     */
    public function isOutput(Division $division)
    {
        return true;
    }

    /**
     * checks if a property should be in the output
     *
     * @param string $property
     *
     * @return boolean
     */
    public function isOutputProperty($property)
    {
        $propertyHolder = new PropertyHolder();

        if (!$propertyHolder->isOutputProperty($property)) {
            return false;
        }

        return true;
    }
}
