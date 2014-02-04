<?php

namespace Browscap\Command;

use Browscap\Parser\IniParser;
use Monolog\Handler\NullHandler;
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
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

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
        $this->output = $output;

        $leftFilename  = $input->getArgument('left');
        $rightFilename = $input->getArgument('right');

        $debug          = $input->getOption('debug');

        if ($debug) {
            $logHandlers = array(
                new StreamHandler('php://output', Logger::DEBUG)
            );
        } else {
            $logHandlers = array(
                new NullHandler(Logger::DEBUG)
            );
        }

        $this->logger = new Logger('browscap', $logHandlers);

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
            $this->output->writeln('The following differences have been found:');
            $sectionsRead = array();

            $this->logger->log(Logger::DEBUG, 'Pass 1 (LTR)');

            foreach ($ltrDiff as $section => $props) {
                if (isset($rightFile[$section]) && is_array($rightFile[$section])) {
                    $this->compareSectionProperties($section, $props, $leftFile[$section], (isset($rtlDiff[$section]) ? $rtlDiff[$section] : null), $rightFile[$section]);
                } else {
                    $msg = sprintf('<comment>[%s]</comment>%s<error>Whole section only on LEFT</error>', $section, "\n");
                    $this->output->writeln("\n" . $msg);
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
                        $leftFile[$section],
                        $props,
                        $rightFile[$section]
                    );
                } else {
                    $msg = sprintf('<comment>[%s]</comment>%s<error>Whole section only on RIGHT</error>', $section, "\n");
                    $this->output->writeln("\n" . $msg);
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
            $this->output->writeln($msg);
        } else {
            $this->output->writeln('<info>No differences found, hooray!</info>');
        }
    }

    /**
     * @param string $section
     * @param array  $leftPropsDifferences
     * @param array  $leftProps
     * @param array  $rightPropsDifferences
     * @param array  $rightProps
     */
    public function compareSectionProperties($section, array $leftPropsDifferences, array $leftProps, array $rightPropsDifferences, array $rightProps)
    {
        $msg = sprintf('<comment>[%s]</comment>', $section);
        $this->output->writeln("\n" . $msg);

        // Diff the properties
        $propsRead = array();

        if (isset($leftPropsDifferences)) {
            foreach ($leftPropsDifferences as $prop => $value) {
                if (isset($rightProps[$prop])) {
                    $msg = sprintf('<error>"%s" differs (L / R): %s / %s</error>', $prop, $value, $rightProps[$prop]);
                } else {
                    $msg = sprintf('<error>"%s" is only on the LEFT</error>', $prop);
                }

                $this->output->writeln($msg);
                $this->diffsFound++;

                $propsRead[] = $prop;
            }
        }

        if (isset($rightPropsDifferences)) {
            foreach ($rightPropsDifferences as $prop => $value) {
                if (in_array($prop, $propsRead)) {
                    continue;
                }

                if (isset($leftProps[$prop])) {
                    $msg = sprintf('<error>"%s" differs (R / L): %s / %s</error>', $prop, $value, $leftProps[$prop]);
                } else {
                    $msg = sprintf('<error>"%s" is only on the RIGHT</error>', $prop);
                }

                $this->output->writeln($msg);
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
