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
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Data;

/**
 * Class Device
 *
 * @category   Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class Device
{
    /**
     * @var string[]
     */
    private $properties = [];

    /**
     * @var bool
     */
    private $standard = false;

    /**
     * @param string[] $properties
     * @param bool     $standard
     */
    public function __construct(array $properties, $standard)
    {
        $this->properties = $properties;
        $this->standard   = (bool) $standard;
    }

    /**
     * @return string[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return bool
     */
    public function isStandard()
    {
        return $this->standard;
    }
}
