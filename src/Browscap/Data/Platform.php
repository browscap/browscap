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
 * Class Platform
 *
 * @category   Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class Platform
{
    /**
     * @var string
     */
    private $match = null;

    /**
     * @var string[]
     */
    private $properties = [];

    /**
     * @var bool
     */
    private $isLite = false;

    /**
     * @var bool
     */
    private $isStandard = false;

    /**
     * @param string   $match
     * @param string[] $properties
     * @param bool     $isLite
     * @param bool     $standard
     */
    public function __construct($match, array $properties, $isLite, $standard)
    {
        $this->match      = $match;
        $this->properties = $properties;
        $this->isLite     = (bool) $isLite;
        $this->isStandard = (bool) $standard;
    }

    /**
     * @return string
     */
    public function getMatch()
    {
        return $this->match;
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
    public function isLite()
    {
        return $this->isLite;
    }

    /**
     * @return bool
     */
    public function isStandard()
    {
        return $this->isStandard;
    }
}
