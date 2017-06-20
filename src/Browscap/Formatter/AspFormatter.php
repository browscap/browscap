<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Browscap\Formatter;

use Browscap\Data\PropertyHolder;
use Browscap\Filter\FilterInterface;

/**
 * Class AspFormatter
 *
 * @category   Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class AspFormatter implements FormatterInterface
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
        return 'asp';
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
                $valueOutput = trim($value);
                break;
            case PropertyHolder::TYPE_BOOLEAN:
                if (true === $value || $value === 'true') {
                    $valueOutput = 'true';
                } elseif (false === $value || $value === 'false') {
                    $valueOutput = 'false';
                } else {
                    $valueOutput = '';
                }
                break;
            case PropertyHolder::TYPE_IN_ARRAY:
                try {
                    $valueOutput = $propertyHolder->checkValueInArray($property, $value);
                } catch (\InvalidArgumentException $ex) {
                    $valueOutput = '';
                }
                break;
            default:
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
