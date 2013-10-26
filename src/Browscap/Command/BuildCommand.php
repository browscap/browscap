<?php

namespace Browscap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Browscap\Generator\BrowscapIniGenerator;
use Browscap\Generator\DataCollection;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class BuildCommand extends Command
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
     * @var string
     */
    protected $buildFolder;

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('The JSON source files and builds the INI files')
            ->addArgument('version', InputArgument::REQUIRED, "Version number to apply");
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->resourceFolder = __DIR__ . '/../../../resources';
        $this->buildFolder = __DIR__ . '/../../../build';
        $version = $input->getArgument('version');

        $collection = $this->createDataCollection($version, $this->resourceFolder);

        $this->writeIniFiles($collection, $this->buildFolder);

        $this->output->writeln('<info>All done.</info>');
    }

    /**
     * Create and populate a data collection object from a resource folder
     *
     * @param string $resourceFolder
     * @return \Browscap\Generator\DataCollection
     */
    public function createDataCollection($version, $resourceFolder)
    {
        $collection = new DataCollection($version);
        $collection->addPlatformsFile($resourceFolder . '/platforms.json');

        $uaSourceDirectory = $resourceFolder . '/user-agents';

        $iterator = new \RecursiveDirectoryIterator($uaSourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            if (!$file->isFile() || $file->getExtension() != 'json') {
                continue;
            }

            $msg = sprintf('<info>Processing file %s ...</info>', $file->getPathname());
            $this->output->writeln($msg);
            $collection->addSourceFile($file->getPathname());
        }

        return $collection;
    }

    /**
     * Write out the various INI file formats
     *
     * @param \Browscap\Generator\DataCollection $collection
     * @param string $buildFolder
     */
    public function writeIniFiles(DataCollection $collection, $buildFolder)
    {
        $iniGenerator = new BrowscapIniGenerator();
        $iniGenerator->setDataCollection($collection);

        $formats = array(
            ['full_asp_browscap.ini', 'ASP/FULL', false, true, false],
            ['full_php_browscap.ini', 'PHP/FULL', true, true, false],
            ['browscap.ini', 'ASP', false, false, false],
            ['php_browscap.ini', 'PHP', true, false, false],
            ['lite_asp_browscap.ini', 'ASP/LITE', false, false, true],
            ['lite_php_browscap.ini', 'PHP/LITE', true, false, true],
        );

        foreach ($formats as $format) {
            $this->output->writeln('<info>Generating ' . $format[0] . ' [' . $format[1] . ']</info>');

            $outputFile = $buildFolder . '/' . $format[0];

            $iniGenerator->setOptions($format[2], $format[3], $format[4]);

            file_put_contents($outputFile, $iniGenerator->generate());
        }
    }
}
