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
 * Class Device
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
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
    public function __construct(array $properties, bool $standard)
    {
        $this->properties = $properties;
        $this->standard   = $standard;
    }

    /**
     * @return string[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return bool
     */
    public function isStandard(): bool
    {
        return $this->standard;
    }
}
