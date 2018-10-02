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
