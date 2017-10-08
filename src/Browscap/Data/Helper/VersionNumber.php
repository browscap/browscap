<?php
declare(strict_types = 1);
namespace Browscap\Data\Helper;

class VersionNumber
{
    /**
     * Render the property of a single User Agent and replaces version placeholders
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
