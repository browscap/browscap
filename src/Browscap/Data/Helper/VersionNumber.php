<?php
declare(strict_types = 1);
namespace Browscap\Data\Helper;

/**
 * Class Expander
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
