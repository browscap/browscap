<?php
declare(strict_types = 1);
namespace Browscap\Generator;

interface GeneratorInterface
{
    /**
     * Entry point for generating builds for a specified version
     *
     * @param string $buildVersion
     * @param bool   $createZipFile
     */
    public function run(string $buildVersion, bool $createZipFile = true) : void;

    /**
     * Sets the flag to collect pattern ids during this build
     *
     * @param bool $value
     */
    public function setCollectPatternIds(bool $value) : void;
}
