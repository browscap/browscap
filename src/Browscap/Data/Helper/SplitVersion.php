<?php
declare(strict_types = 1);
namespace Browscap\Data\Helper;

/**
 * Class Expander
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
