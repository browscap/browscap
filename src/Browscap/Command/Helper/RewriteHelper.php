<?php

declare(strict_types=1);

namespace Browscap\Command\Helper;

use Ergebnis\Json;
use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Normalizer\Exception\InvalidIndentSize;
use Ergebnis\Json\Normalizer\Exception\InvalidIndentStyle;
use Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodeOptions;
use Ergebnis\Json\Normalizer\Exception\InvalidNewLineString;
use Ergebnis\Json\Pointer;
use Ergebnis\Json\Printer\Printer;
use Ergebnis\Json\SchemaValidator\SchemaValidator;
use Exception;
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
use function sprintf;

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
     * @throws InvalidNewLineString
     * @throws InvalidIndentStyle
     * @throws InvalidIndentSize
     * @throws InvalidJsonEncodeOptions
     */
    public function rewrite(LoggerInterface $logger, string $resources, string $schema, bool $sort = false): void
    {
        $logger->debug('initialize rewrite helper');

        $normalizer = new Normalizer\SchemaNormalizer(
            $schema,
            new SchemaStorage(),
            new SchemaValidator(),
            Pointer\Specification::never(),
        );

        $format = Normalizer\Format\Format::create(
            Normalizer\Format\JsonEncodeOptions::fromInt(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            Normalizer\Format\Indent::fromSizeAndStyle(2, 'space'),
            Normalizer\Format\NewLine::fromString("\n"),
            true,
        );

        $logger->debug('initialize file finder');

        $finder = new Finder();
        $finder->files();
        $finder->name('*.json');
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);
        $finder->sortByName();
        $finder->ignoreUnreadableDirs();

        try {
            $finder->in($resources);
        } catch (DirectoryNotFoundException $exception) {
            $logger->critical(new Exception('the resource directory was not found', 0, $exception));

            return;
        }

        foreach ($finder as $file) {
            $logger->info(sprintf('source file %s: read', $file->getPathname()));

            try {
                $json = $file->getContents();
            } catch (RuntimeException $e) {
                $logger->critical(
                    sprintf('File "%s" is not readable', $file->getPathname()),
                    ['Exception' => $e],
                );

                continue;
            }

            if ($sort) {
                $logger->debug(sprintf('source file %s: sort content', $file->getPathname()));

                $sorterHelper = $this->helperSet->get('sorter');
                assert($sorterHelper instanceof Sorter);

                try {
                    $json = $sorterHelper->sort($json);
                } catch (JsonException $e) {
                    $logger->critical(
                        sprintf('sorting File "%s" failed, because it had invalid JSON.', $file->getPathname()),
                        ['Exception' => $e],
                    );

                    continue;
                }
            }

            $logger->debug(sprintf('source file %s: normalize content', $file->getPathname()));

            try {
                $chainNormalizer = new Normalizer\ChainNormalizer(
                    $normalizer,
                    new Normalizer\FormatNormalizer(new Printer(), $format),
                );
                $normalized      = $chainNormalizer->normalize(Json\Json::fromString($json));
            } catch (Throwable $e) {
                $logger->critical(
                    sprintf('normalizing File "%s" failed, because it had invalid JSON.', $file->getPathname()),
                    ['Exception' => $e],
                );

                continue;
            }

            $logger->debug(sprintf('source file %s: write content', $file->getPathname()));

            file_put_contents($file->getPathname(), $normalized->toString());
        }
    }
}
