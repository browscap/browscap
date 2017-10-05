<?php
declare(strict_types = 1);
namespace Browscap\Data;

/**
 * represents one useragent section inside of a division as defined in the resources/user-agents directory
 */
class UserAgent
{
    /**
     * @var string
     */
    private $userAgent = '';

    /**
     * @var string[]
     */
    private $properties = [];

    /**
     * @var array[]
     */
    private $children = [];

    /**
     * @var string|null
     */
    private $platform;

    /**
     * @var string|null
     */
    private $engine;

    /**
     * @var string|null
     */
    private $device;

    /**
     * @param string      $userAgent
     * @param string[]    $properties
     * @param array[]     $children
     * @param string|null $platform
     * @param string|null $engine
     * @param string|null $device
     */
    public function __construct(
        string $userAgent,
        array $properties,
        array $children = [],
        ?string $platform = null,
        ?string $engine = null,
        ?string $device = null
    ) {
        $this->userAgent  = $userAgent;
        $this->properties = $properties;
        $this->children   = $children;
        $this->platform   = $platform;
        $this->engine     = $engine;
        $this->device     = $device;
    }

    /**
     * @return string
     */
    public function getUserAgent() : string
    {
        return $this->userAgent;
    }

    /**
     * @return (string|bool)[]
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

    /**
     * @return array[]
     */
    public function getChildren() : array
    {
        return $this->children;
    }

    /**
     * @return string|null
     */
    public function getPlatform() : ?string
    {
        return $this->platform;
    }

    /**
     * @return string|null
     */
    public function getEngine() : ?string
    {
        return $this->engine;
    }

    /**
     * @return string|null
     */
    public function getDevice() : ?string
    {
        return $this->device;
    }
}
