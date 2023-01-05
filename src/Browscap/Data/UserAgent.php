<?php

declare(strict_types=1);

namespace Browscap\Data;

/**
 * Represents one useragent section inside of a division as defined in the resources/user-agents directory
 *
 * @phpstan-type UserAgentChild array{match: string, device?: string, platforms?: array<int, string>, devices?: array<string, string>}
 * @phpstan-type UserAgentProperties array{Parent: string, Comment: string, Version?: string, PatternId?: string}
 * @phpstan-type UserAgentData array{userAgent: string, browser?: string, engine?: string, device?: string, platform?: string, properties: UserAgentProperties, children?: array<int, UserAgentChild>}
 */
class UserAgent
{
    /** @phpcsSuppress SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion.RequiredConstructorPropertyPromotion */
    private string $userAgent = '';

    /**
     * @var array<string>
     * @phpstan-var UserAgentProperties
     */
    private array $properties;

    /**
     * @var mixed[]
     * @phpstan-var array<UserAgentChild>
     */
    private array $children = [];

    /** @phpcsSuppress SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion.RequiredConstructorPropertyPromotion */
    private string|null $platform = null;

    /** @phpcsSuppress SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion.RequiredConstructorPropertyPromotion */
    private string|null $engine = null;

    /** @phpcsSuppress SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion.RequiredConstructorPropertyPromotion */
    private string|null $device = null;

    /** @phpcsSuppress SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion.RequiredConstructorPropertyPromotion */
    private string|null $browser = null;

    /**
     * @param array<string> $properties
     * @param array<mixed>  $children
     * @phpstan-param UserAgentProperties $properties
     * @phpstan-param array<UserAgentChild> $children
     *
     * @throws void
     */
    public function __construct(
        string $userAgent,
        array $properties,
        array $children = [],
        string|null $platform = null,
        string|null $engine = null,
        string|null $device = null,
        string|null $browser = null,
    ) {
        $this->userAgent  = $userAgent;
        $this->properties = $properties;
        $this->children   = $children;
        $this->platform   = $platform;
        $this->engine     = $engine;
        $this->device     = $device;
        $this->browser    = $browser;
    }

    /** @throws void */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @return array<string>
     * @phpstan-return UserAgentProperties
     *
     * @throws void
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array<mixed>
     * @phpstan-return array<UserAgentChild>
     *
     * @throws void
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /** @throws void */
    public function getPlatform(): string|null
    {
        return $this->platform;
    }

    /** @throws void */
    public function getEngine(): string|null
    {
        return $this->engine;
    }

    /** @throws void */
    public function getDevice(): string|null
    {
        return $this->device;
    }

    /** @throws void */
    public function getBrowser(): string|null
    {
        return $this->browser;
    }
}
