<?php

declare(strict_types=1);

namespace Browscap\Data;

class Browser
{
    /** @var string */
    private $type;

    /** @var array<string> */
    private $properties = [];

    /** @var bool */
    private $isLite = false;

    /** @var bool */
    private $isStandard = false;

    /**
     * @param array<string> $properties
     */
    public function __construct(array $properties, string $type, bool $isLite, bool $standard)
    {
        $this->type       = $type;
        $this->properties = $properties;
        $this->isLite     = $isLite;
        $this->isStandard = $standard;
    }

    public function getType(): string
    {
        return $this->type;
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
