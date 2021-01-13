<?php

declare(strict_types=1);

namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

interface WriterInterface
{
    public const TYPE_CSV  = 'csv';
    public const TYPE_INI  = 'ini';
    public const TYPE_JSON = 'json';
    public const TYPE_XML  = 'xml';

    public function __construct(string $file, LoggerInterface $logger);

    /**
     * returns the Type of the writer
     */
    public function getType(): string;

    /**
     * closes the Writer and the written File
     */
    public function close(): void;

    public function setSilent(bool $silent): void;

    public function isSilent(): bool;

    /**
     * Generates a start sequence for the output file
     */
    public function fileStart(): void;

    /**
     * Generates a end sequence for the output file
     */
    public function fileEnd(): void;

    /**
     * Generate the header
     *
     * @param array<string> $comments
     */
    public function renderHeader(array $comments = []): void;

    /**
     * renders the version information
     *
     * @param array<string> $versionData
     */
    public function renderVersion(array $versionData = []): void;

    /**
     * renders the header for all divisions
     */
    public function renderAllDivisionsHeader(DataCollection $collection): void;

    /**
     * renders the header for a division
     */
    public function renderDivisionHeader(string $division, string $parent = 'DefaultProperties'): void;

    /**
     * renders the header for a section
     */
    public function renderSectionHeader(string $sectionName): void;

    /**
     * renders all found useragents into a string
     *
     * @param array<string, int|string|true>            $section
     * @param array<string, array<string, bool|string>> $sections
     *
     * @throws InvalidArgumentException
     */
    public function renderSectionBody(array $section, DataCollection $collection, array $sections = [], string $sectionName = ''): void;

    /**
     * renders the footer for a section
     */
    public function renderSectionFooter(string $sectionName = ''): void;

    /**
     * renders the footer for a division
     */
    public function renderDivisionFooter(): void;

    /**
     * renders the footer for all divisions
     */
    public function renderAllDivisionsFooter(): void;

    public function setFormatter(FormatterInterface $formatter): void;

    public function getFormatter(): FormatterInterface;

    public function setFilter(FilterInterface $filter): void;

    public function getFilter(): FilterInterface;
}
