<?php
/**
 * Created by PhpStorm.
 * User: Thomas Müller2
 * Date: 29.06.14
 * Time: 00:02
 */

namespace Browscap\Formatter;


interface FormatterInterface
{
    /**
     * returns the Type of the formatter
     *
     * @return string
     */
    public function getType();
}