<?php
declare(strict_types = 1);
namespace Browscap\Data;

/**
 * Represents a device as defined in the resources/devices directory
 */
class Device
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var bool
     */
    private $standard = false;

    /**
     * @param array  $properties
     * @param string $type
     * @param bool   $standard
     */
    public function __construct(array $properties, string $type, bool $standard)
    {
        $this->type       = $type;
        $this->properties = $properties;
        $this->standard   = $standard;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @return array
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
