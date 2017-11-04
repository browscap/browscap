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
namespace Browscap\Filter;

use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;
use Browscap\Writer\WriterInterface;

/**
 * Class StandardFilter
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class StandardFilter implements FilterInterface
{
    /**
     * @var \Browscap\Data\PropertyHolder
     */
    private $propertyHolder;

    /**
     * @param \Browscap\Data\PropertyHolder|null $propertyHolder
     */
    public function __construct(PropertyHolder $propertyHolder = null)
    {
        if (null === $propertyHolder) {
            $this->propertyHolder = new PropertyHolder();
        } else {
            $this->propertyHolder = $propertyHolder;
        }
    }

    /**
     * returns the Type of the filter
     *
     * @return string
     */
    public function getType(): string
    {
        return '';
    }

    /**
     * checks if a division should be in the output
     *
     * @param \Browscap\Data\Division $division
     *
     * @return bool
     */
    public function isOutput(Division $division): bool
    {
        return $division->isStandard();
    }

    /**
     * checks if a section should be in the output
     *
     * @param bool[] $section
     *
     * @return bool
     */
    public function isOutputSection(array $section): bool
    {
        return !isset($section['standard']) || $section['standard'];
    }

    /**
     * checks if a property should be in the output
     *
     * @param string                                $property
     * @param \Browscap\Writer\WriterInterface|null $writer
     *
     * @return bool
     */
    public function isOutputProperty(string $property, ?WriterInterface $writer = null): bool
    {
        if (!$this->propertyHolder->isOutputProperty($property, $writer)) {
            return false;
        }

        if ($this->propertyHolder->isLiteModeProperty($property, $writer)) {
            return true;
        }

        return $this->propertyHolder->isStandardModeProperty($property, $writer);
    }
}
