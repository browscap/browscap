<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   Browscap
 * @copyright  1998-2017 Browser Capabilities Project
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
