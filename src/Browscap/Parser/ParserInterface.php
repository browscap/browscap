<?php
declare(strict_types=1);

namespace Browscap\Parser;

interface ParserInterface
{
    /**
     * @return array
     */
    public function parse(): array;

    /**
     * @return array
     */
    public function getParsed(): array;

    /**
     * @return string
     */
    public function getFilename(): string;
}
