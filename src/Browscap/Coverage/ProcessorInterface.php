<?php
declare(strict_types = 1);
namespace Browscap\Coverage;

interface ProcessorInterface
{
    /**
     * Process the directory of JSON files using the collected pattern ids
     *
     * @param string[] $coveredIds
     */
    public function process(array $coveredIds) : void;

    /**
     * Write the processed coverage data to filename
     *
     * @param string $fileName
     */
    public function write(string $fileName) : void;

    /**
     * Set covered pattern ids
     *
     * @param string[] $coveredIds
     *
     * @return void
     */
    public function setCoveredPatternIds(array $coveredIds) : void;

    /**
     * Returns the stored pattern ids
     *
     * @return array
     */
    public function getCoveredPatternIds() : array;

    /**
     * Process an individual file for coverage data using covered ids
     *
     * @param string   $file
     * @param string   $contents
     * @param string[] $coveredIds
     *
     * @return array
     */
    public function processFile(string $file, string $contents, array $coveredIds) : array;
}
