<?php

namespace Browscap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Zend\Config\Reader\Ini as IniReader;

/**
 * @author James Titcumb <james@asgrim.com
 */
class DiffCommand extends Command
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $resourceFolder;

    /**
     * @var \Browscap\Adapters\ClassPropertiesAdapter
     */
    protected $classPropertiesAdapter;

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
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $leftFilename = $input->getArgument('left');
        $rightFilename = $input->getArgument('right');

        $leftFile = $this->sortArrayAndChildArrays($this->loadIniFileToArray($leftFilename));
        $rightFile = $this->sortArrayAndChildArrays($this->loadIniFileToArray($rightFilename));

        $ltrDiff = $this->recursiveArrayDiff($leftFile, $rightFile);
        $rtlDiff = $this->recursiveArrayDiff($rightFile, $leftFile);

        //$this->output->writeln('<info>LTR</info>');
        //var_dump($ltrDiff);
        //$this->output->writeln('<info>RTL</info>');
        //var_dump($rtlDiff);

        if (count($ltrDiff) || count($rtlDiff)) {
            $sectionsRead = array();

            foreach ($ltrDiff as $section => $props) {
                if (isset($rtlDiff[$section]) && is_array($rtlDiff[$section])) {
                    $msg = sprintf('<error>Section properties differ [%s]:</error>', $section);
                    $this->output->writeln($msg);

                    // Diff the properties
                    $propsRead = array();

                    foreach ($props as $prop => $value) {
                        if (isset($rtlDiff[$section][$prop])) {
                            $msg = sprintf('<comment>%s property is different (L / R): %s / %s</comment>', $prop, $value, $rtlDiff[$section][$prop]);
                            $this->output->writeln($msg);
                        } else {
                            $msg = sprintf('<comment>%s property is only on the left</comment>', $prop);
                            $this->output->writeln($msg);
                        }

                        $propsRead[] = $prop;
                    }

                    foreach ($rtlDiff[$section] as $prop => $value) {
                        if (in_array($prop, $propsRead)) {
                            continue;
                        }

                        $msg = sprintf('<comment>%s property is only on the right</comment>', $prop);
                        $this->output->writeln($msg);
                    }
                } else {
                    $msg = sprintf('<error>Section only on left [%s]</error>', $section);
                    $this->output->writeln($msg);
                }

                $sectionsRead[] = $section;
            }

            foreach ($rtlDiff as $section => $props) {
                if (in_array($section, $sectionsRead)) {
                    continue;
                }

                $msg = sprintf('<error>Section only on right [%s]</error>', $section);
                $this->output->writeln($msg);
            }
        } else {
            $this->output->writeln('<info>No differences found, hooray!</info>');
        }

        /*if (count($diff)) {
            foreach ($diff as $section => $props) {
                $msg = sprintf('<error>Section differs: [%s]</error>', $section);
                $this->output->writeln($msg);

                foreach ($props as $prop => $value) {
                    $msg = sprintf('<comment>Property differs: %s = %s</comment>', $prop, $value);
                    $this->output->writeln($msg);
                }
            }
        } else {
            $this->output->writeln('<info>No differences found!</info>');
        }*/
    }

    public function loadIniFileToArray($filename)
    {
        $reader = new IniReader();
        $reader->setNestSeparator(null);
        $data = $reader->fromFile($filename);

        return $data;
    }

    public function sortArrayAndChildArrays(&$array)
    {
        ksort($array);

        foreach ($array as $key => $childArray)
        {
            ksort($array[$key]);
        }

        return $array;
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
