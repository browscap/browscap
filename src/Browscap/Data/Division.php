<?php
/**
 * Created by PhpStorm.
 * User: Thomas Müller2
 * Date: 29.06.14
 * Time: 10:13
 */

namespace Browscap\Data;


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
     * @return boolean
     */
    public function getLite()
    {
        return $this->lite;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * @return int
     */
    public function getSortIndex()
    {
        return $this->sortIndex;
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
    public function getUserAgents()
    {
        return $this->userAgents;
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

    /**
     * @return array
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @return array
     */
    public function expand()
    {
        $sections = array();

        return $sections;
    }
}