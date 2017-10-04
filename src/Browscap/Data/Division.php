<?php
declare(strict_types = 1);
namespace Browscap\Data;

/**
 * Class Division
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class Division
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string|null
     */
    private $fileName;

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
     * @var \Browscap\Data\Useragent[]
     */
    private $userAgents = [];

    /**
     * @param string                     $name
     * @param int                        $sortIndex
     * @param \Browscap\Data\Useragent[] $userAgents
     * @param bool                       $lite
     * @param bool                       $standard
     * @param array                      $versions
     * @param string|null                $fileName
     */
    public function __construct(
        string $name,
        int $sortIndex,
        array $userAgents,
        bool $lite,
        bool $standard = true,
        array $versions = [],
        ?string $fileName = null
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
     * @return \Browscap\Data\Useragent[]
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
