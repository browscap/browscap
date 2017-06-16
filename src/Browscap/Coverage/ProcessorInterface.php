<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
