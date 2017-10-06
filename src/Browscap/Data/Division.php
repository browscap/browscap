<?php
declare(strict_types = 1);
namespace Browscap\Data;

/**
 * represents a useragent as defined in the resources/user-agents directory
 */
class Division
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $fileName = '';

    /**
     * @var int
     */
    private $sortIndex = 0;

    /**
     * @var bool
     */
    private $lite = false;

    /**
     * @var bool
     */
    private $standard = false;

    /**
     * @var array
     */
    private $versions = [];

    /**
     * @var UserAgent[]
     */
    private $userAgents = [];

    /**
     * @param string      $name
     * @param int         $sortIndex
     * @param UserAgent[] $userAgents
     * @param bool        $lite
     * @param bool        $standard
     * @param array       $versions
     * @param string      $fileName
     */
    public function __construct(
        string $name,
        int $sortIndex,
        array $userAgents,
        bool $lite,
        bool $standard,
        array $versions,
        string $fileName
    ) {
        $this->name       = $name;
        $this->sortIndex  = $sortIndex;
        $this->userAgents = $userAgents;
        $this->lite       = $lite;
        $this->standard   = $standard;
        $this->versions   = $versions;
        $this->fileName   = $fileName;
    }

    /**
     * @return bool
     */
    public function isLite() : bool
    {
        return $this->lite;
    }

    /**
     * @return bool
     */
    public function isStandard() : bool
    {
        return $this->standard;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSortIndex() : int
    {
        return $this->sortIndex;
    }

    /**
     * @return UserAgent[]
     */
    public function getUserAgents() : array
    {
        return $this->userAgents;
    }

    /**
     * @return array
     */
    public function getVersions() : array
    {
        return $this->versions;
    }

    /**
     * @return string|null
     */
    public function getFileName() : ?string
    {
        return $this->fileName;
    }
}
