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
     * @var array
     */
    private $versions = array();

    /**
     * @var array
     */
    private $userAgents = array();

    /**
     * @return boolean
     */
    public function isLite()
    {
        return $this->lite;
    }

    /**
     * @param boolean $lite
     *
     * @return \Browscap\Data\Division
     */
    public function setLite($lite)
    {
        $this->lite = $lite;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return \Browscap\Data\Division
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getSortIndex()
    {
        return $this->sortIndex;
    }

    /**
     * @param int $sortIndex
     *
     * @return \Browscap\Data\Division
     */
    public function setSortIndex($sortIndex)
    {
        $this->sortIndex = $sortIndex;

        return $this;
    }

    /**
     * @return array
     */
    public function getUserAgents()
    {
        return $this->userAgents;
    }

    /**
     * @param array $userAgents
     *
     * @return \Browscap\Data\Division
     */
    public function setUserAgents($userAgents)
    {
        $this->userAgents = $userAgents;

        return $this;
    }

    /**
     * @return array
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @param array $versions
     *
     * @return \Browscap\Data\Division
     */
    public function setVersions($versions)
    {
        $this->versions = $versions;

        return $this;
    }
}
