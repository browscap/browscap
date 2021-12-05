<?php

declare(strict_types=1);

namespace Browscap\Data;

/**
 * Represents a useragent division as defined in the resources/user-agents directory
 */
class Division
{
    private string $name = '';

    private string $fileName = '';

    private int $sortIndex = 0;

    private bool $lite = false;

    private bool $standard = false;

    /** @var array<int, int|string> */
    private array $versions = [];

    /** @var UserAgent[] */
    private array $userAgents = [];

    /**
     * @param UserAgent[]            $userAgents
     * @param array<int, int|string> $versions
     */
    public function __construct(
        string $name,
        int $sortIndex,
        array $userAgents,
        bool $lite,
        bool $standard,
        array $versions,
        string $fileName
    ) {
        $this->name       = $name;
        $this->sortIndex  = $sortIndex;
        $this->userAgents = $userAgents;
        $this->lite       = $lite;
        $this->standard   = $standard;
        $this->versions   = $versions;
        $this->fileName   = $fileName;
    }

    public function isLite(): bool
    {
        return $this->lite;
    }

    public function isStandard(): bool
    {
        return $this->standard;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSortIndex(): int
    {
        return $this->sortIndex;
    }

    /**
     * @return UserAgent[]
     */
    public function getUserAgents(): array
    {
        return $this->userAgents;
    }

    /**
     * @return array<int, int|string>
     */
    public function getVersions(): array
    {
        return $this->versions;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }
}
