<?php
declare(strict_types = 1);
namespace Browscap\Data;

/**
 * Class Platform
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class Platform
{
    /**
     * @var string|null
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
     * @param string   $match
     * @param string[] $properties
     * @param bool     $isLite
     * @param bool     $standard
     */
    public function __construct(string $match, array $properties, bool $isLite, bool $standard)
    {
        $this->match      = $match;
        $this->properties = $properties;
        $this->isLite     = $isLite;
        $this->isStandard = $standard;
    }

    /**
     * @return string|null
     */
    public function getMatch() : ?string
    {
        return $this->match;
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
