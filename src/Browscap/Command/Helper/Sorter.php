<?php
declare(strict_types = 1);
namespace Browscap\Command\Helper;

use JsonClass\Json;
use Symfony\Component\Console\Helper\Helper;

class Sorter extends Helper
{
    public function getName() : string
    {
        return 'sorter';
    }

    /**
     * @param string $json
     *
     * @throws \ExceptionalJSON\DecodeErrorException when the decode operation fails
     * @throws \ExceptionalJSON\EncodeErrorException When the encode operation fails
     *
     * @return string
     */
    public function sort(string $json) : string
    {
        $jsonClass = new Json();

        $data = $jsonClass->decode($json, true);

        ksort($data);

        return $jsonClass->encode($data);
    }
}
