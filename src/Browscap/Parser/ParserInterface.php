<?php

namespace Browscap\Parser;

interface ParserInterface
{
    public function parse();

    public function getParsed();

    public function getFilename();
}
