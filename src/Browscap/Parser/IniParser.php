<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Browscap\Parser;

use InvalidArgumentException;
use RuntimeException;

use function array_keys;
use function count;
use function explode;
use function file;
use function file_exists;
use function is_array;
use function ksort;
use function mb_strlen;
use function mb_substr;
use function sprintf;
use function trim;

use const FILE_IGNORE_NEW_LINES;
use const FILE_SKIP_EMPTY_LINES;

final class IniParser implements ParserInterface
{
    private string $filename;

    private bool $shouldSort = false;

    /** @var array<array<array<string>|string>> */
    private array $data;

    /** @var array<string> */
    private array $fileLines = [];

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function setShouldSort(bool $shouldSort): void
    {
        $this->shouldSort = $shouldSort;
    }

    public function shouldSort(): bool
    {
        return $this->shouldSort;
    }

    /**
     * @return array<array<array<string>|string>>
     */
    public function getParsed(): array
    {
        return $this->data;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return array<string>
     *
     * @throws InvalidArgumentException
     */
    public function getLinesFromFile(): array
    {
        $filename = $this->filename;

        if (! file_exists($filename)) {
            throw new InvalidArgumentException(sprintf('File not found: %s', $filename));
        }

        $data = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($data === false) {
            throw new InvalidArgumentException(sprintf('An error occured while reading File: %s', $filename));
        }

        return $data;
    }

    /**
     * @param array<string> $fileLines
     */
    public function setFileLines(array $fileLines): void
    {
        $this->fileLines = $fileLines;
    }

    /**
     * @return array<string>
     */
    public function getFileLines(): array
    {
        if (! $this->fileLines) {
            $fileLines = $this->getLinesFromFile();
        } else {
            $fileLines = $this->fileLines;
        }

        return $fileLines;
    }

    /**
     * @return array<array<array<string>|string>>
     *
     * @throws RuntimeException
     */
    public function parse(): array
    {
        $fileLines = $this->getFileLines();

        $data = [];

        $currentSection  = '';
        $currentDivision = '';

        for ($line = 0, $count = count($fileLines); $line < $count; ++$line) {
            $currentLine       = $fileLines[$line];
            $currentLineLength = mb_strlen($currentLine);

            if ($currentLineLength === 0) {
                continue;
            }

            if (mb_substr($currentLine, 0, 40) === ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;') {
                $currentDivision = trim(mb_substr($currentLine, 41));

                continue;
            }

            // We only skip comments that *start* with semicolon
            if ($currentLine[0] === ';') {
                continue;
            }

            if ($currentLine[0] === '[') {
                $currentSection = mb_substr($currentLine, 1, $currentLineLength - 2);

                continue;
            }

            $bits = explode('=', $currentLine);

            if (2 < count($bits)) {
                throw new RuntimeException(sprintf('Too many equals in line: %s, in Division: %s', $currentLine, $currentDivision));
            }

            if (2 > count($bits)) {
                $bits[1] = '';
            }

            $data[$currentSection][$bits[0]]   = $bits[1];
            $data[$currentSection]['Division'] = $currentDivision;
        }

        if ($this->shouldSort()) {
            $data = $this->sortArrayAndChildArrays($data);
        }

        $this->data = $data;

        return $data;
    }

    /**
     * @param array<array<string>|string> $array
     *
     * @return array<array<array<string>|string>>
     */
    private function sortArrayAndChildArrays(array $array): array
    {
        ksort($array);

        foreach (array_keys($array) as $key) {
            if (! is_array($array[$key]) || empty($array[$key])) {
                continue;
            }

            $array[$key] = $this->sortArrayAndChildArrays($array[$key]);
        }

        return $array;
    }
}
