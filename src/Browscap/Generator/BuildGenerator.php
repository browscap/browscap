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
namespace Browscap\Generator;

use ZipArchive;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <mimmi20@live.de>
 */
class BuildGenerator extends AbstractBuildGenerator
{
    /**
     * Entry point for generating builds for a specified version
     *
     * @param string $buildVersion
     * @param bool   $createZipFile
     */
    public function run(string $buildVersion, bool $createZipFile = true) : void
    {
        $this->preBuild();
        $this->build($buildVersion);
        $this->postBuild($createZipFile);
    }

    /**
     * runs after the build
     *
     * @param bool $createZipFile
     */
    protected function postBuild(bool $createZipFile = true) : void
    {
        if (!$createZipFile) {
            return;
        }

        $this->logger->info('started creating the zip archive');

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

        $this->logger->info('finished creating the zip archive');
    }
}
