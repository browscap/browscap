<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   Browscap
 * @package    Writer
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\Expander;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface WriterInterface
 *
 * @category   Browscap
 * @package    Writer
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
interface WriterInterface
{
    /**
     * @param string $file
     */
    public function __construct($file);

    /**
     * returns the Type of the writer
     *
     * @return string
     */
    public function getType();

    /**
     * closes the Writer and the written File
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function close();

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setLogger(LoggerInterface $logger);

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger();

    /**
     * @param Expander $expander
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setExpander(Expander $expander);

    /**
     * @param boolean $silent
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setSilent($silent);

    /**
     * @return boolean
     */
    public function isSilent();

    /**
     * Generates a start sequence for the output file
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function fileStart();

    /**
     * Generates a end sequence for the output file
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function fileEnd();

    /**
     * Generate the header
     *
     * @param string[] $comments
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderHeader(array $comments = array());

    /**
     * renders the version information
     *
     * @param string[] $versionData
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderVersion(array $versionData = array());

    /**
     * renders the header for all divisions
     *
     * @param \Browscap\Data\DataCollection $collection
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderAllDivisionsHeader(DataCollection $collection);

    /**
     * renders the header for a division
     *
     * @param string $division
     * @param string $parent
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderDivisionHeader($division, $parent = 'DefaultProperties');

    /**
     * renders the header for a section
     *
     * @param string $sectionName
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderSectionHeader($sectionName);

    /**
     * renders all found useragents into a string
     *
     * @param string[]                      $section
     * @param \Browscap\Data\DataCollection $collection
     * @param array[]                       $sections
     * @param string                        $sectionName
     *
     * @throws \InvalidArgumentException
     * @return \Browscap\Writer\WriterCollection
     */
    public function renderSectionBody(array $section, DataCollection $collection, array $sections = array(), $sectionName = '');

    /**
     * renders the footer for a section
     *
     * @param string $sectionName
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderSectionFooter($sectionName = '');

    /**
     * renders the footer for a division
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderDivisionFooter();

    /**
     * renders the footer for all divisions
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderAllDivisionsFooter();

    /**
     * @param \Browscap\Formatter\FormatterInterface $formatter
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setFormatter(FormatterInterface $formatter);

    /**
     * @return \Browscap\Formatter\FormatterInterface
     */
    public function getFormatter();

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setFilter(FilterInterface $filter);

    /**
     * @return \Browscap\Filter\FilterInterface
     */
    public function getFilter();
}
