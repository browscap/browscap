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
