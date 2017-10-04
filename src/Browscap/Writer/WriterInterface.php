<?php
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
    public const TYPE_CSV  = 'csv';
    public const TYPE_INI  = 'ini';
    public const TYPE_JSON = 'json';
    public const TYPE_XML  = 'xml';

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
     *
     * @return void
     */
    public function close() : void;

    /**
     * @param bool $silent
     *
     * @return void
     */
    public function setSilent(bool $silent) : void;

    /**
     * @return bool
     */
    public function isSilent() : bool;

    /**
     * Generates a start sequence for the output file
     *
     * @return void
     */
    public function fileStart() : void;

    /**
     * Generates a end sequence for the output file
     *
     * @return void
     */
    public function fileEnd() : void;

    /**
     * Generate the header
     *
     * @param string[] $comments
     *
     * @return void
     */
    public function renderHeader(array $comments = []) : void;

    /**
     * renders the version information
     *
     * @param string[] $versionData
     *
     * @return void
     */
    public function renderVersion(array $versionData = []) : void;

    /**
     * renders the header for all divisions
     *
     * @param \Browscap\Data\DataCollection $collection
     *
     * @return void
     */
    public function renderAllDivisionsHeader(DataCollection $collection) : void;

    /**
     * renders the header for a division
     *
     * @param string $division
     * @param string $parent
     *
     * @return void
     */
    public function renderDivisionHeader(string $division, string $parent = 'DefaultProperties') : void;

    /**
     * renders the header for a section
     *
     * @param string $sectionName
     *
     * @return void
     */
    public function renderSectionHeader(string $sectionName) : void;

    /**
     * renders all found useragents into a string
     *
     * @param (int|string|bool)[]           $section
     * @param \Browscap\Data\DataCollection $collection
     * @param array[]                       $sections
     * @param string                        $sectionName
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function renderSectionBody(array $section, DataCollection $collection, array $sections = [], string $sectionName = '') : void;

    /**
     * renders the footer for a section
     *
     * @param string $sectionName
     *
     * @return void
     */
    public function renderSectionFooter(string $sectionName = '') : void;

    /**
     * renders the footer for a division
     *
     * @return void
     */
    public function renderDivisionFooter() : void;

    /**
     * renders the footer for all divisions
     *
     * @return void
     */
    public function renderAllDivisionsFooter() : void;

    /**
     * @param \Browscap\Formatter\FormatterInterface $formatter
     *
     * @return void
     */
    public function setFormatter(FormatterInterface $formatter) : void;

    /**
     * @return \Browscap\Formatter\FormatterInterface
     */
    public function getFormatter() : FormatterInterface;

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     *
     * @return void
     */
    public function setFilter(FilterInterface $filter) : void;

    /**
     * @return \Browscap\Filter\FilterInterface
     */
    public function getFilter() : FilterInterface;
}
