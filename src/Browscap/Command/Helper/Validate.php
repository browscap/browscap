<?php
declare(strict_types = 1);
namespace Browscap\Command\Helper;

use Ergebnis\Json\Normalizer\Validator\SchemaValidator;
use JsonSchema\Constraints;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use Psr\Log\LoggerInterface;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Finder\Finder;

class Validate extends Helper
{
    public function getName() : string
    {
        return 'validate';
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param string                   $resources
     * @param string                   $schema
     *
     * @return bool
     */
    public function validate(LoggerInterface $logger, string $resources, string $schema) : bool
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
            /** @var \stdClass $schema */
            $schema = $schemaStorage->getSchema($schema);
        } catch (\Throwable $exception) {
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
            /* @var \Symfony\Component\Finder\SplFileInfo $file */
            $logger->info('read source file ' . $file->getPathname());

            try {
                $json = $file->getContents();
            } catch (\RuntimeException $e) {
                $logger->critical(new \Exception(sprintf('file "%s" is not readable', $file->getPathname()), 0, $e));
                $failed = true;

                continue;
            }

            try {
                $decoded = $jsonParser->parse($json, JsonParser::DETECT_KEY_CONFLICTS);

                if (!$schemaValidator->isValid($decoded, $schema)) {
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
