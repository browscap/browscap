<?php

declare(strict_types=1);

namespace Browscap\Command\Helper;

use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Normalizer\Exception\InvalidIndentSizeException;
use Ergebnis\Json\Normalizer\Exception\InvalidIndentStyleException;
use Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodeOptionsException;
use Ergebnis\Json\Normalizer\Exception\InvalidNewLineStringException;
use Ergebnis\Json\Printer\Printer;
use Exception;
use JsonException;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
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
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class RewriteHelper extends Helper
{
    /**
     * @throws void
     */
    public function getName(): string
    {
        return 'rewrite';
    }

    /**
     * @throws DirectoryNotFoundException
     * @throws InvalidNewLineStringException
     * @throws InvalidIndentStyleException
     * @throws InvalidIndentSizeException
     * @throws InvalidJsonEncodeOptionsException
     */
    public function rewrite(LoggerInterface $logger, string $resources, string $schema, bool $sort = false): void
    {
        $normalizer = new Normalizer\SchemaNormalizer(
            $schema,
            new SchemaStorage(),
            new Normalizer\Validator\SchemaValidator(new Validator())
        );
        $format     = new Normalizer\Format\Format(
            Normalizer\Format\JsonEncodeOptions::fromInt(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            Normalizer\Format\Indent::fromSizeAndStyle(2, 'space'),
            Normalizer\Format\NewLine::fromString("\n"),
            true
        );
        $printer    = new Printer();
        $formatter  = new Normalizer\Format\Formatter($printer);

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
                $logger->critical(new Exception(sprintf('could not read file "%s"', $file->getPathname()), 0, $e));

                continue;
            }

            if ($sort) {
                $sorterHelper = $this->helperSet->get('sorter');
                assert($sorterHelper instanceof Sorter);

                try {
                    $json = $sorterHelper->sort($json);
                } catch (JsonException $e) {
                    $logger->critical(new Exception(sprintf('file "%s" is not valid', $file->getPathname()), 0, $e));

                    continue;
                }
            }

            try {
                $normalized = (new Normalizer\FixedFormatNormalizer($normalizer, $format, $formatter))->normalize(Normalizer\Json::fromEncoded($json));
            } catch (Throwable $e) {
                $logger->critical(new Exception(sprintf('file "%s" is not valid', $file->getPathname()), 0, $e));

                continue;
            }

            file_put_contents($file->getPathname(), $normalized);
        }
    }
}
