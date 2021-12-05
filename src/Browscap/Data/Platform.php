<?php

declare(strict_types=1);

namespace Browscap\Data;

/**
 * Represents a platform as defined in the resources/platforms directory
 */
class Platform
{
    private string $match;

    /** @var array<string> */
    private array $properties = [];

    private bool $isLite = false;

    private bool $isStandard = false;

    /**
     * @param array<string> $properties
     */
    public function __construct(string $match, array $properties, bool $isLite, bool $standard)
    {
        $this->match      = $match;
        $this->properties = $properties;
        $this->isLite     = $isLite;
        $this->isStandard = $standard;
    }

    public function getMatch(): string
    {
        return $this->match;
    }

    /**
     * @return array<string>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function isLite(): bool
    {
        return $this->isLite;
    }

    public function isStandard(): bool
    {
        return $this->isStandard;
    }
}
