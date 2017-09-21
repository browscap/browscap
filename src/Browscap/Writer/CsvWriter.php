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
 * Class CsvWriter
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class CsvWriter implements WriterInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var resource
     */
    private $file;

    /**
     * @var \Browscap\Formatter\FormatterInterface
     */
    private $formatter;

    /**
     * @var \Browscap\Filter\FilterInterface
     */
    private $filter;

    /**
     * @var bool
     */
    private $silent = false;

    /**
     * @var array
     */
    private $outputProperties = [];

    /**
     * @param string                   $file
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(string $file, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->file   = fopen($file, 'w');
    }

    /**
     * returns the Type of the writer
     *
     * @return string
     */
    public function getType(): string
    {
        return 'csv';
    }

    /**
     * closes the Writer and the written File
     *
     * @return void
     */
    public function close(): void
    {
        fclose($this->file);
    }

    /**
     * @param \Browscap\Formatter\FormatterInterface $formatter
     *
     * @return void
     */
    public function setFormatter(FormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    /**
     * @return \Browscap\Formatter\FormatterInterface
     */
    public function getFormatter(): FormatterInterface
    {
        return $this->formatter;
    }

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     *
     * @return void
     */
    public function setFilter(FilterInterface $filter): void
    {
        $this->filter             = $filter;
        $this->outputProperties = [];
    }

    /**
     * @return \Browscap\Filter\FilterInterface
     */
    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

    /**
     * @param bool $silent
     *
     * @return void
     */
    public function setSilent(bool $silent): void
    {
        $this->silent = $silent;
    }

    /**
     * @return bool
     */
    public function isSilent(): bool
    {
        return $this->silent;
    }

    /**
     * Generates a start sequence for the output file
     *
     * @return void
     */
    public function fileStart(): void
    {
        //
    }

    /**
     * Generates a end sequence for the output file
     *
     * @return void
     */
    public function fileEnd(): void
    {
        //
    }

    /**
     * Generate the header
     *
     * @param string[] $comments
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderHeader(array $comments = []): void
    {
        //
    }

    /**
     * renders the version information
     *
     * @param string[] $versionData
     *
     * @return void
     */
    public function renderVersion(array $versionData = []): void
    {
        if ($this->isSilent()) {
            return;
        }

        $this->logger->debug('rendering version information');

        fwrite($this->file, '"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL);

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        fwrite($this->file, '"' . $versionData['version'] . '","' . $versionData['released'] . '"' . PHP_EOL);
    }

    /**
     * renders the header for all divisions
     *
     * @param \Browscap\Data\DataCollection $collection
     *
     * @return void
     */
    public function renderAllDivisionsHeader(DataCollection $collection): void
    {
        $division = $collection->getDefaultProperties();
        $ua       = $division->getUserAgents();

        if (empty($ua[0]['properties']) || !is_array($ua[0]['properties'])) {
            return;
        }

        $defaultproperties = $ua[0]['properties'];
        $properties        = array_merge(
            ['PropertyName', 'MasterParent', 'LiteMode', 'Parent'],
            array_keys($defaultproperties)
        );

        $values = [];

        foreach ($properties as $property) {
            if (!isset($this->outputProperties[$property])) {
                $this->outputProperties[$property] = $this->getFilter()->isOutputProperty($property, $this);
            }

            if (!$this->outputProperties[$property]) {
                continue;
            }

            $values[] = $this->getFormatter()->formatPropertyName($property);
        }

        fwrite($this->file, implode(',', $values) . PHP_EOL);
    }

    /**
     * renders the header for a division
     *
     * @param string $division
     * @param string $parent
     *
     * @return void
     */
    public function renderDivisionHeader(string $division, string $parent = 'DefaultProperties'): void
    {
        //
    }

    /**
     * renders the header for a section
     *
     * @param string $sectionName
     *
     * @return void
     */
    public function renderSectionHeader(string $sectionName): void
    {
        //
    }

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
    public function renderSectionBody(array $section, DataCollection $collection, array $sections = [], string $sectionName = ''): void
    {
        if ($this->isSilent()) {
            return;
        }

        $division          = $collection->getDefaultProperties();
        $ua                = $division->getUserAgents();
        $defaultproperties = $ua[0]['properties'];
        $properties        = array_merge(
            ['PropertyName', 'MasterParent', 'LiteMode', 'Parent'],
            array_keys($defaultproperties)
        );

        $values = [];

        $section['PropertyName'] = $sectionName;
        $section['MasterParent'] = $this->detectMasterParent($sectionName, $section);

        if (in_array($sectionName, ['DefaultProperties', '*'])) {
            $section['LiteMode'] = 'true';
        } else {
            $section['LiteMode'] = ((!isset($section['lite']) || !$section['lite']) ? 'false' : 'true');
        }

        foreach ($properties as $property) {
            if (!isset($this->outputProperties[$property])) {
                $this->outputProperties[$property] = $this->getFilter()->isOutputProperty($property, $this);
            }

            if (!$this->outputProperties[$property]) {
                continue;
            }

            if (isset($section[$property])) {
                $value = $section[$property];
            } else {
                $value = '';
            }

            $values[] = $this->getFormatter()->formatPropertyValue($value, $property);
        }

        fwrite($this->file, implode(',', $values) . PHP_EOL);
    }

    /**
     * renders the footer for a section
     *
     * @param string $sectionName
     *
     * @return void
     */
    public function renderSectionFooter(string $sectionName = ''): void
    {
        //
    }

    /**
     * renders the footer for a division
     *
     * @return void
     */
    public function renderDivisionFooter(): void
    {
        //
    }

    /**
     * renders the footer for all divisions
     *
     * @return void
     */
    public function renderAllDivisionsFooter(): void
    {
        //
    }

    /**
     * @param string $key
     * @param array  $properties
     *
     * @return string
     */
    private function detectMasterParent(string $key, array $properties): string
    {
        $this->logger->debug('check if the element can be marked as "MasterParent"');

        if (in_array($key, ['DefaultProperties', '*'])
            || empty($properties['Parent'])
            || 'DefaultProperties' === $properties['Parent']
        ) {
            return 'true';
        }

        return 'false';
    }
}
