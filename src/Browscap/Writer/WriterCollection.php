<?php

declare(strict_types=1);

namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * a collection of writers to be able to write multiple files at once
 */
class WriterCollection
{
    /** @var WriterInterface[] */
    private $writers = [];

    /**
     * add a new writer to the collection
     */
    public function addWriter(WriterInterface $writer): void
    {
        $this->writers[] = $writer;
    }

    /**
     * closes the Writer and the written File
     */
    public function close(): void
    {
        foreach ($this->writers as $writer) {
            $writer->close();
        }
    }

    public function setSilent(Division $division): void
    {
        foreach ($this->writers as $writer) {
            $writer->setSilent(! $writer->getFilter()->isOutput($division));
        }
    }

    /**
     * @param bool[] $section
     */
    public function setSilentSection(array $section): void
    {
        foreach ($this->writers as $writer) {
            $writer->setSilent(! $writer->getFilter()->isOutputSection($section));
        }
    }

    /**
     * Generates a start sequence for the output file
     */
    public function fileStart(): void
    {
        foreach ($this->writers as $writer) {
            $writer->fileStart();
        }
    }

    /**
     * Generates a end sequence for the output file
     */
    public function fileEnd(): void
    {
        foreach ($this->writers as $writer) {
            $writer->fileEnd();
        }
    }

    /**
     * Generate the header
     *
     * @param array<string> $comments
     */
    public function renderHeader(array $comments = []): void
    {
        foreach ($this->writers as $writer) {
            $writer->renderHeader($comments);
        }
    }

    /**
     * renders the version information
     */
    public function renderVersion(string $version, DateTimeImmutable $generationDate, DataCollection $collection): void
    {
        foreach ($this->writers as $writer) {
            $writer->renderVersion(
                [
                    'version' => $version,
                    'released' => $generationDate->format('r'),
                    'format' => $writer->getFormatter()->getType(),
                    'type' => $writer->getFilter()->getType(),
                ]
            );
        }
    }

    /**
     * renders the header for all divisions
     */
    public function renderAllDivisionsHeader(DataCollection $collection): void
    {
        foreach ($this->writers as $writer) {
            $writer->renderAllDivisionsHeader($collection);
        }
    }

    /**
     * renders the header for a division
     */
    public function renderDivisionHeader(string $division, string $parent = 'DefaultProperties'): void
    {
        foreach ($this->writers as $writer) {
            $writer->renderDivisionHeader($division, $parent);
        }
    }

    /**
     * renders the header for a section
     */
    public function renderSectionHeader(string $sectionName): void
    {
        foreach ($this->writers as $writer) {
            $writer->renderSectionHeader($sectionName);
        }
    }

    /**
     * renders all found useragents into a string
     *
     * @param mixed[]              $section
     * @param array<array<string>> $sections
     *
     * @throws InvalidArgumentException
     */
    public function renderSectionBody(array $section, DataCollection $collection, array $sections = [], string $sectionName = ''): void
    {
        foreach ($this->writers as $writer) {
            $writer->renderSectionBody($section, $collection, $sections, $sectionName);
        }
    }

    /**
     * renders the footer for a section
     */
    public function renderSectionFooter(string $sectionName = ''): void
    {
        foreach ($this->writers as $writer) {
            $writer->renderSectionFooter($sectionName);
        }
    }

    /**
     * renders the footer for a division
     */
    public function renderDivisionFooter(): void
    {
        foreach ($this->writers as $writer) {
            $writer->renderDivisionFooter();
        }
    }

    /**
     * renders the footer for all divisions
     */
    public function renderAllDivisionsFooter(): void
    {
        foreach ($this->writers as $writer) {
            $writer->renderAllDivisionsFooter();
        }
    }
}
