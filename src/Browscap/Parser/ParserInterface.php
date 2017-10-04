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
namespace Browscap\Parser;

interface ParserInterface
{
    /**
     * @return array
     */
    public function parse() : array;

    /**
     * @return array
     */
    public function getParsed() : array;

    /**
     * @return string
     */
    public function getFilename() : string;
}
