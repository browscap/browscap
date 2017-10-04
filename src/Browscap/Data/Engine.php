<?php
declare(strict_types = 1);
namespace Browscap\Data;

/**
 * Class Engine
 *
 * @author     Thomas Müller <mimmi20@live.de>
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
