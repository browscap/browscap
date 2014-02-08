<?php

namespace Browscap\Parser;

interface ParserInterface
{
    /**
     * @return array
     */
    public function parse();

    /**
     * @return array
     */
    public function getParsed();

    /**
     * @return string
     */
    public function getFilename();
}
