<?php
/**
 * Created by PhpStorm.
 * User: Thomas Müller2
 * Date: 29.06.14
 * Time: 00:01
 */

namespace Browscap\Formatter;


class PhpFormatter implements FormatterInterface
{
    /**
     * returns the Type of the formatter
     *
     * @return string
     */
    public function getType()
    {
        return 'PHP';
    }
}