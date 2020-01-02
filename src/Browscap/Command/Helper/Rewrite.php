<?php
declare(strict_types = 1);
namespace Browscap\Command\Helper;

use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer\Printer;
use ExceptionalJSON\DecodeErrorException;
use ExceptionalJSON\EncodeErrorException;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Finder\Finder;

class Rewrite extends Helper
{
    public function getName() : string
    {
        return 'rewrite';
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param string                   $resources
     * @param string                   $schema
     * @param bool                     $sort
     */
    public function rewrite(LoggerInterface $logger, string $resources, string $schema, bool $sort = false) : void
    {
        $normalizer = new Normalizer\SchemaNormalizer(
            $schema,
            new SchemaStorage(),
            new Normalizer\Validator\SchemaValidator(new Validator())
        );
        $format = new Normalizer\Format\Format(
            Normalizer\Format\JsonEncodeOptions::fromInt(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            Normalizer\Format\Indent::fromSizeAndStyle(2, 'space'),
            Normalizer\Format\NewLine::fromString("\n"),
            true
        );
        $printer   = new Printer();
        $formatter = new Normalizer\Format\Formatter($printer);

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
            } catch (\RuntimeException $e) {
                $logger->critical(new \Exception(sprintf('could not read file "%s"', $file->getPathname()), 0, $e));

                continue;
            }

            if ($sort) {
                /** @var \Browscap\Command\Helper\Sorter $sorterHelper */
                $sorterHelper = $this->helperSet->get('sorter');

                try {
                    $json = $sorterHelper->sort($json);
                } catch (DecodeErrorException | EncodeErrorException $e) {
                    $logger->critical(new \Exception(sprintf('file "%s" is not valid', $file->getPathname()), 0, $e));

                    continue;
                }
            }

            try {
                $normalized = (new Normalizer\FixedFormatNormalizer($normalizer, $format, $formatter))->normalize(Normalizer\Json::fromEncoded($json));
            } catch (\Throwable $e) {
                $logger->critical(new \Exception(sprintf('file "%s" is not valid', $file->getPathname()), 0, $e));

                continue;
            }

            file_put_contents($file->getPathname(), $normalized);
        }
    }
}
