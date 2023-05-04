<?php

declare(strict_types=1);

namespace Browscap\Data;

/**
 * @phpstan-type BrowserType 'application'|'bot'|'bot-syndication-reader'|'bot-trancoder'|'browser'|'email-client'|'feed-reader'|'library'|'multimedia-player'|'offline-browser'|'tool'|'transcoder'|'useragent-anonymizer'|'unknown'
 * @phpstan-type BrowserProperties array{Browser?: string, Browser_Maker?: string}
 * @phpstan-type BrowserData array{properties?: BrowserProperties, standard: bool, lite: bool, type: BrowserType}
 */
class Browser
{
    /** @phpcsSuppress SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion.RequiredConstructorPropertyPromotion */
    private bool $isLite     = false;
    private bool $isStandard = false;

    /**
     * @param array<string, string> $properties
     * @phpstan-param BrowserProperties $properties
     * @phpstan-param BrowserType $type
     *
     * @throws void
     */
    public function __construct(private readonly array $properties, private readonly string $type, bool $isLite, bool $standard)
    {
        $this->isLite     = $isLite;
        $this->isStandard = $standard;
    }

    /**
     * @phpstan-return BrowserType
     *
     * @throws void
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, string>
     * @phpstan-return BrowserProperties
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
