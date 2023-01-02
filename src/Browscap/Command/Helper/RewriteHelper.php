<?php

declare(strict_types=1);

namespace Browscap\Command\Helper;

use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Normalizer\Exception\InvalidIndentSize;
use Ergebnis\Json\Normalizer\Exception\InvalidIndentStyle;
use Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodeOptions;
use Ergebnis\Json\Normalizer\Exception\InvalidNewLineString;
use Ergebnis\Json\Printer\Printer;
use Ergebnis\Json\SchemaValidator\SchemaValidator;
use JsonException;
use JsonSchema\SchemaStorage;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Throwable;

use function assert;
use function file_put_contents;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class RewriteHelper extends Helper
{
    /** @throws void */
    public function getName(): string
    {
        return 'rewrite';
    }

    /**
     * @throws DirectoryNotFoundException
     * @throws InvalidNewLineString
     * @throws InvalidIndentStyle
     * @throws InvalidIndentSize
     * @throws InvalidJsonEncodeOptions
     */
    public function rewrite(LoggerInterface $logger, string $resources, string $schema, bool $sort = false): void
    {
        $normalizer = new Normalizer\SchemaNormalizer(
            $schema,
            new SchemaStorage(),
            new SchemaValidator(),
        );
        $format     = Normalizer\Format\Format::create(
            Normalizer\Format\JsonEncodeOptions::fromInt(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            Normalizer\Format\Indent::fromSizeAndStyle(2, 'space'),
            Normalizer\Format\NewLine::fromString("\n"),
            true,
        );
        $printer    = new Printer();
        $formatter  = new Normalizer\Format\DefaultFormatter($printer);

        $finder = new Finder();
        $finder->files();
        $finder->name('*.json');
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);
        $finder->sortByName();
        $finder->ignoreUnreadableDirs();
        $finder->in($resources);

        foreach ($finder as $file) {
            $logger->info('read source file ' . $file->getPathname());

            try {
                $json = $file->getContents();
            } catch (RuntimeException $e) {
                $logger->critical(
                    'File "{File}" is not readable',
                    [
                        'File' => $file->getPathname(),
                        'Exception' => $e,
                    ],
                );

                continue;
            }

            if ($sort) {
                $sorterHelper = $this->helperSet->get('sorter');
                assert($sorterHelper instanceof Sorter);

                try {
                    $json = $sorterHelper->sort($json);
                } catch (JsonException $e) {
                    $logger->critical(
                        'File "{File}" had invalid JSON.',
                        [
                            'File' => $file->getPathname(),
                            'Exception' => $e,
                        ],
                    );

                    continue;
                }
            }

            try {
                $normalized = (new Normalizer\FixedFormatNormalizer($normalizer, $format, $formatter))->normalize(Normalizer\Json::fromEncoded($json));
            } catch (Throwable $e) {
                $logger->critical(
                    'File "{File}" is not valid',
                    [
                        'File' => $file->getPathname(),
                        'Exception' => $e,
                    ],
                );

                continue;
            }

            file_put_contents($file->getPathname(), $normalized);
        }
    }
}
