<?php
declare(strict_types = 1);
namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use Psr\Log\LoggerInterface;

/**
 * Class XmlWriter
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */

class XmlWriter implements WriterInterface
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
    public function getType() : string
    {
        return WriterInterface::TYPE_XML;
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

        fwrite($this->file, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($this->file, '<browsercaps>' . PHP_EOL);
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

        fwrite($this->file, '</browsercaps>' . PHP_EOL);
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

        fwrite($this->file, '<comments>' . PHP_EOL);

        foreach ($comments as $text) {
            fwrite($this->file, '<comment><![CDATA[' . $text . ']]></comment>' . PHP_EOL);
        }

        fwrite($this->file, '</comments>' . PHP_EOL);
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

        fwrite($this->file, '<gjk_browscap_version>' . PHP_EOL);

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        fwrite($this->file, '<item name="Version" value="' . $this->formatter->formatPropertyName($versionData['version']) . '"/>' . PHP_EOL);
        fwrite($this->file, '<item name="Released" value="' . $this->formatter->formatPropertyName($versionData['released']) . '"/>' . PHP_EOL);

        fwrite($this->file, '</gjk_browscap_version>' . PHP_EOL);
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
        fwrite($this->file, '<browsercapitems>' . PHP_EOL);
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

        fwrite(
            $this->file,
            '<browscapitem name="' . $this->formatter->formatPropertyName($sectionName) . '">' . PHP_EOL
        );
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

            fwrite(
                $this->file,
                '<item name="' . $this->formatter->formatPropertyName($property)
                . '" value="' . $this->formatter->formatPropertyValue($section[$property], $property)
                . '"/>' . PHP_EOL
            );
        }
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

        fwrite($this->file, '</browscapitem>' . PHP_EOL);
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
        fwrite($this->file, '</browsercapitems>' . PHP_EOL);
    }
}
