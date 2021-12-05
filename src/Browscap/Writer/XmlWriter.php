<?php

declare(strict_types=1);

namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

use function array_keys;
use function array_merge;
use function fclose;
use function fopen;
use function fwrite;
use function sprintf;

use const PHP_EOL;

/**
 * This writer is responsible to create the browscap.xml files
 */
class XmlWriter implements WriterInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var resource */
    private $file;

    /** @var FormatterInterface */
    private $formatter;

    /** @var FilterInterface */
    private $filter;

    /** @var bool */
    private $silent = false;

    /** @var bool[] */
    private $outputProperties = [];

    public function __construct(string $file, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $ressource    = fopen($file, 'w');

        if ($ressource === false) {
            throw new InvalidArgumentException(sprintf('An error occured while opening File: %s', $file));
        }

        $this->file = $ressource;
    }

    /**
     * returns the Type of the writer
     */
    public function getType(): string
    {
        return WriterInterface::TYPE_XML;
    }

    /**
     * closes the Writer and the written File
     */
    public function close(): void
    {
        fclose($this->file);
    }

    public function setFormatter(FormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    public function getFormatter(): FormatterInterface
    {
        return $this->formatter;
    }

    public function setFilter(FilterInterface $filter): void
    {
        $this->filter           = $filter;
        $this->outputProperties = [];
    }

    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

    public function setSilent(bool $silent): void
    {
        $this->silent = $silent;
    }

    public function isSilent(): bool
    {
        return $this->silent;
    }

    /**
     * Generates a start sequence for the output file
     */
    public function fileStart(): void
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
    public function fileEnd(): void
    {
        if ($this->isSilent()) {
            return;
        }

        fwrite($this->file, '</browsercaps>' . PHP_EOL);
    }

    /**
     * Generate the header
     *
     * @param array<string> $comments
     */
    public function renderHeader(array $comments = []): void
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
     * @param array<string> $versionData
     */
    public function renderVersion(array $versionData = []): void
    {
        if ($this->isSilent()) {
            return;
        }

        $this->logger->debug('rendering version information');

        fwrite($this->file, '<gjk_browscap_version>' . PHP_EOL);

        if (! isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (! isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        fwrite($this->file, '<item name="Version" value="' . $this->formatter->formatPropertyName($versionData['version']) . '"/>' . PHP_EOL);
        fwrite($this->file, '<item name="Released" value="' . $this->formatter->formatPropertyName($versionData['released']) . '"/>' . PHP_EOL);

        fwrite($this->file, '</gjk_browscap_version>' . PHP_EOL);
    }

    /**
     * renders the header for all divisions
     */
    public function renderAllDivisionsHeader(DataCollection $collection): void
    {
        fwrite($this->file, '<browsercapitems>' . PHP_EOL);
    }

    /**
     * renders the header for a division
     */
    public function renderDivisionHeader(string $division, string $parent = 'DefaultProperties'): void
    {
        // nothing to do here
    }

    /**
     * renders the header for a section
     */
    public function renderSectionHeader(string $sectionName): void
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
     * @param array<string, int|string|true>            $section
     * @param array<string, array<string, bool|string>> $sections
     *
     * @throws InvalidArgumentException
     */
    public function renderSectionBody(array $section, DataCollection $collection, array $sections = [], string $sectionName = ''): void
    {
        if ($this->isSilent()) {
            return;
        }

        $division          = $collection->getDefaultProperties();
        $ua                = $division->getUserAgents()[0];
        $defaultproperties = $ua->getProperties();
        $properties        = array_merge(['Parent'], array_keys($defaultproperties));

        foreach ($properties as $property) {
            if (! isset($section[$property])) {
                continue;
            }

            if (! isset($this->outputProperties[$property])) {
                $this->outputProperties[$property] = $this->filter->isOutputProperty($property, $this);
            }

            if (! $this->outputProperties[$property]) {
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
     */
    public function renderSectionFooter(string $sectionName = ''): void
    {
        if ($this->isSilent()) {
            return;
        }

        fwrite($this->file, '</browscapitem>' . PHP_EOL);
    }

    /**
     * renders the footer for a division
     */
    public function renderDivisionFooter(): void
    {
        // nothing to do here
    }

    /**
     * renders the footer for all divisions
     */
    public function renderAllDivisionsFooter(): void
    {
        fwrite($this->file, '</browsercapitems>' . PHP_EOL);
    }
}
