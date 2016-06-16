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
 * Class IniWriter
 *
 * @category   Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class IniWriter implements WriterInterface, WriterNeedsExpanderInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @var resource
     */
    private $file = null;

    /**
     * @var \Browscap\Formatter\FormatterInterface
     */
    private $formatter = null;

    /**
     * @var \Browscap\Filter\FilterInterface
     */
    private $type = null;

    /**
     * @var bool
     */
    private $silent = false;

    /**
     * @var array
     */
    private $outputProperties = [];

    /**
     * @var \Browscap\Data\Expander
     */
    private $expander = null;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = fopen($file, 'w');
    }

    /**
     * returns the Type of the writer
     *
     * @return string
     */
    public function getType()
    {
        return 'ini';
    }

    /**
     * closes the Writer and the written File
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function close()
    {
        fclose($this->file);
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param \Browscap\Formatter\FormatterInterface $formatter
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * @return \Browscap\Formatter\FormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setFilter(FilterInterface $filter)
    {
        $this->type             = $filter;
        $this->outputProperties = [];

        return $this;
    }

    /**
     * @return \Browscap\Filter\FilterInterface
     */
    public function getFilter()
    {
        return $this->type;
    }

    /**
     * @param \Browscap\Data\Expander $expander
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setExpander(Expander $expander)
    {
        $this->expander = $expander;

        return $this;
    }

    /**
     * @param bool $silent
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setSilent($silent)
    {
        $this->silent = (boolean) $silent;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSilent()
    {
        return $this->silent;
    }

    /**
     * Generates a start sequence for the output file
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function fileStart()
    {
        return $this;
    }

    /**
     * Generates a end sequence for the output file
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function fileEnd()
    {
        return $this;
    }

    /**
     * Generate the header
     *
     * @param string[] $comments
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderHeader(array $comments = [])
    {
        if ($this->isSilent()) {
            return $this;
        }

        $this->getLogger()->debug('rendering comments');

        foreach ($comments as $comment) {
            fputs($this->file, ';;; ' . $comment . PHP_EOL);
        }

        fputs($this->file, PHP_EOL);

        return $this;
    }

    /**
     * renders the version information
     *
     * @param string[] $versionData
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderVersion(array $versionData = [])
    {
        if ($this->isSilent()) {
            return $this;
        }

        $this->getLogger()->debug('rendering version information');

        $this->renderDivisionHeader('Browscap Version');

        fputs($this->file, '[GJK_Browscap_Version]' . PHP_EOL);

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        if (!isset($versionData['format'])) {
            $versionData['format'] = '';
        }

        if (!isset($versionData['type'])) {
            $versionData['type'] = '';
        }

        fputs($this->file, 'Version=' . $versionData['version'] . PHP_EOL);
        fputs($this->file, 'Released=' . $versionData['released'] . PHP_EOL);
        fputs($this->file, 'Format=' . $versionData['format'] . PHP_EOL);
        fputs($this->file, 'Type=' . $versionData['type'] . PHP_EOL . PHP_EOL);

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
        return $this;
    }

    /**
     * renders the header for a division
     *
     * @param string $division
     * @param string $parent
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderDivisionHeader($division, $parent = 'DefaultProperties')
    {
        if ($this->isSilent() || 'DefaultProperties' !== $parent) {
            return $this;
        }

        fputs($this->file, ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; ' . $division . PHP_EOL . PHP_EOL);

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
        if ($this->isSilent()) {
            return $this;
        }

        fputs($this->file, '[' . $sectionName . ']' . PHP_EOL);

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
     * @return IniWriter
     */
    public function renderSectionBody(array $section, DataCollection $collection, array $sections = [], $sectionName = '')
    {
        if ($this->isSilent()) {
            return $this;
        }

        $division          = $collection->getDefaultProperties();
        $ua                = $division->getUserAgents();
        $defaultproperties = $ua[0]['properties'];
        $properties        = array_merge(['Parent'], array_keys($defaultproperties));

        foreach ($defaultproperties as $propertyName => $propertyValue) {
            $defaultproperties[$propertyName] = $this->expander->trimProperty($propertyValue);
        }

        foreach ($properties as $property) {
            if (!isset($section[$property])) {
                continue;
            }

            if (!isset($this->outputProperties[$property])) {
                $this->outputProperties[$property] = $this->getFilter()->isOutputProperty($property, $this);
            }

            if (!$this->outputProperties[$property]) {
                continue;
            }

            if (isset($section['Parent']) && 'Parent' !== $property) {
                if ('DefaultProperties' === $section['Parent']
                    || !isset($sections[$section['Parent']])
                ) {
                    if (isset($defaultproperties[$property])
                        && $defaultproperties[$property] === $section[$property]
                    ) {
                        continue;
                    }
                } else {
                    $parentProperties = $sections[$section['Parent']];

                    if (isset($parentProperties[$property])
                        && $parentProperties[$property] === $section[$property]
                    ) {
                        continue;
                    }
                }
            }

            fputs(
                $this->file,
                $this->getFormatter()->formatPropertyName($property)
                . '=' . $this->getFormatter()->formatPropertyValue($section[$property], $property) . PHP_EOL
            );
        }

        return $this;
    }

    /**
     * renders the footer for a section
     *
     * @param string $sectionName
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderSectionFooter($sectionName = '')
    {
        if ($this->isSilent()) {
            return $this;
        }

        fputs($this->file, PHP_EOL);

        return $this;
    }

    /**
     * renders the footer for a division
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderDivisionFooter()
    {
        return $this;
    }

    /**
     * renders the footer for all divisions
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderAllDivisionsFooter()
    {
        return $this;
    }
}
