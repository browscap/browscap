<?php

declare(strict_types=1);

namespace Browscap\Helper;

use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use UnexpectedValueException;

use function array_key_exists;
use function assert;
use function is_string;
use function sprintf;

class IteratorHelper
{
    /**
     * @phpstan-param 'full'|'standard'|'lite' $testKey
     *
     * @return array<array<string>>
     * @phpstan-return array{0: array<string, array{ua: string, properties: array<string, string|int|bool>, lite: bool, standard: bool, full: bool}>, 1: array<string, string>}
     *
     * @throws UnexpectedValueException
     */
    public function getTestFiles(LoggerInterface $logger, string $testKey = 'full'): array
    {
        $data = [];

        $checks          = [];
        $sourceDirectory = __DIR__ . '/../../../tests/issues/';
        $iterator        = new RecursiveDirectoryIterator($sourceDirectory);

        $errors = [];

        foreach (new RecursiveIteratorIterator($iterator) as $file) {
            assert($file instanceof SplFileInfo);
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $tests = require $file->getPathname();

            foreach ($tests as $key => $test) {
                assert(is_string($key));

                if (array_key_exists($key, $data)) {
                    $error = sprintf('Test data is duplicated for key "%s"', $key);

                    $logger->error($error);
                    $errors[] = $error;
                }

                if (! array_key_exists($testKey, $test)) {
                    $error = sprintf('"%s" keyword is missing for  key "%s"', $testKey, $key);

                    $logger->error($error);
                    $errors[] = $error;
                }

                if (! $test[$testKey]) {
                    continue;
                }

                if (isset($checks[$test['ua']])) {
                    $error = sprintf(
                        'UA "%s" added more than once, now for key "%s", before for key "%s"',
                        $test['ua'],
                        $key,
                        $checks[$test['ua']]
                    );

                    $logger->error($error);
                    $errors[] = $error;
                }

                $data[$key]          = $test;
                $checks[$test['ua']] = $key;
            }
        }

        return [$data, $errors];
    }
}
