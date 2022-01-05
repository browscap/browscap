<?php

declare(strict_types=1);

namespace Browscap\Data;

/**
 * Represents an engine as defined in the resources/engines directory
 *
 * @phpstan-type EngineProperties array{RenderingEngine_Name?: string, RenderingEngine_Version?: string, RenderingEngine_Description?: string, RenderingEngine_Maker?: string, VBScript?: bool, JavaApplets?: bool, ActiveXControls?: bool, BackgroundSounds?: bool, Frames?: bool, IFrames?: bool, Tables?: bool, Cookies?: bool, JavaScript?: bool, CssVersion?: int}
 * @phpstan-type EngineData array{properties?: EngineProperties, inherits?: string}
 */
class Engine
{
    /**
     * @var array<string, string|int|bool>
     * @phpstan-var EngineProperties
     */
    private array $properties = [];

    /**
     * @param array<string, string|bool|int> $properties
     * @phpstan-param EngineProperties $properties
     *
     * @throws void
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return array<string, string|bool|int>
     * @phpstan-return EngineProperties
     *
     * @throws void
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
