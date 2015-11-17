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
 * Class Division
 *
 * @category   Browscap
 * @package    Data
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class Division
{
    /**
     * @var string
     */
    private $name = null;

    /**
     * @var integer
     */
    private $sortIndex = 0;

    /**
     * @var boolean
     */
    private $lite = false;

    /**
     * @var boolean
     */
    private $standard = false;

    /**
     * @var array
     */
    private $versions = array();

    /**
     * @var array
     */
    private $userAgents = array();

    /**
     * @param string  $name
     * @param integer $sortIndex
     * @param array   $userAgents
     * @param boolean $lite
     * @param boolean $standard
     * @param array   $versions
     */
    public function __construct($name, $sortIndex, array $userAgents, $lite, $standard = true, array $versions = array())
    {
        $this->name       = $name;
        $this->sortIndex  = $sortIndex;
        $this->userAgents = $userAgents;
        $this->lite       = $lite;
        $this->standard   = $standard;
        $this->versions   = $versions;
    }

    /**
     * @return boolean
     */
    public function isLite()
    {
        return $this->lite;
    }

    /**
     * @return boolean
     */
    public function isStandard()
    {
        return $this->standard;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSortIndex()
    {
        return $this->sortIndex;
    }

    /**
     * @return array
     */
    public function getUserAgents()
    {
        return $this->userAgents;
    }

    /**
     * @return array
     */
    public function getVersions()
    {
        return $this->versions;
    }
}
