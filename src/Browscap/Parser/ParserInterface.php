<?php

namespace Browscap\Parser;

/**
 * Interface ParserInterface
 *
 * @package Browscap\Parser
 */
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
