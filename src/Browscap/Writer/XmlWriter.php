<?php
declare(strict_types = 1);
namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use Psr\Log\LoggerInterface;

/**
 * This writer is responsible to create the browscap.xml files
 */
class XmlWriter implements WriterInterface
{
    /**
     * @var LoggerInterface
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
     * @param string          $file
     * @param LoggerInterface $logger
     */
    public function __construct(string $file, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $ressource    = fopen($file, 'wb');

        if (false === $ressource) {
            throw new \InvalidArgumentException("An error occured while opening File: {$file}");
        }

        $this->file = $ressource;
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
     */
    public function close() : void
    {
        fclose($this->file);
    }

    /**
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter) : void
    {
        $this->formatter = $formatter;
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter() : FormatterInterface
    {
        return $this->formatter;
    }

    /**
     * @param FilterInterface $filter
     */
    public function setFilter(FilterInterface $filter) : void
    {
        $this->filter           = $filter;
        $this->outputProperties = [];
    }

    /**
     * @return FilterInterface
     */
    public function getFilter() : FilterInterface
    {
        return $this->filter;
    }

    /**
     * @param bool $silent
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
     * @param DataCollection $collection
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
     */
    public function renderDivisionHeader(string $division, string $parent = 'DefaultProperties') : void
    {
        // nothing to do here
    }

    /**
     * renders the header for a section
     *
     * @param string $sectionName
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
     * @param array          $section
     * @param DataCollection $collection
     * @param array[]        $sections
     * @param string         $sectionName
     *
     * @throws \InvalidArgumentException
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
     */
    public function renderDivisionFooter() : void
    {
        // nothing to do here
    }

    /**
     * renders the footer for all divisions
     */
    public function renderAllDivisionsFooter() : void
    {
        fwrite($this->file, '</browsercapitems>' . PHP_EOL);
    }
}
