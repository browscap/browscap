<?php

declare(strict_types=1);

namespace Browscap\Coverage;

use JsonException;
use RuntimeException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

interface ProcessorInterface
{
    /**
     * Process the directory of JSON files using the collected pattern ids
     *
     * @param array<string> $coveredIds
     *
     * @throws RuntimeException
     * @throws DirectoryNotFoundException
     */
    public function process(array $coveredIds): void;

    /**
     * Write the processed coverage data to filename
     *
     * @throws JsonException
     */
    public function write(string $fileName): void;

    /**
     * Set covered pattern ids
     *
     * @param array<string> $coveredIds
     *
     * @throws void
     */
    public function setCoveredPatternIds(array $coveredIds): void;

    /**
     * Returns the stored pattern ids
     *
     * @return array<array<string>>
     *
     * @throws void
     */
    public function getCoveredPatternIds(): array;

    /**
     * Process an individual file for coverage data using covered ids
     *
     * @param array<string> $coveredIds
     *
     * @return array<array<string>>
     *
     * @throws void
     */
    public function processFile(string $file, string $contents, array $coveredIds): array;
}
