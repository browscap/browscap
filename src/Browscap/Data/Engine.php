<?php

declare(strict_types=1);

namespace Browscap\Data;

/**
 * Represents an engine as defined in the resources/engines directory
 */
class Engine
{
    /** @var array<string> */
    private array $properties = [];

    /**
     * @param array<string> $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return array<string>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
