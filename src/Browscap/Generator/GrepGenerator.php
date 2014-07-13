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

use Browscap\Command\GrepCommand;
use Psr\Log\LoggerInterface;
use phpbrowscap\Browscap;

/**
 * Class GrepGenerator
 *
 * @category   Browscap
 * @package    Generator
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class GrepGenerator
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @var \phpbrowscap\Browscap
     */
    private $browscap = null;

    /**
     * Entry point for generating builds for a specified version
     *
     * @param \phpbrowscap\Browscap $browscap
     * @param string                $cacheDir
     * @param string                $inputFile
     * @param string                $mode
     */
    public function run(Browscap $browscap, $cacheDir, $inputFile, $mode)
    {
        $this->logger->debug('initialize Browscap');
        $this->browscap = $browscap;

        $fileContents = file_get_contents($inputFile);

        if (false !== strpos("\r\n", $fileContents)) {
            $uas = explode("\r\n", $fileContents);
        } else {
            $uas = explode("\n", $fileContents);
        }

        $foundMode       = 0;
        $foundInvisible  = 0;
        $foundUnexpected = 0;

        foreach (array_unique($uas) as $ua) {
            if (!$ua) {
                continue;
            }

            $check = $this->testUA($ua, $mode);

            if ($check === $mode) {
                $foundMode++;
            } elseif ($check === GrepCommand::FOUND_INVISIBLE) {
                $foundInvisible++;
            } else {
                $foundUnexpected++;
            }
        }

        $this->logger->info(
            'Found ' . $foundMode . ' ' . $mode . ' UAs and ' . $foundInvisible. ' other UAs, ' . $foundUnexpected
            . ' UAs had unexpected results'
        );
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Generator\GrepGenerator
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param string $ua
     * @param string $mode
     *
     * @return string
     */
    private function testUA($ua, $mode)
    {
        $data = $this->browscap->getBrowser($ua, true);

        if ($mode == GrepCommand::MODE_UNMATCHED && $data['Browser'] == 'Default Browser') {
            $this->logger->info($ua);

            return GrepCommand::MODE_UNMATCHED;
        } elseif ($mode == GrepCommand::MODE_MATCHED && $data['Browser'] != 'Default Browser') {
            $this->logger->info($ua);

            return GrepCommand::MODE_MATCHED;
        }

        return GrepCommand::FOUND_INVISIBLE;
    }
}
