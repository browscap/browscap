<?php

declare(strict_types=1);

namespace Browscap\Command\Helper;

use Exception;
use JsonException;
use JsonSchema\Constraints;
use JsonSchema\SchemaStorage;
use JsonSchema\Uri;
use JsonSchema\Validator;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use stdClass;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function assert;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class ValidateHelper extends Helper
{
    /** @throws void */
    public function getName(): string
    {
        return 'validate';
    }

    /** @throws void */
    public function validate(LoggerInterface $logger, string $resources, string $schemaUri, string|array|null $notPath = null): bool
    {
        $uriRetriever  = new Uri\UriRetriever();
        $schemaStorage = new SchemaStorage(
            $uriRetriever,
            new Uri\UriResolver(),
        );

        $validator = new Validator(new Constraints\Factory(
            $schemaStorage,
            $uriRetriever,
        ));

        $schemaDecoded = $schemaStorage->getSchema($schemaUri);

        assert($schemaDecoded instanceof stdClass || $schemaDecoded === null);

        if ($schemaDecoded === null) {
            $logger->critical('the given json schema is invalid');

            return true;
        }

        $failed     = false;
        $jsonParser = new JsonParser();

        $finder = new Finder();
        $finder->files();
        $finder->name('*.json');
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);
        $finder->sortByName();
        $finder->ignoreUnreadableDirs();

        if ($notPath !== null) {
            $finder->notPath($notPath);
        }

        try {
            $finder->in($resources);
        } catch (DirectoryNotFoundException $exception) {
            $logger->critical(new Exception('the resource directory was not found', 0, $exception));

            return true;
        }

        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $logger->info(sprintf('source file %s: read', $file->getPathname()));

            try {
                $json = $file->getContents();
            } catch (RuntimeException $e) {
                $logger->critical(
                    sprintf('File "%s" is not readable', $file->getPathname()),
                    ['Exception' => $e],
                );
                $failed = true;

                continue;
            }

            try {
                $logger->debug(sprintf('source file %s: validate', $file->getPathname()));

                $jsonDecoded = json_decode(
                    $json,
                    false,
                    512,
                    JSON_THROW_ON_ERROR,
                );

                $validator->validate(
                    $jsonDecoded,
                    $schemaDecoded,
                );

                /** @var array<int, array> $errors */
                $errors = $validator->getErrors();

                if ($errors !== []) {
                    $logger->critical(
                        sprintf('File "%s" is not valid', $file->getPathname()),
                        ['errors' => $errors],
                    );
                    $failed = true;
                }
            } catch (JsonException $e) {
                $logger->critical(
                    sprintf('validating File "%s" failed, because it had invalid JSON.', $file->getPathname()),
                    ['Exception' => $e],
                );
                $failed = true;

                continue;
            }

            try {
                $logger->debug(sprintf('source file %s: parse with json parser', $file->getPathname()));

                $jsonParser->parse($json, JsonParser::DETECT_KEY_CONFLICTS);
            } catch (ParsingException $e) {
                $logger->critical(
                    sprintf('parsing File "%s" failed, because it had invalid JSON.', $file->getPathname()),
                    ['Exception' => $e],
                );
                $failed = true;
            }
        }

        return $failed;
    }
}
