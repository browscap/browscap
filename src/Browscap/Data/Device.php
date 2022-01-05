<?php

declare(strict_types=1);

namespace Browscap\Data;

/**
 * Represents a device as defined in the resources/devices directory
 *
 * @phpstan-type DeviceType 'car-entertainment-system'|'console'|'desktop'|'digital-camera'|'ebook-reader'|'feature-phone'|'fone-pad'|'mobile-console'|'mobile-device'|'mobile-phone'|'smartphone'|'tablet'|'tv'|'tv-console'|'unknown'
 * @phpstan-type DeviceProperties array{Device_Name?: string, Device_Code_Name?: string, Device_Maker?: string, Device_Pointing_Method?: string, Device_Brand_Name?: string}
 * @phpstan-type DeviceData array{properties: DeviceProperties, standard: bool, type: DeviceType}
 */
class Device
{
    private string $type;

    /** @var array<string, string> */
    private array $properties = [];

    private bool $standard = false;

    /**
     * @param array<string, string> $properties
     *
     * @throws void
     */
    public function __construct(array $properties, string $type, bool $standard)
    {
        $this->type       = $type;
        $this->properties = $properties;
        $this->standard   = $standard;
    }

    /**
     * @throws void
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, string>
     *
     * @throws void
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @throws void
     */
    public function isStandard(): bool
    {
        return $this->standard;
    }
}
