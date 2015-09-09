<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   Browscap
 * @package    Generator
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Generator;

use ZipArchive;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 * @package    Generator
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class BuildGenerator extends AbstractBuildGenerator
{
    /**
     * Entry point for generating builds for a specified version
     *
     * @param string  $version
     * @param boolean $createZipFile
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    public function run($version, $createZipFile = true)
    {
        return $this
            ->preBuild()
            ->build($version)
            ->postBuild($createZipFile)
            ;
    }

    /**
     * runs after the build
     *
     * @param boolean $createZipFile
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

        $files = array(
            'full_asp_browscap.ini',
            'full_php_browscap.ini',
            'browscap.ini',
            'php_browscap.ini',
            'lite_asp_browscap.ini',
            'lite_php_browscap.ini',
            'browscap.xml',
            'browscap.csv',
            'browscap.json'
        );

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
