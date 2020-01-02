<?php
declare(strict_types = 1);
namespace Browscap\Data;

/**
 * Represents an engine as defined in the resources/engines directory
 */
class Engine
{
    /**
     * @var string[]
     */
    private $properties = [];

    /**
     * @param string[] $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return string[]
     */
    public function getProperties() : array
    {
        return $this->properties;
    }
}
