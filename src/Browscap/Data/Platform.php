<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace Browscap\Data;

/**
 * Class Platform
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
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
