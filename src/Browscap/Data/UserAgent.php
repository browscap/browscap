<?php

declare(strict_types=1);

namespace Browscap\Data;

/**
 * Represents one useragent section inside of a division as defined in the resources/user-agents directory
 */
class UserAgent
{
    private string $userAgent = '';

    /** @var array<string> */
    private array $properties = [];

    /** @var mixed[] */
    private array $children = [];

    private ?string $platform = null;

    private ?string $engine = null;

    private ?string $device = null;

    private ?string $browser = null;

    /**
     * @param array<string> $properties
     * @param mixed[]       $children
     */
    public function __construct(
        string $userAgent,
        array $properties,
        array $children = [],
        ?string $platform = null,
        ?string $engine = null,
        ?string $device = null,
        ?string $browser = null
    ) {
        $this->userAgent  = $userAgent;
        $this->properties = $properties;
        $this->children   = $children;
        $this->platform   = $platform;
        $this->engine     = $engine;
        $this->device     = $device;
        $this->browser    = $browser;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @return array<string>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return mixed[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function getEngine(): ?string
    {
        return $this->engine;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }
}
