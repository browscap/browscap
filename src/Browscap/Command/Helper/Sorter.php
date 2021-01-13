<?php

declare(strict_types=1);

namespace Browscap\Command\Helper;

use ExceptionalJSON\DecodeErrorException;
use ExceptionalJSON\EncodeErrorException;
use JsonClass\Json;
use Symfony\Component\Console\Helper\Helper;

use function ksort;

class Sorter extends Helper
{
    public function getName(): string
    {
        return 'sorter';
    }

    /**
     * @throws DecodeErrorException when the decode operation fails.
     * @throws EncodeErrorException When the encode operation fails.
     */
    public function sort(string $json): string
    {
        $jsonClass = new Json();

        $data = $jsonClass->decode($json, true);

        ksort($data);

        return $jsonClass->encode($data);
    }
}
