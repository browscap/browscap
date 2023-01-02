<?php

declare(strict_types=1);

namespace Browscap\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\Helper\TrimProperty;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use Exception;
use InvalidArgumentException;
use JsonException;
use Psr\Log\LoggerInterface;

use function array_keys;
use function array_merge;
use function count;
use function fclose;
use function fopen;
use function fwrite;
use function is_bool;
use function json_encode;
use function sprintf;

use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

/**
 * This writer is responsible to create the browscap.json files
 */
class JsonWriter implements WriterInterface
{
    /** @var resource */
    private $file;

    private FormatterInterface $formatter;

    private FilterInterface $filter;

    private bool $silent = false;

    /** @var bool[] */
    private array $outputProperties = [];

    private TrimProperty $trimProperty;

    /** @throws InvalidArgumentException */
    public function __construct(string $file, private LoggerInterface $logger)
    {
        $this->trimProperty = new TrimProperty();
        $ressource          = fopen($file, 'w');

        if ($ressource === false) {
            throw new InvalidArgumentException(sprintf('An error occured while opening File: %s', $file));
        }

        $this->file = $ressource;
    }

    /**
     * returns the Type of the writer
     *
     * @throws void
     */
    public function getType(): string
    {
        return WriterInterface::TYPE_JSON;
    }

    /**
     * closes the Writer and the written File
     *
     * @throws void
     */
    public function close(): void
    {
        fclose($this->file);
    }

    /** @throws void */
    public function setFormatter(FormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    /** @throws void */
    public function getFormatter(): FormatterInterface
    {
        return $this->formatter;
    }

    /** @throws void */
    public function setFilter(FilterInterface $filter): void
    {
        $this->filter           = $filter;
        $this->outputProperties = [];
    }

    /** @throws void */
    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

    /** @throws void */
    public function setSilent(bool $silent): void
    {
        $this->silent = $silent;
    }

    /** @throws void */
    public function isSilent(): bool
    {
        return $this->silent;
    }

    /**
     * Generates a start sequence for the output file
     *
     * @throws void
     */
    public function fileStart(): void
    {
        if ($this->isSilent()) {
            return;
        }

        fwrite($this->file, '{' . PHP_EOL);
    }

    /**
     * Generates a end sequence for the output file
     *
     * @throws void
     */
    public function fileEnd(): void
    {
        if ($this->isSilent()) {
            return;
        }

        fwrite($this->file, '}' . PHP_EOL);
    }

    /**
     * Generate the header
     *
     * @param array<string> $comments
     *
     * @throws JsonException
     */
    public function renderHeader(array $comments = []): void
    {
        if ($this->isSilent()) {
            return;
        }

        $this->logger->debug('rendering comments');

        fwrite($this->file, '  "comments": [' . PHP_EOL);

        foreach ($comments as $i => $text) {
            fwrite($this->file, '    ' . json_encode($text, JSON_THROW_ON_ERROR));

            if ($i < count($comments) - 1) {
                fwrite($this->file, ',');
            }

            fwrite($this->file, PHP_EOL);
        }

        fwrite($this->file, '  ],' . PHP_EOL);
    }

    /**
     * renders the version information
     *
     * @param array<string> $versionData
     *
     * @throws JsonException
     */
    public function renderVersion(array $versionData = []): void
    {
        if ($this->isSilent()) {
            return;
        }

        $this->logger->debug('rendering version information');

        fwrite($this->file, '  "GJK_Browscap_Version": {' . PHP_EOL);

        if (! isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (! isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        fwrite($this->file, '    "Version": ' . json_encode($versionData['version'], JSON_THROW_ON_ERROR) . ',' . PHP_EOL);
        fwrite($this->file, '    "Released": ' . json_encode($versionData['released'], JSON_THROW_ON_ERROR) . '' . PHP_EOL);

        fwrite($this->file, '  },' . PHP_EOL);
    }

    /**
     * renders the header for all divisions
     *
     * @throws void
     */
    public function renderAllDivisionsHeader(DataCollection $collection): void
    {
        // nothing to do here
    }

    /**
     * renders the header for a division
     *
     * @throws void
     */
    public function renderDivisionHeader(string $division, string $parent = 'DefaultProperties'): void
    {
        // nothing to do here
    }

    /**
     * renders the header for a section
     *
     * @throws JsonException
     */
    public function renderSectionHeader(string $sectionName): void
    {
        if ($this->isSilent()) {
            return;
        }

        fwrite($this->file, '  ' . $this->formatter->formatPropertyName($sectionName) . ': ');
    }

    /**
     * renders all found useragents into a string
     *
     * @param array<string, int|string|true>            $section
     * @param array<string, array<string, bool|string>> $sections
     *
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws JsonException
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

        foreach ($defaultproperties as $propertyName => $propertyValue) {
            if (is_bool($propertyValue)) {
                $defaultproperties[$propertyName] = $propertyValue;
            } else {
                $defaultproperties[$propertyName] = $this->trimProperty->trim((string) $propertyValue);
            }
        }

        $propertiesToOutput = [];

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

            if (isset($section['Parent']) && $property !== 'Parent') {
                if (
                    $section['Parent'] === 'DefaultProperties'
                    || ! isset($sections[$section['Parent']])
                ) {
                    if (
                        isset($defaultproperties[$property])
                        && $defaultproperties[$property] === $section[$property]
                    ) {
                        continue;
                    }
                } else {
                    $parentProperties = $sections[$section['Parent']];

                    if (
                        isset($parentProperties[$property])
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
            $this->formatter->formatPropertyValue(json_encode($propertiesToOutput, JSON_THROW_ON_ERROR), 'Comment'),
        );
    }

    /**
     * renders the footer for a section
     *
     * @throws void
     */
    public function renderSectionFooter(string $sectionName = ''): void
    {
        if ($this->isSilent()) {
            return;
        }

        if ($sectionName !== '*') {
            fwrite($this->file, ',');
        }

        fwrite($this->file, PHP_EOL);
    }

    /**
     * renders the footer for a division
     *
     * @throws void
     */
    public function renderDivisionFooter(): void
    {
        // nothing to do here
    }

    /**
     * renders the footer for all divisions
     *
     * @throws void
     */
    public function renderAllDivisionsFooter(): void
    {
        // nothing to do here
    }
}
