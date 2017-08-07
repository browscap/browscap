<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Browscap\Coverage;

/**
 * Interface ProcessorInterface
 *
 * @category   Browscap
 *
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
    public function getCoveredPatternIds(): array;

    /**
     * Process an individual file for coverage data using covered ids
     *
     * @param string   $file
     * @param string   $contents
     * @param string[] $coveredIds
     *
     * @return array
     */
    public function processFile(string $file, string $contents, array $coveredIds): array;
}
