<?php

declare(strict_types=1);

namespace Browscap\Command\Helper;

use Ergebnis\Json\SchemaValidator;
use Ergebnis\Json\SchemaValidator\Exception\CanNotResolve;
use Ergebnis\Json\SchemaValidator\Exception\InvalidJson;
use Ergebnis\Json\SchemaValidator\Exception\ResolvedToRootSchema;
use Exception;
use JsonException;
use JsonSchema\SchemaStorage;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use stdClass;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

use function assert;
use function json_encode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class ValidateHelper extends Helper
{
    /**
     * @throws void
     */
    public function getName(): string
    {
        return 'validate';
    }

    /**
     * @throws void
     */
    public function validate(LoggerInterface $logger, string $resources, string $schema): bool
    {
        $schemaStorage   = new SchemaStorage();
        $schemaValidator = new SchemaValidator\SchemaValidator();
        $jsonPointer     = SchemaValidator\JsonPointer::empty();

        try {
            $schema = $schemaStorage->getSchema($schema);
            assert($schema instanceof stdClass);

            $schema = SchemaValidator\Json::fromString(json_encode($schema, JSON_THROW_ON_ERROR));
        } catch (Throwable $exception) {
            $logger->critical(new Exception('the schema file is invalid', 0, $exception));

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

        try {
            $finder->in($resources);
        } catch (DirectoryNotFoundException $exception) {
            $logger->critical(new Exception('the resource directory was not found', 0, $exception));

            return true;
        }

        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $logger->info('read source file ' . $file->getPathname());

            try {
                $json = $file->getContents();
            } catch (RuntimeException $e) {
                $logger->critical(new Exception(sprintf('file "%s" is not readable', $file->getPathname()), 0, $e));
                $failed = true;

                continue;
            }

            try {
                $decoded = $jsonParser->parse($json, JsonParser::DETECT_KEY_CONFLICTS);

                assert($decoded instanceof stdClass);

                $decoded = SchemaValidator\Json::fromString(json_encode($decoded, JSON_THROW_ON_ERROR));

                if (! $schemaValidator->validate($decoded, $schema, $jsonPointer)->isValid()) {
                    $logger->critical(sprintf('file "%s" is not valid', $file->getPathname()));
                    $failed = true;
                }
            } catch (ParsingException | JsonException | InvalidJson | CanNotResolve | ResolvedToRootSchema $e) {
                $logger->critical('File "' . $file->getPathname() . '" had invalid JSON.');
                $failed = true;
            }
        }

        return $failed;
    }
}
