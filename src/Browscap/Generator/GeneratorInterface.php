<?php

declare(strict_types=1);

namespace Browscap\Generator;

use Assert\AssertionFailedException;
use DateTimeImmutable;
use Exception;

interface GeneratorInterface
{
    /**
     * Entry point for generating builds for a specified version
     *
     * @throws Exception
     * @throws AssertionFailedException
     */
    public function run(string $buildVersion, DateTimeImmutable $generationDate, bool $createZipFile = true): void;

    /**
     * Sets the flag to collect pattern ids during this build
     *
     * @throws void
     */
    public function setCollectPatternIds(bool $value): void;
}
