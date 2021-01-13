<?php

declare(strict_types=1);

namespace Browscap\Data\Helper;

use function explode;

class SplitVersion
{
    /**
     * splits a version into the major and the minor version
     *
     * @return array<string>
     */
    public function getVersionParts(string $version): array
    {
        $dots = explode('.', $version, 2);

        $majorVer = $dots[0];
        $minorVer = ($dots[1] ?? '0');

        return [$majorVer, $minorVer];
    }
}
