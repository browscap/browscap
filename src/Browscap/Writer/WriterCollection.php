<?php

namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Psr\Log\LoggerInterface;

/**
 * Class BrowscapCsvGenerator
 *
 * @package Browscap\Generator
 */
class WriterCollection
{
    /**
     * @var WriterInterface[]
     */
    private $writers = array();

    /**
     * add a new writer to the collection
     *
     * @param WriterInterface $writer
     */
    public function addWriter(WriterInterface $writer)
    {
        $this->writers[] = $writer;
    }

    /**
     * closes the Writer and the written File
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function close()
    {
        foreach ($this->writers as $writer) {
            $writer->close();
        }

        return $this;
    }

    /**
     * @param boolean $silent
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setSilent($silent)
    {
        foreach ($this->writers as $writer) {
            $writer->setSilent($silent);
        }

        return $this;
    }

    /**
     * Generates a start sequence for the output file
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function fileStart()
    {
        foreach ($this->writers as $writer) {
            $writer->fileStart();
        }

        return $this;
    }

    /**
     * Generates a end sequence for the output file
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function fileEnd()
    {
        foreach ($this->writers as $writer) {
            $writer->fileEnd();
        }

        return $this;
    }

    /**
     * Generate the header
     *
     * @param string[] $comments
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderHeader(array $comments = array())
    {
        foreach ($this->writers as $writer) {
            $writer->renderHeader($comments);
        }

        return $this;
    }

    /**
     * renders the version information
     *
     * @param string[] $versionData
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderVersion(array $versionData = array())
    {
        foreach ($this->writers as $writer) {
            $writer->renderVersion($versionData);
        }

        return $this;
    }

    /**
     * renders the header for all divisions
     *
     * @param \Browscap\Data\DataCollection $collection
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderAllDivisionsHeader(DataCollection $collection)
    {
        foreach ($this->writers as $writer) {
            $writer->renderAllDivisionsHeader($collection);
        }

        return $this;
    }

    /**
     * renders the header for a division
     *
     * @param string $division
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderDivisionHeader($division)
    {
        foreach ($this->writers as $writer) {
            $writer->renderDivisionHeader($division);
        }

        return $this;
    }

    /**
     * renders the header for a section
     *
     * @param string $sectionName
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderSectionHeader($sectionName)
    {
        foreach ($this->writers as $writer) {
            $writer->renderSectionHeader($sectionName);
        }

        return $this;
    }

    /**
     * renders all found useragents into a string
     *
     * @param string[] $section
     *
     * @throws \InvalidArgumentException
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderSectionBody(array $section)
    {
        foreach ($this->writers as $writer) {
            $writer->renderSectionBody($section);
        }

        return $this;
    }

    /**
     * renders the footer for a section
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderSectionFooter()
    {
        foreach ($this->writers as $writer) {
            $writer->renderSectionFooter();
        }

        return $this;
    }

    /**
     * renders the footer for a division
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderDivisionFooter()
    {
        foreach ($this->writers as $writer) {
            $writer->renderDivisionFooter();
        }

        return $this;
    }

    /**
     * renders the footer for all divisions
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderAllDivisionsFooter()
    {
        foreach ($this->writers as $writer) {
            $writer->renderAllDivisionsFooter();
        }

        return $this;
    }
}
