<?php

namespace Browscap\Writer;

use Psr\Log\LoggerInterface;

/**
 * Class WriterInterface
 *
 * @package Browscap\Generator
 */
interface WriterInterface
{
    /**
     * @param string $file
     */
    public function __construct($file);

    public function __destruct();

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
     * renders the header for a division
     *
     * @param string $division
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderDivisionHeader($division);

    /**
     * renders all found useragents into a string
     *
     * @param array[] $allDivisions
     * @param string  $output
     * @param array   $allProperties
     *
     * @throws \InvalidArgumentException
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderDivisionBody(array $allDivisions, $output, array $allProperties);

    /**
     * @param \Browscap\Formatter\FormatterInterface $formatter
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function addFormatter(\Browscap\Formatter\FormatterInterface $formatter);

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function addFilter(\Browscap\Filter\FilterInterface $filter);
}
