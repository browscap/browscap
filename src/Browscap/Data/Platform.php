<?php

declare(strict_types=1);

namespace Browscap\Data;

/**
 * Represents a platform as defined in the resources/platforms directory
 *
 * @phpstan-type PlatformProperties array{Platform?: string, Platform_Maker?: string, Platform_Version?: string, Platform_Description?: string, Win16?: bool, Win32?: bool, Win64?: bool, Browser_Bits?: int, Platform_Bits?: int}
 * @phpstan-type PlatformData array{match: string, properties?: PlatformProperties, standard: bool, lite: bool}
 */
class Platform
{
    /** @phpcsSuppress SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion.RequiredConstructorPropertyPromotion */
    private string $match;

    /** @phpcsSuppress SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion.RequiredConstructorPropertyPromotion */
    private bool $isLite = false;

    private bool $isStandard = false;

    /**
     * @param array<string, string|int|bool> $properties
     * @phpstan-param PlatformProperties $properties
     *
     * @throws void
     */
    public function __construct(string $match, private readonly array $properties, bool $isLite, bool $standard)
    {
        $this->match      = $match;
        $this->isLite     = $isLite;
        $this->isStandard = $standard;
    }

    /** @throws void */
    public function getMatch(): string
    {
        return $this->match;
    }

    /**
     * @return array<string, string|int|bool>
     * @phpstan-return PlatformProperties
     *
     * @throws void
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /** @throws void */
    public function isLite(): bool
    {
        return $this->isLite;
    }

    /** @throws void */
    public function isStandard(): bool
    {
        return $this->isStandard;
    }
}
