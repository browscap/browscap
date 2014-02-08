<?php

namespace Browscap\Command;

use Browscap\Parser\IniParser;
use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author James Titcumb <james@asgrim.com>
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
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('diff')
            ->setDescription('Compare the data contained within two .ini files (regardless of order or format)')
            ->addArgument('left', InputArgument::REQUIRED, 'The left .ini file to compare')
            ->addArgument('right', InputArgument::REQUIRED, 'The right .ini file to compare')
            ->addOption('debug', null, InputOption::VALUE_NONE, "Should the debug mode entered?")
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->diffsFound = 0;

        $leftFilename  = $input->getArgument('left');
        $rightFilename = $input->getArgument('right');

        $debug = $input->getOption('debug');

        if ($debug) {
            $stream = new StreamHandler('php://output', Logger::DEBUG);
        } else {
            $stream = new StreamHandler('php://output', Logger::INFO);
        }

        $stream->setFormatter(new LineFormatter('%message%' . "\n"));

        $this->logger = new Logger('browscap');
        $this->logger->pushHandler($stream);
        $this->logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::NOTICE));

        ErrorHandler::register($this->logger);

        $this->logger->log(Logger::DEBUG, 'parsing left file ' . $leftFilename);
        $iniParserLeft = new IniParser($leftFilename);
        $leftFile      = $iniParserLeft->setShouldSort(true)->parse();

        $this->logger->log(Logger::DEBUG, 'parsing right file ' . $rightFilename);
        $iniParserRight = new IniParser($rightFilename);
        $rightFile      = $iniParserRight->setShouldSort(true)->parse();

        $this->logger->log(Logger::DEBUG, 'build diffs between files');
        $ltrDiff = $this->recursiveArrayDiff($leftFile, $rightFile);
        $rtlDiff = $this->recursiveArrayDiff($rightFile, $leftFile);

        $this->logger->log(Logger::DEBUG, 'LTR');
        $this->logger->log(Logger::DEBUG, var_export($ltrDiff, true));

        $this->logger->log(Logger::DEBUG, 'RTL');
        $this->logger->log(Logger::DEBUG, var_export($rtlDiff, true));

        if (count($ltrDiff) || count($rtlDiff)) {
            $this->logger->log(Logger::INFO, 'The following differences have been found:');
            $sectionsRead = array();

            $this->logger->log(Logger::DEBUG, 'Pass 1 (LTR)');
            foreach ($ltrDiff as $section => $props) {
                if (isset($rightFile[$section]) && is_array($rightFile[$section])) {
                    $this->compareSectionProperties($section, $props, (isset($rtlDiff[$section]) ? $rtlDiff[$section] : null), $rightFile[$section]);
                } else {
                    $this->logger->log(Logger::INFO, $section . "\n" . 'Whole section only on LEFT');
                    $this->diffsFound++;
                }

                $sectionsRead[] = $section;
            }

            $this->logger->log(Logger::DEBUG, 'Pass 2 (RTL)');
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
                    $this->logger->log(Logger::INFO, $section . "\n" . 'Whole section only on RIGHT');
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

            $this->logger->log(Logger::INFO, $msg);
        } else {
            $this->logger->log(Logger::INFO, 'No differences found, hooray!');
        }
    }

    /**
     * @param string $section
     * @param array  $leftPropsDifferences
     * @param array  $rightPropsDifferences
     * @param array  $rightProps
     *
     * @internal param array $leftProps
     */
    public function compareSectionProperties($section, array $leftPropsDifferences, array $rightPropsDifferences, array $rightProps)
    {
        $this->logger->log(Logger::INFO, $section);

        // Diff the properties
        $propsRead = array();

        if (isset($leftPropsDifferences)) {
            foreach ($leftPropsDifferences as $prop => $value) {
                if (isset($rightProps[$prop])) {
                    $msg = sprintf('"%s" differs (L / R): %s / %s', $prop, $value, $rightProps[$prop]);
                } else {
                    $msg = sprintf('"%s" is only on the LEFT', $prop);
                }

                $this->logger->log(Logger::INFO, $msg);
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
    public function recursiveArrayDiff(array $leftArray, array $rightArray)
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
