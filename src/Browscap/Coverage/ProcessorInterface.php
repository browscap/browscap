<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   Browscap
 * @copyright  1998-2017 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Coverage;

/**
 * Interface ProcessorInterface
 *
 * @category   Browscap
 * @author     Jay Klehr <jay.klehr@gmail.com>
 */
interface ProcessorInterface
{
    /**
     * Process the directory of JSON files using the collected pattern ids
     *
     * @param string[] $coveredIds
     *
     * @return void
     */
    public function process(array $coveredIds);

    /**
     * Write the processed coverage data to filename
     *
     * @param string $fileName
     *
     * @return void
     */
    public function write(string $fileName);

    /**
     * Set covered pattern ids
     *
     * @param string[] $coveredIds
     */
    public function setCoveredPatternIds(array $coveredIds);

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
