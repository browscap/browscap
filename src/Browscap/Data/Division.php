<?php

declare(strict_types=1);

/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Browscap\Data;

/**
 * Class Division
 *
 * @category   Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class Division
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $fileName = '';

    /**
     * @var int
     */
    private $sortIndex = 0;

    /**
     * @var bool
     */
    private $lite = false;

    /**
     * @var bool
     */
    private $standard = false;

    /**
     * @var array
     */
    private $versions = [];

    /**
     * @var array
     */
    private $userAgents = [];

    /**
     * @param string $name
     * @param int    $sortIndex
     * @param array  $userAgents
     * @param bool   $lite
     * @param bool   $standard
     * @param array  $versions
     * @param string $fileName
     */
    public function __construct(
        string $name,
        int $sortIndex,
        array $userAgents,
        bool $lite,
        bool $standard = true,
        array $versions = [],
        string $fileName = null
    ) {
        $this->name       = $name;
        $this->sortIndex  = $sortIndex;
        $this->userAgents = $userAgents;
        $this->lite       = $lite;
        $this->standard   = $standard;
        $this->versions   = $versions;
        $this->fileName   = $fileName;
    }

    /**
     * @return bool
     */
    public function isLite() : bool
    {
        return $this->lite;
    }

    /**
     * @return bool
     */
    public function isStandard() : bool
    {
        return $this->standard;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSortIndex() : int
    {
        return $this->sortIndex;
    }

    /**
     * @return array
     */
    public function getUserAgents() : array
    {
        return $this->userAgents;
    }

    /**
     * @return array
     */
    public function getVersions() : array
    {
        return $this->versions;
    }

    /**
     * @return string
     */
    public function getFileName() : string
    {
        return $this->fileName;
    }
}
