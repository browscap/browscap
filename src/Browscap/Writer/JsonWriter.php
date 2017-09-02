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
use Browscap\Data\Expander;
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

class JsonWriter implements WriterInterface, WriterNeedsExpanderInterface
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
     * @var FormatterInterface
     */
    private $formatter = null;

    /**
     * @var FilterInterface
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
     * @param string                   $file
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct($file, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->file   = fopen($file, 'w');
    }

    /**
     * returns the Type of the writer
     *
     * @return string
     */
    public function getType()
    {
        return 'json';
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
    public function setSilent(bool $silent)
    {
        $this->silent = $silent;

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
        if ($this->isSilent()) {
            return $this;
        }

        fwrite($this->file, '{' . PHP_EOL);

        return $this;
    }

    /**
     * Generates a end sequence for the output file
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function fileEnd()
    {
        if ($this->isSilent()) {
            return $this;
        }

        fwrite($this->file, '}' . PHP_EOL);

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

        fwrite($this->file, '  ' . $this->getFormatter()->formatPropertyName($sectionName) . ': ');

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
     * @return JsonWriter
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
            if (is_bool($propertyValue)) {
                $defaultproperties[$propertyName] = $propertyValue;
            } else {
                $defaultproperties[$propertyName] = $this->expander->trimProperty((string) $propertyValue);
            }
        }

        $propertiesToOutput = [];

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

            $propertiesToOutput[$property] = $section[$property];
        }

        fwrite(
            $this->file,
            $this->getFormatter()->formatPropertyValue(json_encode($propertiesToOutput), 'Comment')
        );

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

        if ('*' !== $sectionName) {
            fwrite($this->file, ',');
        }

        fwrite($this->file, PHP_EOL);

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
