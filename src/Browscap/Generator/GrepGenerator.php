<?php

namespace Browscap\Generator;

use Browscap\Command\GrepCommand;
use Psr\Log\LoggerInterface;
use ZipArchive;
use phpbrowscap\Browscap;

/**
 * Class BuildGenerator
 *
 * @package Browscap\Generator
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
     * @param string $cacheDir
     * @param string $iniFile
     * @param string $inputFile
     * @param string $mode
     */
    public function run($cacheDir, $iniFile, $inputFile, $mode)
    {
        $this->logger->debug('initialize Browscap');
        $this->browscap = new Browscap($cacheDir);
        $this->browscap->localFile = $iniFile;

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
