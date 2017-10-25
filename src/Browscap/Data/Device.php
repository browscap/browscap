<?php
declare(strict_types = 1);
namespace Browscap\Data;

/**
 * Represents a device as defined in the resources/devices directory
 */
class Device
{
    /**
     * @var string[]
     */
    private $properties = [];

    /**
     * @var bool
     */
    private $standard = false;

    /**
     * @param string[] $properties
     * @param bool     $standard
     */
    public function __construct(array $properties, bool $standard)
    {
        $this->properties = $properties;
        $this->standard   = $standard;
    }

    /**
     * @return string[]
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

    public function isStandard() : bool
    {
        return $this->standard;
    }
}
