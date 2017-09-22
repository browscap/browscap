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
namespace Browscap\Data\Helper;

/**
 * Class Expander
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class VersionNumber
{
    /**
     * Render the property of a single User Agent
     *
     * @param string $value
     * @param string $majorVer
     * @param string $minorVer
     *
     * @return string
     */
    public function replace(string $value, string $majorVer, string $minorVer) : string
    {
        return str_replace(
            ['#MAJORVER#', '#MINORVER#'],
            [$majorVer, $minorVer],
            $value
        );
    }
}
