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

/**
 * Interface ParserInterface
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 */
interface ParserInterface
{
    /**
     * @return array
     */
    public function parse();

    /**
     * @return array
     */
    public function getParsed();

    /**
     * @return string
     */
    public function getFilename();
}
