<?php
declare(strict_types = 1);
namespace Browscap\Helper;

use Psr\Log\LoggerInterface;

class IteratorHelper
{
    /**
     * @param LoggerInterface $logger
     * @param string          $testKey
     *
     * @return array
     */
    public function getTestFiles(LoggerInterface $logger, string $testKey = 'full') : array
    {
        $data = [];

        $checks          = [];
        $sourceDirectory = __DIR__ . '/../../../tests/issues/';
        $iterator        = new \RecursiveDirectoryIterator($sourceDirectory);

        $errors = [];

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || 'php' !== $file->getExtension()) {
                continue;
            }

            $tests = require $file->getPathname();

            foreach ($tests as $key => $test) {
                if (isset($data[$key])) {
                    $logger->error('Test data is duplicated for key "' . $key . '"');
                    $errors[] = 'Test data is duplicated for key "' . $key . '"';
                }

                if (!array_key_exists($testKey, $test)) {
                    $logger->error(
                        '"full" keyword is missing for  key "' . $key . '"'
                    );
                    $errors[] = '"full" keyword is missing for  key "' . $key . '"';
                }

                if (!$test[$testKey]) {
                    continue;
                }

                if (isset($checks[$test['ua']])) {
                    $logger->error(
                        'UA "' . $test['ua'] . '" added more than once, now for key "' . $key . '", before for key "'
                        . $checks[$test['ua']] . '"'
                    );
                    $errors[] = 'UA "' . $test['ua'] . '" added more than once, now for key "' . $key . '", before for key "'
                        . $checks[$test['ua']] . '"';
                }

                $data[$key]          = $test;
                $checks[$test['ua']] = $key;
            }
        }

        return [$data, $errors];
    }
}
