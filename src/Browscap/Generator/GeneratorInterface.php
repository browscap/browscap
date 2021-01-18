<?php

declare(strict_types=1);

namespace Browscap\Generator;

use DateTimeImmutable;

interface GeneratorInterface
{
    /**
     * Entry point for generating builds for a specified version
     */
    public function run(string $buildVersion, DateTimeImmutable $generationDate, bool $createZipFile = true): void;

    /**
     * Sets the flag to collect pattern ids during this build
     */
    public function setCollectPatternIds(bool $value): void;
}
