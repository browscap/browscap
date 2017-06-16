<?php

declare(strict_types=1);

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
