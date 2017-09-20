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

use Browscap\Data\PropertyHolder;
use Browscap\Filter\FilterInterface;

/**
 * Class CsvFormatter
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class CsvFormatter implements FormatterInterface
{
    /**
     * @var \Browscap\Filter\FilterInterface
     */
    private $filter;

    /**
     * returns the Type of the formatter
     *
     * @return string
     */
    public function getType()
    {
        return 'csv';
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
        return '"' . str_replace('"', '""', $name) . '"';
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
                if (true === $value || 'true' === $value) {
                    $valueOutput = 'true';
                } elseif (false === $value || 'false' === $value) {
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

        if ('unknown' === $valueOutput) {
            $valueOutput = '';
        }

        return '"' . str_replace('"', '""', $valueOutput) . '"';
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
