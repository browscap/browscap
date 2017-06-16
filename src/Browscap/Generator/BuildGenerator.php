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

namespace Browscap\Generator;

use ZipArchive;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class BuildGenerator extends AbstractBuildGenerator
{
    /**
     * Entry point for generating builds for a specified version
     *
     * @param string $version
     * @param bool   $createZipFile
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    public function run($version, $createZipFile = true)
    {
        return $this
            ->preBuild()
            ->build($version)
            ->postBuild($createZipFile);
    }

    /**
     * runs after the build
     *
     * @param bool $createZipFile
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    protected function postBuild($createZipFile = true)
    {
        if (!$createZipFile) {
            return $this;
        }

        $this->getLogger()->info('started creating the zip archive');

        $zip = new ZipArchive();
        $zip->open($this->buildFolder . '/browscap.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = [
            'full_asp_browscap.ini',
            'full_php_browscap.ini',
            'browscap.ini',
            'php_browscap.ini',
            'lite_asp_browscap.ini',
            'lite_php_browscap.ini',
            'browscap.xml',
            'browscap.csv',
            'browscap.json',
        ];

        foreach ($files as $file) {
            $filePath = $this->buildFolder . '/' . $file;

            if (!file_exists($filePath) || !is_readable($filePath)) {
                continue;
            }

            $zip->addFile($filePath, $file);
        }

        $zip->close();

        $this->getLogger()->info('finished creating the zip archive');

        return $this;
    }
}
