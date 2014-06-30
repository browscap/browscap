<?php
/**
 * Created by PhpStorm.
 * User: Thomas Müller2
 * Date: 29.06.14
 * Time: 00:02
 */

namespace Browscap\Formatter;

use Browscap\Filter\FilterInterface;

interface FormatterInterface
{
    /**
     * returns the Type of the formatter
     *
     * @return string
     */
    public function getType();
    
    /**
     * formats the name of a property
     *
     * @param string $name
     *
     * @return string
     */
    public function formatPropertyName($name);
    
    /**
     * formats the name of a property
     *
     * @param string $value
     * @param string $property
     *
     * @return string
     */
    public function formatPropertyValue($value, $property);

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     *
     * @return \Browscap\Formatter\FormatterInterface
     */
    public function setFilter(FilterInterface $filter);

    /**
     * @return \Browscap\Filter\FilterInterface
     */
    public function getFilter();
}