<?php

namespace Browscap\Generator;

use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class BuildGenerator
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $resourceFolder;

    /**
     * @var string
     */
    private $buildFolder;

    public function __construct($resourceFolder, $buildFolder)
    {
        $this->resourceFolder = $this->checkDirectoryExists($resourceFolder, 'resource');
        $this->buildFolder = $this->checkDirectoryExists($buildFolder, 'build');
    }

    private function checkDirectoryExists($directory, $type)
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

        $this->writeFiles($collection, $this->buildFolder);
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
     *
     * @return null
     */
    private function output($messages)
    {
        if (isset($this->output) && $this->output instanceof OutputInterface) {
            return $this->output->writeln($messages);
        }

        return null;
    }

    /**
     * Create and populate a data collection object from a resource folder
     *
     * @param        $version
     * @param string $resourceFolder
     *
     * @return \Browscap\Generator\DataCollection
     */
    private function createDataCollection($version, $resourceFolder)
    {
        $collection = new DataCollection($version);
        $collection->addPlatformsFile($resourceFolder . '/platforms.json');

        $uaSourceDirectory = $resourceFolder . '/user-agents';

        $iterator = new \RecursiveDirectoryIterator($uaSourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
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
     * Write out the various INI file formats and the XML file format
     *
     * @param \Browscap\Generator\DataCollection $collection
     * @param string $buildFolder
     */
    private function writeFiles(DataCollection $collection, $buildFolder)
    {
        $collectionParser = new CollectionParser();
        $iniGenerator     = new BrowscapIniGenerator();
        $xmlGenerator     = new BrowscapXmlGenerator();
        $csvGenerator     = new BrowscapCsvGenerator();

        $version = $collection->getVersion();
        $dateUtc = $collection->getGenerationDate()->format('l, F j, Y \a\t h:i A T');
        $date    = $collection->getGenerationDate()->format('r');

        $comments = [
            'Provided courtesy of http://browscap.org/',
            'Created on ' . $dateUtc,
            'Keep up with the latest goings-on with the project:',
            'Follow us on Twitter <https://twitter.com/browscap>, or...',
            'Like us on Facebook <https://facebook.com/browscap>, or...',
            'Collaborate on GitHub <https://github.com/browscap>, or...',
            'Discuss on Google Groups <https://groups.google.com/forum/#!forum/browscap>.'
        ];

        $formats = [
            [
                'file'   => 'full_asp_browscap.ini',
                'info'   => 'ASP/FULL',
                'set'    => BrowscapIniGenerator::OUTPUT_SET_FULL,
                'format' => BrowscapIniGenerator::OUTPUT_FORMAT_ASP
            ],
            [
                'file'   => 'full_php_browscap.ini',
                'info'   => 'PHP/FULL',
                'set'    => BrowscapIniGenerator::OUTPUT_SET_FULL,
                'format' => BrowscapIniGenerator::OUTPUT_FORMAT_PHP
            ],
            [
                'file'   => 'browscap.ini',
                'info'   => 'ASP',
                'set'    => BrowscapIniGenerator::OUTPUT_SET_NORMAL,
                'format' => BrowscapIniGenerator::OUTPUT_FORMAT_ASP
            ],
            [
                'file'   => 'php_browscap.ini',
                'info'   => 'PHP',
                'set'    => BrowscapIniGenerator::OUTPUT_SET_NORMAL,
                'format' => BrowscapIniGenerator::OUTPUT_FORMAT_PHP
            ],
            [
                'file'   => 'lite_asp_browscap.ini',
                'info'   => 'ASP/LITE',
                'set'    => BrowscapIniGenerator::OUTPUT_SET_LITE,
                'format' => BrowscapIniGenerator::OUTPUT_FORMAT_ASP
            ],
            [
                'file'   => 'lite_php_browscap.ini',
                'info'   => 'PHP/LITE',
                'set'    => BrowscapIniGenerator::OUTPUT_SET_LITE,
                'format' => BrowscapIniGenerator::OUTPUT_FORMAT_PHP
            ],
        ];

        $collectionParser->setDataCollection($collection);
        $collectionData = $collectionParser->parse();

        $iniGenerator
            ->setCollectionData($collectionData)
            ->setComments($comments)
        ;

        foreach ($formats as $format) {
            $this->output('<info>Generating ' . $format['file'] . ' [' . $format['info'] . ']</info>');

            $outputFile = $buildFolder . '/' . $format['file'];

            $iniGenerator
                ->setVersionData(array('version' => $version, 'released' => $date))
            ;

            file_put_contents($outputFile, $iniGenerator->generate($format['set'], $format['format']));
        }

        $this->output('<info>Generating browscap.xml [XML]</info>');

        $xmlGenerator
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => $version, 'released' => $date))
        ;

        file_put_contents($buildFolder . '/browscap.xml', $xmlGenerator->generate());

        $this->output('<info>Generating browscap.csv [CSV]</info>');

        $csvGenerator
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => $version, 'released' => $date))
        ;

        file_put_contents($buildFolder . '/browscap.csv', $csvGenerator->generate());

        $this->output('<info>Generating browscap.zip [ZIP]</info>');

        $zip = new ZipArchive();
        $zip->open($buildFolder . '/browscap.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFile($buildFolder . '/full_asp_browscap.ini', 'full_asp_browscap.ini');
        $zip->addFile($buildFolder . '/full_php_browscap.ini', 'full_php_browscap.ini');
        $zip->addFile($buildFolder . '/browscap.ini', 'browscap.ini');
        $zip->addFile($buildFolder . '/php_browscap.ini', 'php_browscap.ini');
        $zip->addFile($buildFolder . '/lite_asp_browscap.ini', 'lite_asp_browscap.ini');
        $zip->addFile($buildFolder . '/lite_php_browscap.ini', 'lite_php_browscap.ini');
        $zip->addFile($buildFolder . '/browscap.xml', 'browscap.xml');
        $zip->addFile($buildFolder . '/browscap.csv', 'browscap.csv');

        $zip->close();
    }
}
