<?php

declare(strict_types=1);

namespace Browscap\Command\Helper;

use Ergebnis\Json\Normalizer\Validator\SchemaValidator;
use Exception;
use JsonSchema\Constraints;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use stdClass;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

use function assert;
use function sprintf;

class ValidateHelper extends Helper
{
    public function getName(): string
    {
        return 'validate';
    }

    public function validate(LoggerInterface $logger, string $resources, string $schema): bool
    {
        $schemaStorage   = new SchemaStorage();
        $schemaValidator = new SchemaValidator(
            new Validator(
                new Constraints\Factory(
                    $schemaStorage,
                    $schemaStorage->getUriRetriever()
                )
            )
        );

        try {
            $schema = $schemaStorage->getSchema($schema);
            assert($schema instanceof stdClass);
        } catch (Throwable $exception) {
            $logger->critical('the schema file is invalid');

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
        $finder->in($resources);

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

                if (! $schemaValidator->isValid($decoded, $schema)) {
                    $logger->critical(sprintf('file "%s" is not valid', $file->getPathname()));
                    $failed = true;
                }
            } catch (ParsingException $e) {
                $logger->critical('File "' . $file->getPathname() . '" had invalid JSON.');
                $failed = true;
            }
        }

        return $failed;
    }
}
