<?php
declare(strict_types = 1);
namespace Browscap\Data;

class Browser
{
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
     * @param string[] $properties
     * @param bool     $isLite
     * @param bool     $standard
     */
    public function __construct(array $properties, bool $isLite, bool $standard)
    {
        $this->properties = $properties;
        $this->isLite     = $isLite;
        $this->isStandard = $standard;
    }

    /**
     * @return string[]
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
