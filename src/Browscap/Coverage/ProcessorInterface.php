<?php

declare(strict_types=1);

namespace Browscap\Coverage;

interface ProcessorInterface
{
    /**
     * Process the directory of JSON files using the collected pattern ids
     *
     * @param array<string> $coveredIds
     */
    public function process(array $coveredIds): void;

    /**
     * Write the processed coverage data to filename
     */
    public function write(string $fileName): void;

    /**
     * Set covered pattern ids
     *
     * @param array<string> $coveredIds
     */
    public function setCoveredPatternIds(array $coveredIds): void;

    /**
     * Returns the stored pattern ids
     *
     * @return array<array<string>>
     */
    public function getCoveredPatternIds(): array;

    /**
     * Process an individual file for coverage data using covered ids
     *
     * @param array<string> $coveredIds
     *
     * @return array<array<string>>
     */
    public function processFile(string $file, string $contents, array $coveredIds): array;
}
