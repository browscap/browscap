<?php
declare(strict_types = 1);
namespace Browscap\Data;

/**
 * Represents a platform as defined in the resources/platforms.json file
 */
class Platform
{
    /**
     * @var string
     */
    private $match;

    /**
     * @var string[]
     */
    private $properties = [];

    /**
     * @var bool
     */
    private $isLite = false;

    /**
     * @var bool
     */
    private $isStandard = false;

    /**
     * @param string $match
     * @param array  $properties
     * @param bool   $isLite
     * @param bool   $standard
     */
    public function __construct(string $match, array $properties, bool $isLite, bool $standard)
    {
        $this->match      = $match;
        $this->properties = $properties;
        $this->isLite     = $isLite;
        $this->isStandard = $standard;
    }

    public function getMatch() : string
    {
        return $this->match;
    }

    /**
     * @return array
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

    public function isLite() : bool
    {
        return $this->isLite;
    }

    public function isStandard() : bool
    {
        return $this->isStandard;
    }
}
