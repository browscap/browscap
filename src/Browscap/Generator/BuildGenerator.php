<?php

namespace Browscap\Generator;

use Browscap\Helper\CollectionCreator;
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

        $collection = CollectionCreator::createDataCollection($version, $this->resourceFolder);

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

        $comments = array(
            'Provided courtesy of http://browscap.org/',
            'Created on ' . $dateUtc,
            'Keep up with the latest goings-on with the project:',
            'Follow us on Twitter <https://twitter.com/browscap>, or...',
            'Like us on Facebook <https://facebook.com/browscap>, or...',
            'Collaborate on GitHub <https://github.com/browscap>, or...',
            'Discuss on Google Groups <https://groups.google.com/forum/#!forum/browscap>.'
        );

        $formats = array(
            ['full_asp_browscap.ini', 'ASP/FULL', false, true, false],
            ['full_php_browscap.ini', 'PHP/FULL', true, true, false],
            ['browscap.ini', 'ASP', false, false, false],
            ['php_browscap.ini', 'PHP', true, false, false],
            ['lite_asp_browscap.ini', 'ASP/LITE', false, false, true],
            ['lite_php_browscap.ini', 'PHP/LITE', true, false, true],
        );

        $collectionParser->setDataCollection($collection);
        $collectionData = $collectionParser->parse();

        $iniGenerator->setCollectionData($collectionData);

        foreach ($formats as $format) {
            $this->output('<info>Generating ' . $format[0] . ' [' . $format[1] . ']</info>');

            $outputFile = $buildFolder . '/' . $format[0];

            $iniGenerator
                ->setOptions($format[2], $format[3], $format[4])
                ->setComments($comments)
                ->setVersionData(array('version' => $version, 'released' => $date))
            ;

            file_put_contents($outputFile, $iniGenerator->generate());
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
