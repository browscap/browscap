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
 * @package    Data
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Data;

/**
 * Class Platform
 *
 * @category   Browscap
 * @package    Data
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
    private $properties = array();

    /**
     * @var bool
     */
    private $isLite = false;

    /**
     * @return string
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @param string $match
     *
     * @return \Browscap\Data\Platform
     */
    public function setMatch($match)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param string[] $properties
     *
     * @return \Browscap\Data\Platform
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLite()
    {
        return $this->isLite;
    }

    /**
     * @param bool $isLite
     * @return \Browscap\Data\Platform
     */
    public function setIsLite($isLite)
    {
        $this->isLite = (bool)$isLite;
        return $this;
    }
}
