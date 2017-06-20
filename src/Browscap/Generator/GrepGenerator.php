<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Browscap\Generator;

use Browscap\Command\GrepCommand;
use BrowscapPHP\Browscap;
use Psr\Log\LoggerInterface;

/**
 * Class GrepGenerator
 *
 * @category   Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class GrepGenerator
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @var \BrowscapPHP\Browscap
     */
    private $browscap = null;

    /**
     * Entry point for generating builds for a specified version
     *
     * @param \BrowscapPHP\Browscap $browscap
     * @param string                $inputFile
     * @param string                $mode
     */
    public function run(Browscap $browscap, $inputFile, $mode)
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
                ++$foundMode;
            } elseif ($check === GrepCommand::FOUND_INVISIBLE) {
                ++$foundInvisible;
            } else {
                ++$foundUnexpected;
            }
        }

        $this->logger->info(
            'Found ' . $foundMode . ' ' . $mode . ' UAs and ' . $foundInvisible . ' other UAs, ' . $foundUnexpected
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

        if ($mode === GrepCommand::MODE_UNMATCHED && $data['Browser'] === 'Default Browser') {
            $this->logger->info($ua);

            return GrepCommand::MODE_UNMATCHED;
        } elseif ($mode === GrepCommand::MODE_MATCHED && $data['Browser'] !== 'Default Browser') {
            $this->logger->info($ua);

            return GrepCommand::MODE_MATCHED;
        }

        return GrepCommand::FOUND_INVISIBLE;
    }
}
