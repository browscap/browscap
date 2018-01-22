<?php
declare(strict_types = 1);
namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use Psr\Log\LoggerInterface;

interface WriterInterface
{
    public const TYPE_CSV  = 'csv';
    public const TYPE_INI  = 'ini';
    public const TYPE_JSON = 'json';
    public const TYPE_XML  = 'xml';

    /**
     * @param string          $file
     * @param LoggerInterface $logger
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
     * @param DataCollection $collection
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
     * @param array          $section
     * @param DataCollection $collection
     * @param array[]        $sections
     * @param string         $sectionName
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
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter) : void;

    /**
     * @return FormatterInterface
     */
    public function getFormatter() : FormatterInterface;

    /**
     * @param FilterInterface $filter
     */
    public function setFilter(FilterInterface $filter) : void;

    /**
     * @return FilterInterface
     */
    public function getFilter() : FilterInterface;
}
