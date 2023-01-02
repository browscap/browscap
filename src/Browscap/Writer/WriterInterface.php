<?php

declare(strict_types=1);

namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use InvalidArgumentException;
use JsonException;

interface WriterInterface
{
    public const TYPE_CSV  = 'csv';
    public const TYPE_INI  = 'ini';
    public const TYPE_JSON = 'json';
    public const TYPE_XML  = 'xml';

    /**
     * returns the Type of the writer
     *
     * @throws void
     */
    public function getType(): string;

    /**
     * closes the Writer and the written File
     *
     * @throws void
     */
    public function close(): void;

    /** @throws void */
    public function setSilent(bool $silent): void;

    /** @throws void */
    public function isSilent(): bool;

    /**
     * Generates a start sequence for the output file
     *
     * @throws void
     */
    public function fileStart(): void;

    /**
     * Generates a end sequence for the output file
     *
     * @throws void
     */
    public function fileEnd(): void;

    /**
     * Generate the header
     *
     * @param array<string> $comments
     *
     * @throws JsonException
     */
    public function renderHeader(array $comments = []): void;

    /**
     * renders the version information
     *
     * @param array<string> $versionData
     *
     * @throws JsonException
     */
    public function renderVersion(array $versionData = []): void;

    /**
     * renders the header for all divisions
     *
     * @throws void
     */
    public function renderAllDivisionsHeader(DataCollection $collection): void;

    /**
     * renders the header for a division
     *
     * @throws void
     */
    public function renderDivisionHeader(string $division, string $parent = 'DefaultProperties'): void;

    /**
     * renders the header for a section
     *
     * @throws void
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
     *
     * @throws void
     */
    public function renderSectionFooter(string $sectionName = ''): void;

    /**
     * renders the footer for a division
     *
     * @throws void
     */
    public function renderDivisionFooter(): void;

    /**
     * renders the footer for all divisions
     *
     * @throws void
     */
    public function renderAllDivisionsFooter(): void;

    /** @throws void */
    public function setFormatter(FormatterInterface $formatter): void;

    /** @throws void */
    public function getFormatter(): FormatterInterface;

    /** @throws void */
    public function setFilter(FilterInterface $filter): void;

    /** @throws void */
    public function getFilter(): FilterInterface;
}
