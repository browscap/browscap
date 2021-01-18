<?php

declare(strict_types=1);

namespace Browscap\Helper;

use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

use function array_key_exists;
use function assert;

class IteratorHelper
{
    /**
     * @return array<array<string>>
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
                if (isset($data[$key])) {
                    $error = 'Test data is duplicated for key "' . $key . '"';

                    $logger->error($error);
                    $errors[] = $error;
                }

                if (! array_key_exists($testKey, $test)) {
                    $error = '"' . $testKey . '" keyword is missing for  key "' . $key . '"';

                    $logger->error($error);
                    $errors[] = $error;
                }

                if (! $test[$testKey]) {
                    continue;
                }

                if (isset($checks[$test['ua']])) {
                    $error = 'UA "' . $test['ua'] . '" added more than once, now for key "' . $key . '", before for key "'
                        . $checks[$test['ua']] . '"';

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
