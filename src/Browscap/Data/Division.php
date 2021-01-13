<?php

declare(strict_types=1);

namespace Browscap\Data;

/**
 * Represents a useragent division as defined in the resources/user-agents directory
 */
class Division
{
    /** @var string */
    private $name = '';

    /** @var string */
    private $fileName = '';

    /** @var int */
    private $sortIndex = 0;

    /** @var bool */
    private $lite = false;

    /** @var bool */
    private $standard = false;

    /** @var array<string> */
    private $versions = [];

    /** @var UserAgent[] */
    private $userAgents = [];

    /**
     * @param UserAgent[]   $userAgents
     * @param array<string> $versions
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
     * @return array<string>
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
