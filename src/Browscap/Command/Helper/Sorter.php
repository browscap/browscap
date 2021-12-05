<?php

declare(strict_types=1);

namespace Browscap\Command\Helper;

use JsonException;
use Symfony\Component\Console\Helper\Helper;

use function assert;
use function is_array;
use function json_decode;
use function json_encode;
use function ksort;

use const JSON_THROW_ON_ERROR;

class Sorter extends Helper
{
    /**
     * @throws void
     */
    public function getName(): string
    {
        return 'sorter';
    }

    /**
     * @throws JsonException
     */
    public function sort(string $json): string
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        assert(is_array($data));

        ksort($data);

        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
