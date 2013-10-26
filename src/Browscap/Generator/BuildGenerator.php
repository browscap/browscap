<?php

namespace Browscap\Generator;

use Symfony\Component\Console\Output\OutputInterface;

class BuildGenerator
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

    public function __construct($resourceFolder, $buildFolder)
    {
        $this->resourceFolder = $this->checkDirectoryExists($resourceFolder, 'resource');
        $this->buildFolder = $this->checkDirectoryExists($buildFolder, 'build');
    }

    protected function checkDirectoryExists($directory, $type)
    {
        if (!isset($directory)) {
            throw new \Exception("You must specify a {$type} folder");
        }

        $realDirectory = realpath($directory);

        if ($realDirectory === false) {
            throw new \Exception("The directory '{$directory}' does not exist, or we cannot access it");
        }

        if (!is_dir($realDirectory)) {
            throw new \Exception("The path '{$realDirectory}' did not resolve to a directory");
        }

        return $realDirectory;
    }

    /**
     * Entry point for generating builds for a specified version
     *
     * @param string $version
     */
    public function generateBuilds($version)
    {
        $this->output('<info>Resource folder: ' . $this->resourceFolder . '</info>');
        $this->output('<info>Build folder: ' . $this->buildFolder . '</info>');

        $collection = $this->createDataCollection($version, $this->resourceFolder);

        $this->writeIniFiles($collection, $this->buildFolder);
    }

    /**
     * Sets the optional output interface
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $outputInterface
     * @return \Browscap\Generator\BuildGenerator
     */
    public function setOutput(OutputInterface $outputInterface)
    {
        $this->output = $outputInterface;
        return $this;
    }

    /**
     * If an output interface has been set, write to it. This does nothing if setOutput has not been called.
     *
     * @param string|array $messages
     */
    protected function output($messages)
    {
    	if (isset($this->output) && $this->output instanceof OutputInterface) {
    	    return $this->output->writeln($messages);
    	}
    	return null;
    }

    /**
     * Create and populate a data collection object from a resource folder
     *
     * @param string $resourceFolder
     * @return \Browscap\Generator\DataCollection
     */
    protected function createDataCollection($version, $resourceFolder)
    {
        $collection = new DataCollection($version);
        $collection->addPlatformsFile($resourceFolder . '/platforms.json');

        $uaSourceDirectory = $resourceFolder . '/user-agents';

        $iterator = new \RecursiveDirectoryIterator($uaSourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            if (!$file->isFile() || $file->getExtension() != 'json') {
                continue;
            }

            #$msg = sprintf('<info>Processing file %s ...</info>', $file->getPathname());
            #$this->output($msg);
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
    protected function writeIniFiles(DataCollection $collection, $buildFolder)
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
            $this->output('<info>Generating ' . $format[0] . ' [' . $format[1] . ']</info>');

            $outputFile = $buildFolder . '/' . $format[0];

            $iniGenerator->setOptions($format[2], $format[3], $format[4]);

            file_put_contents($outputFile, $iniGenerator->generate());
        }
    }
}
