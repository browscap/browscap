<?php

namespace Browscap\Generator;

use Psr\Log\LoggerInterface;
use Browscap\Parser\IniParser;

/**
 * Class BuildGenerator
 *
 * @package Browscap\Generator
 */
class DiffGenerator
{
    /**
     * @var string
     */
    private $buildFolder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * Entry point for generating builds for a specified version
     *
     * @param string $leftFilename
     * @param string $rightFilename
     */
    public function run($leftFilename, $rightFilename)
    {
        $this->logger->debug('parsing left file ' . $leftFilename);
        $iniParserLeft = new IniParser($leftFilename);
        $leftFile      = $iniParserLeft->setShouldSort(true)->parse();


        $this->logger->debug('parsing right file ' . $rightFilename);
        $iniParserRight = new IniParser($rightFilename);
        $rightFile      = $iniParserRight->setShouldSort(true)->parse();

        $this->logger->debug('build diffs between files');
        $ltrDiff = $this->recursiveArrayDiff($leftFile, $rightFile);
        $rtlDiff = $this->recursiveArrayDiff($rightFile, $leftFile);

        $this->logger->debug('LTR');
        $this->logger->debug(var_export($ltrDiff, true));

        $this->logger->debug('RTL');
        $this->logger->debug(var_export($rtlDiff, true));

        if (count($ltrDiff) || count($rtlDiff)) {
            $this->logger->info('The following differences have been found:');
            $sectionsRead = array();

            $this->logger->debug('Pass 1 (LTR)');
            foreach ($ltrDiff as $section => $props) {
                if (isset($rightFile[$section]) && is_array($rightFile[$section])) {
                    $this->compareSectionProperties(
                        $section,
                        $props,
                        (isset($rtlDiff[$section]) ? $rtlDiff[$section] : null),
                        $rightFile[$section]
                    );
                } else {
                    $this->logger->info('[' . $section . ']' . "\n" . 'Whole section only on LEFT');
                    $this->diffsFound++;
                }

                $sectionsRead[] = $section;
            }

            $this->logger->debug('Pass 2 (RTL)');
            foreach ($rtlDiff as $section => $props) {
                if (in_array($section, $sectionsRead)) {
                    continue;
                }

                if (isset($leftFile[$section]) && is_array($leftFile[$section])) {
                    $this->compareSectionProperties(
                        $section,
                        (isset($ltrDiff[$section]) ? $ltrDiff[$section] : array()),
                        $props,
                        $rightFile[$section]
                    );
                } else {
                    $this->logger->info('[' . $section . ']' . "\n" . 'Whole section only on RIGHT');
                    $this->diffsFound++;
                }
            }

            $msg = sprintf(
                '%sThere %s %d difference%s found in the comparison.',
                "\n",
                ($this->diffsFound == 1 ? 'was'  : 'were'),
                $this->diffsFound,
                ($this->diffsFound == 1 ? '' : 's')
            );

            $this->logger->info($msg);
        } else {
            $this->logger->info('No differences found, hooray!');
        }
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Generator\DiffGenerator
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param string $section
     * @param array  $leftPropsDifferences
     * @param array  $rightPropsDifferences
     * @param array  $rightProps
     */
    private function compareSectionProperties($section, array $leftPropsDifferences, array $rightPropsDifferences, array $rightProps)
    {
        $this->logger->info('[' . $section . ']');

        // Diff the properties
        $propsRead = array();

        if (isset($leftPropsDifferences)) {
            foreach ($leftPropsDifferences as $prop => $value) {
                if (isset($rightProps[$prop])) {
                    $msg = sprintf('"%s" differs (L / R): %s / %s', $prop, $value, $rightProps[$prop]);
                } else {
                    $msg = sprintf('"%s" is only on the LEFT', $prop);
                }

                $this->logger->info($msg);
                $this->diffsFound++;

                $propsRead[] = $prop;
            }
        }

        if (isset($rightPropsDifferences)) {
            foreach ($rightPropsDifferences as $prop => $value) {
                if (in_array($prop, $propsRead)) {
                    continue;
                }

                $msg = sprintf('"%s" is only on the RIGHT', $prop);
                $this->logger->info($msg);

                $this->diffsFound++;
            }
        }
    }

    /**
     * @param array $leftArray
     * @param array $rightArray
     *
     * @return array
     */
    private function recursiveArrayDiff(array $leftArray, array $rightArray)
    {
        $diffs = array();

        foreach ($leftArray as $key => $value) {
            if (array_key_exists($key, $rightArray)) {
                if (is_array($value)) {
                    $childDiffs = $this->recursiveArrayDiff($value, $rightArray[$key]);

                    if (count($childDiffs)) {
                        $diffs[$key] = $childDiffs;
                    }
                } else {
                    if ($value != $rightArray[$key]) {
                        $diffs[$key] = $value;
                    }
                }
            } else {
                $diffs[$key] = $value;
            }
        }

        return $diffs;
    }
}
