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
     * @param array<int|string, string> $coveredIds
     *
     * @return array<string, array<int, string|int>|string>
     * @phpstan-return array{path: string, statementMap: array<int, array{start?: array{line?: int, column?: int|false}, end?: array{line?: int, column?: int|false}}>, fnMap: array<int, array{name: string, decl: array{start?: array{line: int, column: int|false}, end?: array{line: int, column: int|false}}, loc: array{start: array{line: int, column: int|false}, end: array{line: int, column: int|false}}}>, branchMap: array<int, array{type: 'switch', locations: array<int, array{start?: array{line: int, column: int|false}, end?: array{line: int, column: int|false}}>, loc:array{start: array{line: int, column: int|false}, end: array{line: int, column: int|false}}}>, s: array<int, int>, b: array<int, array<int, int>>, f: array<int, int>}
     *
     * @throws void
     */
    public function processFile(string $file, string $contents, array $coveredIds): array;
}
