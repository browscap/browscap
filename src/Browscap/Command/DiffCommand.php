<?php

namespace Browscap\Command;

use Browscap\Parser\IniParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class DiffCommand
    extends Command
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
     * (non-PHPdoc)
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('diff')
            ->setDescription('Compare the data contained within two .ini files (regardless of order or format)')
            ->addArgument('left', InputArgument::REQUIRED, 'The left .ini file to compare')
            ->addArgument('right', InputArgument::REQUIRED, 'The right .ini file to compare');
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->diffsFound = 0;
        $this->output     = $output;

        $leftFilename  = $input->getArgument('left');
        $rightFilename = $input->getArgument('right');

        $iniParserLeft = new IniParser($leftFilename);
        $leftFile      = $iniParserLeft->setShouldSort(true)
            ->parse();

        $iniParserRight = new IniParser($rightFilename);
        $rightFile      = $iniParserRight->setShouldSort(true)
            ->parse();

        $ltrDiff = $this->recursiveArrayDiff($leftFile, $rightFile);
        $rtlDiff = $this->recursiveArrayDiff($rightFile, $leftFile);

        //$this->output->writeln('<info>LTR</info>');
        //var_dump($ltrDiff);
        //$this->output->writeln('<info>RTL</info>');
        //var_dump($rtlDiff);

        if (count($ltrDiff) || count($rtlDiff)) {
            $this->output->writeln('The following differences have been found:');
            $sectionsRead = array();

            //$this->output->writeln('<info>Pass 1 (LTR)</info>');

            foreach ($ltrDiff as $section => $props) {
                if (isset($rightFile[$section]) && is_array($rightFile[$section])) {
                    $this->compareSectionProperties(
                        $section,
                        $props,
                        $leftFile[$section],
                        (isset($rtlDiff[$section]) ? $rtlDiff[$section] : null),
                        $rightFile[$section]
                    );
                } else {
                    $msg = sprintf(
                        '<comment>[%s]</comment>%s<error>Whole section only on LEFT</error>',
                        $section,
                        "\n"
                    );
                    $this->output->writeln("\n" . $msg);
                    $this->diffsFound++;
                }

                $sectionsRead[] = $section;
            }

            //$this->output->writeln('<info>Pass 2 (RTL)</info>');

            foreach ($rtlDiff as $section => $props) {
                if (in_array($section, $sectionsRead)) {
                    continue;
                }

                if (isset($leftFile[$section]) && is_array($leftFile[$section])) {
                    $this->compareSectionProperties(
                        $section,
                        (isset($ltrDiff[$section]) ? $ltrDiff[$section] : null),
                        $leftFile[$section],
                        $props,
                        $rightFile[$section]
                    );
                } else {
                    $msg = sprintf(
                        '<comment>[%s]</comment>%s<error>Whole section only on RIGHT</error>',
                        $section,
                        "\n"
                    );
                    $this->output->writeln("\n" . $msg);
                    $this->diffsFound++;
                }
            }

            $msg = sprintf(
                '%sThere %s %d difference%s found in the comparison.',
                "\n",
                ($this->diffsFound == 1 ? 'was' : 'were'),
                $this->diffsFound,
                ($this->diffsFound == 1 ? '' : 's')
            );
            $this->output->writeln($msg);
        } else {
            $this->output->writeln('<info>No differences found, hooray!</info>');
        }
    }

    public function compareSectionProperties(
        $section,
        $leftPropsDifferences,
        $leftProps,
        $rightPropsDifferences,
        $rightProps
    ) {
        $msg = sprintf('<comment>[%s]</comment>', $section);
        $this->output->writeln("\n" . $msg);

        // Diff the properties
        $propsRead = array();

        if (isset($leftPropsDifferences)) {
            foreach ($leftPropsDifferences as $prop => $value) {
                if (isset($rightProps[$prop])) {
                    $msg = sprintf('<error>"%s" differs (L / R): %s / %s</error>', $prop, $value, $rightProps[$prop]);
                    $this->output->writeln($msg);
                    $this->diffsFound++;
                } else {
                    $msg = sprintf('<error>"%s" is only on the LEFT</error>', $prop);
                    $this->output->writeln($msg);
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

                $msg = sprintf('<error>"%s" is only on the RIGHT</error>', $prop);
                $this->output->writeln($msg);
                $this->diffsFound++;
            }
        }
    }

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
