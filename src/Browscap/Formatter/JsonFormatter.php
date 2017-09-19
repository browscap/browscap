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
 * Class JsonFormatter
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class JsonFormatter implements FormatterInterface
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
    public function getType() : string
    {
        return 'json';
    }

    /**
     * formats the name of a property
     *
     * @param string $name
     *
     * @return string
     */
    public function formatPropertyName(string $name) : string
    {
        return json_encode($name);
    }

    /**
     * formats the name of a property
     *
     * @param mixed  $value
     * @param string $property
     *
     * @return string
     */
    public function formatPropertyValue($value, string $property) : string
    {
        $propertyHolder = new PropertyHolder();

        switch ($propertyHolder->getPropertyType($property)) {
            case PropertyHolder::TYPE_STRING:
                $valueOutput = json_encode(trim((string) $value));
                break;
            case PropertyHolder::TYPE_BOOLEAN:
                if (true === $value || $value === 'true') {
                    $valueOutput = 'true';
                } elseif (false === $value || $value === 'false') {
                    $valueOutput = 'false';
                } else {
                    $valueOutput = '""';
                }
                break;
            case PropertyHolder::TYPE_IN_ARRAY:
                try {
                    $valueOutput = json_encode($propertyHolder->checkValueInArray($property, $value));
                } catch (\InvalidArgumentException $ex) {
                    $valueOutput = '""';
                }
                break;
            default:
                $valueOutput = json_encode($value);
                break;
        }

        if ('unknown' === $valueOutput || '"unknown"' === $valueOutput) {
            $valueOutput = '""';
        }

        return $valueOutput;
    }

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     */
    public function setFilter(FilterInterface $filter) : void
    {
        $this->filter = $filter;
    }

    /**
     * @return \Browscap\Filter\FilterInterface
     */
    public function getFilter() : FilterInterface
    {
        return $this->filter;
    }
}
