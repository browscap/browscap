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
use function implode;
use function in_array;
use function sprintf;

use const PHP_EOL;

/**
 * This writer is responsible to create the browscap.csv files
 */
class CsvWriter implements WriterInterface
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
        return WriterInterface::TYPE_CSV;
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
        // nothing to do here
    }

    /**
     * Generates a end sequence for the output file
     */
    public function fileEnd(): void
    {
        // nothing to do here
    }

    /**
     * Generate the header
     *
     * @param array<string> $comments
     */
    public function renderHeader(array $comments = []): void
    {
        // nothing to do here
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

        fwrite($this->file, '"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL);

        if (! isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (! isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        fwrite($this->file, '"' . $versionData['version'] . '","' . $versionData['released'] . '"' . PHP_EOL);
    }

    /**
     * renders the header for all divisions
     */
    public function renderAllDivisionsHeader(DataCollection $collection): void
    {
        $division = $collection->getDefaultProperties();
        $ua       = $division->getUserAgents()[0];

        if (empty($ua->getProperties())) {
            return;
        }

        $defaultproperties = $ua->getProperties();
        $properties        = array_merge(
            ['PropertyName', 'MasterParent', 'LiteMode', 'Parent'],
            array_keys($defaultproperties)
        );

        $values = [];

        foreach ($properties as $property) {
            if (! isset($this->outputProperties[$property])) {
                $this->outputProperties[$property] = $this->filter->isOutputProperty((string) $property, $this);
            }

            if (! $this->outputProperties[$property]) {
                continue;
            }

            $values[] = $this->formatter->formatPropertyName((string) $property);
        }

        fwrite($this->file, implode(',', $values) . PHP_EOL);
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
        // nothing to do here
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
        $properties        = array_merge(
            ['PropertyName', 'MasterParent', 'LiteMode', 'Parent'],
            array_keys($defaultproperties)
        );

        $section['PropertyName'] = $sectionName;
        $section['MasterParent'] = $this->detectMasterParent($sectionName, $section);
        $section['LiteMode']     = (! isset($section['lite']) || ! $section['lite'] ? 'false' : 'true');

        $values = [];

        foreach ($properties as $property) {
            if (! isset($this->outputProperties[$property])) {
                $this->outputProperties[$property] = $this->filter->isOutputProperty($property, $this);
            }

            if (! $this->outputProperties[$property]) {
                continue;
            }

            if (isset($section[$property])) {
                $value = $section[$property];
            } else {
                $value = '';
            }

            $values[] = $this->formatter->formatPropertyValue($value, $property);
        }

        fwrite($this->file, implode(',', $values) . PHP_EOL);
    }

    /**
     * renders the footer for a section
     */
    public function renderSectionFooter(string $sectionName = ''): void
    {
        // nothing to do here
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
        // nothing to do here
    }

    /**
     * @param array<string, bool|int|string> $properties
     */
    private function detectMasterParent(string $key, array $properties): string
    {
        $this->logger->debug('check if the element can be marked as "MasterParent"');

        if (
            in_array($key, ['DefaultProperties', '*'])
            || empty($properties['Parent'])
            || $properties['Parent'] === 'DefaultProperties'
        ) {
            return 'true';
        }

        return 'false';
    }
}
