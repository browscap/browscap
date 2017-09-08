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
namespace Browscap\Formatter;

use Browscap\Filter\FilterInterface;

/**
 * Interface FormatterInterface
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
interface FormatterInterface
{
    /**
     * returns the Type of the formatter
     *
     * @return string
     */
    public function getType() : string;

    /**
     * formats the name of a property
     *
     * @param string $name
     *
     * @return string
     */
    public function formatPropertyName(string $name) : string;

    /**
     * formats the name of a property
     *
     * @param mixed  $value
     * @param string $property
     *
     * @return string
     */
    public function formatPropertyValue($value, string $property) : string;

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     */
    public function setFilter(FilterInterface $filter) : void;

    /**
     * @return \Browscap\Filter\FilterInterface
     */
    public function getFilter() : FilterInterface;
}
