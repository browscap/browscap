<?php

namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BrowscapXmlGenerator
 *
 * @package Browscap\Generator
 */
class XmlWriter implements WriterInterface
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
     * @var boolean
     */
    private $silent = false;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = fopen($file, 'w');
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
        $this->type = $filter;

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
     * @param boolean $silent
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setSilent($silent)
    {
        $this->silent = (boolean) $silent;

        return $this;
    }

    /**
     * @return boolean
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

        fputs($this->file, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fputs($this->file, '<browsercaps>' . PHP_EOL);

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

        fputs($this->file, '</browsercaps>' . PHP_EOL);

        return $this;
    }

    /**
     * Generate the header
     *
     * @param string[] $comments
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderHeader(array $comments = array())
    {
        if ($this->isSilent()) {
            return $this;
        }

        $this->getLogger()->debug('rendering comments');

        fputs($this->file, '<comments>' . PHP_EOL);

        foreach ($comments as $text) {
            fputs($this->file, '<comment><![CDATA[' . $text . ']]></comment>' . PHP_EOL);
        }

        fputs($this->file, '</comments>' . PHP_EOL);

        return $this;
    }

    /**
     * renders the version information
     *
     * @param string[] $versionData
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderVersion(array $versionData = array())
    {
        if ($this->isSilent()) {
            return $this;
        }

        $this->getLogger()->debug('rendering version information');

        fputs($this->file, '<gjk_browscap_version>' . PHP_EOL);

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        fputs($this->file, '<item name="Version" value="' . $versionData['version'] . '"/>' . PHP_EOL);
        fputs($this->file, '<item name="Released" value="' . $versionData['released'] . '"/>' . PHP_EOL);

        fputs($this->file, '</gjk_browscap_version>' . PHP_EOL);

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
        fputs($this->file, '<browsercapitems>' . PHP_EOL);

        return $this;
    }

    /**
     * renders the header for a division
     *
     * @param string $division
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderDivisionHeader($division)
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

        fputs($this->file, '<browscapitem name="' . $this->getFormatter()->formatPropertyName($sectionName) . '">' . PHP_EOL);

        return $this;
    }

    /**
     * renders all found useragents into a string
     *
     * @param string[]                      $section
     * @param \Browscap\Data\DataCollection $collection
     * @param array[]                       $sections
     *
     * @throws \InvalidArgumentException
     * @return \Browscap\Writer\WriterCollection
     */
    public function renderSectionBody(array $section, DataCollection $collection = null, array $sections = array())
    {
        if ($this->isSilent()) {
            return $this;
        }

        foreach ($section as $property => $value) {
            if (!$this->getFilter()->isOutputProperty($property)) {
                continue;
            }

            fputs($this->file, '<item name="' . $this->getFormatter()->formatPropertyName($property)  . '" value="' . $this->getFormatter()->formatPropertyValue($value, $property) . '"/>' . PHP_EOL);
        }

        return $this;
    }

    /**
     * renders the footer for a section
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function renderSectionFooter()
    {
        if ($this->isSilent()) {
            return $this;
        }

        fputs($this->file, '</browscapitem>' . PHP_EOL);

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
        fputs($this->file, '</browsercapitems>' . PHP_EOL);

        return $this;
    }
}
