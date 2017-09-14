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
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface WriterInterface
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
interface WriterInterface
{
    /**
     * @param string                   $file
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(string $file, LoggerInterface $logger);

    /**
     * returns the Type of the writer
     *
     * @return string
     */
    public function getType() : string;

    /**
     * closes the Writer and the written File
     */
    public function close() : void;

    /**
     * @param bool $silent
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setSilent(bool $silent) : void;

    /**
     * @return bool
     */
    public function isSilent() : bool;

    /**
     * Generates a start sequence for the output file
     */
    public function fileStart() : void;

    /**
     * Generates a end sequence for the output file
     */
    public function fileEnd() : void;

    /**
     * Generate the header
     *
     * @param string[] $comments
     */
    public function renderHeader(array $comments = []) : void;

    /**
     * renders the version information
     *
     * @param string[] $versionData
     */
    public function renderVersion(array $versionData = []) : void;

    /**
     * renders the header for all divisions
     *
     * @param \Browscap\Data\DataCollection $collection
     */
    public function renderAllDivisionsHeader(DataCollection $collection) : void;

    /**
     * renders the header for a division
     *
     * @param string $division
     * @param string $parent
     */
    public function renderDivisionHeader(string $division, string $parent = 'DefaultProperties') : void;

    /**
     * renders the header for a section
     *
     * @param string $sectionName
     */
    public function renderSectionHeader(string $sectionName) : void;

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
    public function renderSectionBody(array $section, DataCollection $collection, array $sections = [], string $sectionName = '') : void;

    /**
     * renders the footer for a section
     *
     * @param string $sectionName
     */
    public function renderSectionFooter(string $sectionName = '') : void;

    /**
     * renders the footer for a division
     */
    public function renderDivisionFooter() : void;

    /**
     * renders the footer for all divisions
     */
    public function renderAllDivisionsFooter() : void;

    /**
     * @param \Browscap\Formatter\FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter) : void;

    /**
     * @return \Browscap\Formatter\FormatterInterface
     */
    public function getFormatter() : FormatterInterface;

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setFilter(FilterInterface $filter) : void;

    /**
     * @return \Browscap\Filter\FilterInterface
     */
    public function getFilter() : FilterInterface;
}
