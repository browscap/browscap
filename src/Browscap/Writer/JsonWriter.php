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
use Browscap\Data\Helper\TrimProperty;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use Psr\Log\LoggerInterface;

/**
 * Class JsonWriter
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */

class JsonWriter implements WriterInterface
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
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @var FilterInterface
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
     * @var \Browscap\Data\Helper\TrimProperty
     */
    private $trimProperty;

    /**
     * @param string                   $file
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(string $file, LoggerInterface $logger)
    {
        $this->logger       = $logger;
        $this->file         = fopen($file, 'w');
        $this->trimProperty = new TrimProperty();
    }

    /**
     * returns the Type of the writer
     *
     * @return string
     */
    public function getType() : string
    {
        return WriterInterface::TYPE_JSON;
    }

    /**
     * closes the Writer and the written File
     *
     * @return void
     */
    public function close() : void
    {
        fclose($this->file);
    }

    /**
     * @param \Browscap\Formatter\FormatterInterface $formatter
     *
     * @return void
     */
    public function setFormatter(FormatterInterface $formatter) : void
    {
        $this->formatter = $formatter;
    }

    /**
     * @return \Browscap\Formatter\FormatterInterface
     */
    public function getFormatter() : FormatterInterface
    {
        return $this->formatter;
    }

    /**
     * @param \Browscap\Filter\FilterInterface $filter
     *
     * @return void
     */
    public function setFilter(FilterInterface $filter) : void
    {
        $this->filter           = $filter;
        $this->outputProperties = [];
    }

    /**
     * @return \Browscap\Filter\FilterInterface
     */
    public function getFilter() : FilterInterface
    {
        return $this->filter;
    }

    /**
     * @param bool $silent
     *
     * @return void
     */
    public function setSilent(bool $silent) : void
    {
        $this->silent = $silent;
    }

    /**
     * @return bool
     */
    public function isSilent() : bool
    {
        return $this->silent;
    }

    /**
     * Generates a start sequence for the output file
     *
     * @return void
     */
    public function fileStart() : void
    {
        if ($this->isSilent()) {
            return;
        }

        fwrite($this->file, '{' . PHP_EOL);
    }

    /**
     * Generates a end sequence for the output file
     *
     * @return void
     */
    public function fileEnd() : void
    {
        if ($this->isSilent()) {
            return;
        }

        fwrite($this->file, '}' . PHP_EOL);
    }

    /**
     * Generate the header
     *
     * @param string[] $comments
     *
     * @return void
     */
    public function renderHeader(array $comments = []) : void
    {
        if ($this->isSilent()) {
            return;
        }

        $this->logger->debug('rendering comments');

        fwrite($this->file, '  "comments": [' . PHP_EOL);

        foreach ($comments as $i => $text) {
            fwrite($this->file, '    ' . json_encode($text));

            if ($i < (count($comments) - 1)) {
                fwrite($this->file, ',');
            }

            fwrite($this->file, PHP_EOL);
        }

        fwrite($this->file, '  ],' . PHP_EOL);
    }

    /**
     * renders the version information
     *
     * @param string[] $versionData
     *
     * @return void
     */
    public function renderVersion(array $versionData = []) : void
    {
        if ($this->isSilent()) {
            return;
        }

        $this->logger->debug('rendering version information');

        fwrite($this->file, '  "GJK_Browscap_Version": {' . PHP_EOL);

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        fwrite($this->file, '    "Version": ' . json_encode($versionData['version']) . ',' . PHP_EOL);
        fwrite($this->file, '    "Released": ' . json_encode($versionData['released']) . '' . PHP_EOL);

        fwrite($this->file, '  },' . PHP_EOL);
    }

    /**
     * renders the header for all divisions
     *
     * @param \Browscap\Data\DataCollection $collection
     *
     * @return void
     */
    public function renderAllDivisionsHeader(DataCollection $collection) : void
    {
        // nothing to do here
    }

    /**
     * renders the header for a division
     *
     * @param string $division
     * @param string $parent
     *
     * @return void
     */
    public function renderDivisionHeader(string $division, string $parent = 'DefaultProperties') : void
    {
        // nothing to do here
    }

    /**
     * renders the header for a section
     *
     * @param string $sectionName
     *
     * @return void
     */
    public function renderSectionHeader(string $sectionName) : void
    {
        if ($this->isSilent()) {
            return;
        }

        fwrite($this->file, '  ' . $this->formatter->formatPropertyName($sectionName) . ': ');
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
    public function renderSectionBody(array $section, DataCollection $collection, array $sections = [], string $sectionName = '') : void
    {
        if ($this->isSilent()) {
            return;
        }

        $division          = $collection->getDefaultProperties();
        $ua                = $division->getUserAgents()[0];
        $defaultproperties = $ua->getProperties();
        $properties        = array_merge(['Parent'], array_keys($defaultproperties));

        foreach ($defaultproperties as $propertyName => $propertyValue) {
            if (is_bool($propertyValue)) {
                $defaultproperties[$propertyName] = $propertyValue;
            } else {
                $defaultproperties[$propertyName] = $this->trimProperty->trimProperty((string) $propertyValue);
            }
        }

        $propertiesToOutput = [];

        foreach ($properties as $property) {
            if (!isset($section[$property])) {
                continue;
            }

            if (!isset($this->outputProperties[$property])) {
                $this->outputProperties[$property] = $this->filter->isOutputProperty($property, $this);
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

            $propertiesToOutput[$property] = $section[$property];
        }

        fwrite(
            $this->file,
            $this->formatter->formatPropertyValue(json_encode($propertiesToOutput), 'Comment')
        );
    }

    /**
     * renders the footer for a section
     *
     * @param string $sectionName
     *
     * @return void
     */
    public function renderSectionFooter(string $sectionName = '') : void
    {
        if ($this->isSilent()) {
            return;
        }

        if ('*' !== $sectionName) {
            fwrite($this->file, ',');
        }

        fwrite($this->file, PHP_EOL);
    }

    /**
     * renders the footer for a division
     *
     * @return void
     */
    public function renderDivisionFooter() : void
    {
        // nothing to do here
    }

    /**
     * renders the footer for all divisions
     *
     * @return void
     */
    public function renderAllDivisionsFooter() : void
    {
        // nothing to do here
    }
}
