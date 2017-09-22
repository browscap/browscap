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
class SplitVersion
{
    /**
     * splits a version into the major and the minor version
     *
     * @param string $version
     *
     * @return string[]
     */
    public function getVersionParts(string $version) : array
    {
        $dots = explode('.', $version, 2);

        $majorVer = $dots[0];
        $minorVer = (isset($dots[1]) ? $dots[1] : '0');

        return [$majorVer, $minorVer];
    }
}
