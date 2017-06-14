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
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function addWriter(WriterInterface $writer)
    {
        $this->writers[] = $writer;

        return $this;
    }

    /**
     * closes the Writer and the written File
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function close()
    {
        foreach ($this->writers as $writer) {
            $writer->close();
        }

        return $this;
    }

    /**
     * @param \Browscap\Data\Division $division
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function setSilent(Division $division)
    {
        foreach ($this->writers as $writer) {
            $writer->setSilent(!$writer->getFilter()->isOutput($division));
        }

        return $this;
    }

    /**
     * @param Expander $expander
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setExpander(Expander $expander)
    {
        foreach ($this->writers as $writer) {
            if ($writer instanceof WriterNeedsExpanderInterface) {
                $writer->setExpander($expander);
            }
        }

        return $this;
    }

    /**
     * @param mixed $section
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function setSilentSection($section)
    {
        foreach ($this->writers as $writer) {
            $writer->setSilent(!$writer->getFilter()->isOutputSection($section));
        }

        return $this;
    }

    /**
     * Generates a start sequence for the output file
     *
     * @return \Browscap\Writer\WriterCollection
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
     * @return \Browscap\Writer\WriterCollection
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
     * @return \Browscap\Writer\WriterCollection
     */
    public function renderHeader(array $comments = [])
    {
        foreach ($this->writers as $writer) {
            $writer->renderHeader($comments);
        }

        return $this;
    }

    /**
     * renders the version information
     *
     * @param string                        $version
     * @param \Browscap\Data\DataCollection $collection
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function renderVersion($version, DataCollection $collection)
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

        return $this;
    }

    /**
     * renders the header for all divisions
     *
     * @param \Browscap\Data\DataCollection $collection
     *
     * @return \Browscap\Writer\WriterCollection
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
     * @param string $parent
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function renderDivisionHeader($division, $parent = 'DefaultProperties')
    {
        foreach ($this->writers as $writer) {
            $writer->renderDivisionHeader($division, $parent);
        }

        return $this;
    }

    /**
     * renders the header for a section
     *
     * @param string $sectionName
     *
     * @return \Browscap\Writer\WriterCollection
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
     * @param string[]                      $section
     * @param \Browscap\Data\DataCollection $collection
     * @param array[]                       $sections
     * @param string                        $sectionName
     *
     * @throws \InvalidArgumentException
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function renderSectionBody(array $section, DataCollection $collection, array $sections = [], $sectionName = '')
    {
        foreach ($this->writers as $writer) {
            $writer->renderSectionBody($section, $collection, $sections, $sectionName);
        }

        return $this;
    }

    /**
     * renders the footer for a section
     *
     * @param string $sectionName
     *
     * @return \Browscap\Writer\WriterCollection
     */
    public function renderSectionFooter($sectionName = '')
    {
        foreach ($this->writers as $writer) {
            $writer->renderSectionFooter($sectionName);
        }

        return $this;
    }

    /**
     * renders the footer for a division
     *
     * @return \Browscap\Writer\WriterCollection
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
     * @return \Browscap\Writer\WriterCollection
     */
    public function renderAllDivisionsFooter()
    {
        foreach ($this->writers as $writer) {
            $writer->renderAllDivisionsFooter();
        }

        return $this;
    }
}
