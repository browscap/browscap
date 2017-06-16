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
