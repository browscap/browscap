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
use Browscap\Writer\WriterInterface;

/**
 * Interface FilterInterface
 *
 * @category   Browscap
 * @package    Filter
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */

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
     * @param \Browscap\Writer\WriterInterface $writer
     *
     * @return boolean
     */
    public function isOutputProperty($property, WriterInterface $writer = null);
}
