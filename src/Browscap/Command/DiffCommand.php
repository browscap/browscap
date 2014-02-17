<?php

namespace Browscap\Command;

use Browscap\Generator\BrowscapIniGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Helper\CollectionCreator;
use Browscap\Helper\Generator;
use Browscap\Helper\LoggerHelper;
use Browscap\Parser\IniParser;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author James Titcumb <james@asgrim.com>
 * @package Browscap\Command
 */
class DiffCommand extends Command
{
    /**
     * @var int Number of differences found in total
     */
    protected $diffsFound;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger = null;

    /**
     * (non-PHPdoc)
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $defaultResourceFolder = __DIR__ . BuildCommand::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('diff')
            ->setDescription('Compare the data contained within two .ini files (regardless of order or format)')
            ->addArgument('left', InputArgument::REQUIRED, 'The left .ini file to compare')
            ->addArgument('right', InputArgument::OPTIONAL, 'The right .ini file to compare')
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->diffsFound = 0;

        $leftFilename = $input->getArgument('left');
        $rightFilename = $input->getArgument('right');

        $loggerHelper = new LoggerHelper();
        $this->logger = $loggerHelper->create();

        $iniParserLeft = new IniParser($leftFilename);
        $leftFile = $iniParserLeft->setShouldSort(true)->parse();

        if (!$rightFilename || !file_exists($rightFilename)) {
            $this->logger->log(Logger::INFO, 'right file not set or invalid - creating right file from resources');

            $cache_dir = sys_get_temp_dir() . '/browscap-diff/' . microtime(true) . '/';
            $rightFilename = $cache_dir . 'full_php_browscap.ini';

            if (!file_exists($cache_dir)) {
                mkdir($cache_dir, 0777, true);
            }

            $resourceFolder = $input->getOption('resources');

            $collectionCreator = new CollectionCreator();
            $collectionParser = new CollectionParser();
            $iniGenerator = new BrowscapIniGenerator();

            $generatorHelper = new Generator();
            $generatorHelper
                ->setVersion('temporary-version')
                ->setResourceFolder($resourceFolder)
                ->setCollectionCreator($collectionCreator)
                ->setCollectionParser($collectionParser)
                ->createCollection()
                ->parseCollection()
                ->setGenerator($iniGenerator)
            ;

            file_put_contents($rightFilename, $generatorHelper->create(BuildGenerator::OUTPUT_FORMAT_PHP, BuildGenerator::OUTPUT_TYPE_FULL));
        }

        $iniParserRight = new IniParser($rightFilename);
        $rightFile = $iniParserRight->setShouldSort(true)->parse();

        $ltrDiff = $this->recursiveArrayDiff($leftFile, $rightFile);
        $rtlDiff = $this->recursiveArrayDiff($rightFile, $leftFile);

        if (count($ltrDiff) || count($rtlDiff)) {
            $this->logger->log(Logger::INFO, 'The following differences have been found:');
            $sectionsRead = array();

            foreach ($ltrDiff as $section => $props) {
                if (isset($rightFile[$section]) && is_array($rightFile[$section])) {
                    $this->compareSectionProperties($section, $props, (isset($rtlDiff[$section]) ? $rtlDiff[$section] : null), $rightFile[$section]);
                } else {
                    $this->logger->log(Logger::INFO, $section . "\n" . 'Whole section only on LEFT');
                    $this->diffsFound++;
                }

                $sectionsRead[] = $section;
            }

            foreach ($rtlDiff as $section => $props) {
                if (in_array($section, $sectionsRead)) {
                    continue;
                }

                if (isset($leftFile[$section]) && is_array($leftFile[$section])) {
                    $this->compareSectionProperties($section, (isset($ltrDiff[$section]) ? $ltrDiff[$section] : null), $props, $rightFile[$section]);
                } else {
                    $this->logger->log(Logger::INFO, $section . "\n" . 'Whole section only on RIGHT');
                    $this->diffsFound++;
                }
            }

            $msg = sprintf('%sThere %s %d difference%s found in the comparison.', "\n", ($this->diffsFound == 1 ? 'was'  : 'were'), $this->diffsFound, ($this->diffsFound == 1 ? '' : 's'));
            $this->logger->log(Logger::INFO, $msg);
        } else {
            $this->logger->log(Logger::INFO, 'No differences found, hooray!');
        }

        $this->logger->log(Logger::INFO, 'Diff done.');
    }

    /**
     * @param string $section
     * @param array  $leftPropsDifferences
     * @param array  $rightPropsDifferences
     * @param array  $rightProps
     */
    public function compareSectionProperties($section, $leftPropsDifferences, $rightPropsDifferences, $rightProps)
    {
        $this->logger->log(Logger::INFO, '[' . $section . ']');

        // Diff the properties
        $propsRead = array();

        if (isset($leftPropsDifferences)) {
            foreach ($leftPropsDifferences as $prop => $value) {
                if (isset($rightProps[$prop])) {
                    $msg = sprintf('"%s" differs (L / R): %s / %s', $prop, $value, $rightProps[$prop]);
                    $this->logger->log(Logger::INFO, $msg);
                    $this->diffsFound++;
                } else {
                    $msg = sprintf('"%s" is only on the LEFT', $prop);
                    $this->logger->log(Logger::INFO, $msg);
                    $this->diffsFound++;
                }

                $propsRead[] = $prop;
            }
        }

        if (isset($rightPropsDifferences)) {
            foreach ($rightPropsDifferences as $prop => $value) {
                if (in_array($prop, $propsRead)) {
                    continue;
                }

                $msg = sprintf('"%s" is only on the RIGHT', $prop);
                $this->logger->log(Logger::INFO, $msg);
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
    public function recursiveArrayDiff($leftArray, $rightArray)
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
