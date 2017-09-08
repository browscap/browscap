<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Data\Expander;

/**
 * Class WriterCollection
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class WriterCollection
{
    /**
     * @var \Browscap\Writer\WriterInterface[]
     */
    private $writers = [];

    /**
     * add a new writer to the collection
     *
     * @param \Browscap\Writer\WriterInterface $writer
     */
    public function addWriter(WriterInterface $writer) : void
    {
        $this->writers[] = $writer;
    }

    /**
     * closes the Writer and the written File
     */
    public function close() : void
    {
        foreach ($this->writers as $writer) {
            $writer->close();
        }
    }

    /**
     * @param \Browscap\Data\Division $division
     */
    public function setSilent(Division $division) : void
    {
        foreach ($this->writers as $writer) {
            $writer->setSilent(!$writer->getFilter()->isOutput($division));
        }
    }

    /**
     * @param Expander $expander
     */
    public function setExpander(Expander $expander) : void
    {
        foreach ($this->writers as $writer) {
            if ($writer instanceof WriterNeedsExpanderInterface) {
                $writer->setExpander($expander);
            }
        }
    }

    /**
     * @param mixed $section
     */
    public function setSilentSection($section) : void
    {
        foreach ($this->writers as $writer) {
            $writer->setSilent(!$writer->getFilter()->isOutputSection($section));
        }
    }

    /**
     * Generates a start sequence for the output file
     */
    public function fileStart() : void
    {
        foreach ($this->writers as $writer) {
            $writer->fileStart();
        }
    }

    /**
     * Generates a end sequence for the output file
     */
    public function fileEnd() : void
    {
        foreach ($this->writers as $writer) {
            $writer->fileEnd();
        }
    }

    /**
     * Generate the header
     *
     * @param string[] $comments
     */
    public function renderHeader(array $comments = []) : void
    {
        foreach ($this->writers as $writer) {
            $writer->renderHeader($comments);
        }
    }

    /**
     * renders the version information
     *
     * @param string                        $version
     * @param \Browscap\Data\DataCollection $collection
     */
    public function renderVersion($version, DataCollection $collection) : void
    {
        foreach ($this->writers as $writer) {
            $writer->renderVersion(
                [
                    'version' => $version,
                    'released' => $collection->getGenerationDate()->format('r'),
                    'format' => $writer->getFormatter()->getType(),
                    'type' => $writer->getFilter()->getType(),
                ]
            );
        }
    }

    /**
     * renders the header for all divisions
     *
     * @param \Browscap\Data\DataCollection $collection
     */
    public function renderAllDivisionsHeader(DataCollection $collection) : void
    {
        foreach ($this->writers as $writer) {
            $writer->renderAllDivisionsHeader($collection);
        }
    }

    /**
     * renders the header for a division
     *
     * @param string $division
     * @param string $parent
     */
    public function renderDivisionHeader($division, $parent = 'DefaultProperties') : void
    {
        foreach ($this->writers as $writer) {
            $writer->renderDivisionHeader($division, $parent);
        }
    }

    /**
     * renders the header for a section
     *
     * @param string $sectionName
     */
    public function renderSectionHeader($sectionName) : void
    {
        foreach ($this->writers as $writer) {
            $writer->renderSectionHeader($sectionName);
        }
    }

    /**
     * renders all found useragents into a string
     *
     * @param (int|string|true)[]           $section
     * @param \Browscap\Data\DataCollection $collection
     * @param array[]                       $sections
     * @param string                        $sectionName
     *
     * @throws \InvalidArgumentException
     */
    public function renderSectionBody(array $section, DataCollection $collection, array $sections = [], $sectionName = '') : void
    {
        foreach ($this->writers as $writer) {
            $writer->renderSectionBody($section, $collection, $sections, $sectionName);
        }
    }

    /**
     * renders the footer for a section
     *
     * @param string $sectionName
     */
    public function renderSectionFooter($sectionName = '') : void
    {
        foreach ($this->writers as $writer) {
            $writer->renderSectionFooter($sectionName);
        }
    }

    /**
     * renders the footer for a division
     */
    public function renderDivisionFooter() : void
    {
        foreach ($this->writers as $writer) {
            $writer->renderDivisionFooter();
        }
    }

    /**
     * renders the footer for all divisions
     */
    public function renderAllDivisionsFooter() : void
    {
        foreach ($this->writers as $writer) {
            $writer->renderAllDivisionsFooter();
        }
    }
}
