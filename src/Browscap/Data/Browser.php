<?php
declare(strict_types = 1);
namespace Browscap\Data;

class Browser
{
    /**
     * @var string
     */
    private $type;

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
     * @param array  $properties
     * @param string $type
     * @param bool   $isLite
     * @param bool   $standard
     */
    public function __construct(array $properties, string $type, bool $isLite, bool $standard)
    {
        $this->type       = $type;
        $this->properties = $properties;
        $this->isLite     = $isLite;
        $this->isStandard = $standard;
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

    /**
     * @return bool
     */
    public function isLite() : bool
    {
        return $this->isLite;
    }

    /**
     * @return bool
     */
    public function isStandard() : bool
    {
        return $this->isStandard;
    }
}
