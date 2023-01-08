<?php

declare(strict_types=1);

namespace Browscap\Filter;

use Browscap\Data\Division;
use Browscap\Writer\WriterInterface;

interface FilterInterface
{
    public const TYPE_FULL       = 'FULL';
    public const TYPE_STANDARD   = '';
    public const TYPE_LITE       = 'LITE';
    public const TYPE_CUSTOM     = 'CUSTOM';
    public const TYPE_COLLECTION = 'COLLECTION';

    /**
     * returns the Type of the filter
     *
     * @throws void
     */
    public function getType(): string;

    /**
     * checks if a division should be in the output
     *
     * @throws void
     */
    public function isOutput(Division $division): bool;

    /**
     * checks if a section should be in the output
     *
     * @param bool[] $section
     *
     * @throws void
     */
    public function isOutputSection(array $section): bool;

    /**
     * checks if a property should be in the output
     *
     * @throws void
     */
    public function isOutputProperty(string $property, WriterInterface $writer): bool;
}
