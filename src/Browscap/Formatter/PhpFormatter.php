<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   Browscap
 * @package    Formatter
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Formatter;

use Browscap\Data\PropertyHolder;
use Browscap\Filter\FilterInterface;

/**
 * Class PhpFormatter
 *
 * @category   Browscap
 * @package    Formatter
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class PhpFormatter implements FormatterInterface
{
    /**
     * @var \Browscap\Filter\FilterInterface
     */
    private $filter = null;

    /**
     * returns the Type of the formatter
     *
     * @return string
     */
    public function getType()
    {
        return 'php';
    }

    /**
     * formats the name of a property
     *
     * @param string $name
     *
     * @return string
     */
    public function formatPropertyName($name)
    {
        return $name;
    }

    /**
     * formats the name of a property
     *
     * @param string $value
     * @param string $property
     *
     * @return string
     */
    public function formatPropertyValue($value, $property)
    {
        $valueOutput    = $value;
        $propertyHolder = new PropertyHolder();

        switch ($propertyHolder->getPropertyType($property)) {
            case PropertyHolder::TYPE_STRING:
                $valueOutput = '"' . $value . '"';
                break;
            case PropertyHolder::TYPE_BOOLEAN:
                if (true === $value || $value === 'true') {
                    $valueOutput = '"true"';
                } elseif (false === $value || $value === 'false') {
                    $valueOutput = '"false"';
                } else {
                    $valueOutput = '';
                }
                break;
            case PropertyHolder::TYPE_IN_ARRAY:
                try {
                    $valueOutput = '"' . $propertyHolder->checkValueInArray($property, $value) . '"';
                } catch (\InvalidArgumentException $ex) {
                    $valueOutput = '';
                }
                break;
            default:
                if (preg_match('/[^a-zA-Z0-9]/', $valueOutput)) {
                    $valueOutput = '"' . $valueOutput . '"';
                }
                // nothing t do here
                break;
        }

        return $valueOutput;
    }

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     *
     * @return \Browscap\Formatter\FormatterInterface
     */
    public function setFilter(FilterInterface $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return \Browscap\Filter\FilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }
}
