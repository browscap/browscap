<?php

namespace Browscap\Generator;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
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

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @param string $resourceFolder
     * @param string $buildFolder
     */
    public function __construct($resourceFolder, $buildFolder)
    {
        $this->resourceFolder = $this->checkDirectoryExists($resourceFolder, 'resource');
        $this->buildFolder    = $this->checkDirectoryExists($buildFolder, 'build');
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

        $this->log('initializing collection parser');
        $collectionParser = new CollectionParser();

        $this->log('creating data collection');
        $collection = $collectionParser->createDataCollection($version, $this->resourceFolder);

        $this->log('initializing Generators');
        $iniGenerator = new BrowscapIniGenerator();
        $xmlGenerator = new BrowscapXmlGenerator();
        $csvGenerator = new BrowscapCsvGenerator();

        $this->log('parsing version and date');
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

        $this->log('parsing data collection');
        $collectionData = $collectionParser->parse();

        $iniGenerator->setCollectionData($collectionData);

        foreach ($formats as $format) {
            $this->output('<info>Generating ' . $format[0] . ' [' . $format[1] . ']</info>');

            $outputFile = $this->buildFolder . '/' . $format[0];

            $iniGenerator
                ->setOptions($format[2], $format[3], $format[4])
                ->setComments($comments)
                ->setVersionData(array('version' => $version, 'released' => $date))
                ->setLogger($this->logger)
            ;

            file_put_contents($outputFile, $iniGenerator->generate());
        }

        $this->output('<info>Generating browscap.xml [XML]</info>');

        $xmlGenerator
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => $version, 'released' => $date))
            ->setLogger($this->logger)
        ;

        file_put_contents($this->buildFolder . '/browscap.xml', $xmlGenerator->generate());

        $this->output('<info>Generating browscap.csv [CSV]</info>');

        $csvGenerator
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => $version, 'released' => $date))
            ->setLogger($this->logger)
        ;

        file_put_contents($this->buildFolder . '/browscap.csv', $csvGenerator->generate());

        $this->output('<info>Generating browscap.zip [ZIP]</info>');

        $zip = new ZipArchive();
        $zip->open($buildFolder . '/browscap.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $this->log('adding file "' . $buildFolder . '/full_asp_browscap.ini" to zip  archive');
        $zip->addFile($buildFolder . '/full_asp_browscap.ini', 'full_asp_browscap.ini');

        $this->log('adding file "' . $buildFolder . '/full_php_browscap.ini" to zip  archive');
        $zip->addFile($buildFolder . '/full_php_browscap.ini', 'full_php_browscap.ini');

        $this->log('adding file "' . $buildFolder . '/browscap.ini" to zip  archive');
        $zip->addFile($buildFolder . '/browscap.ini', 'browscap.ini');

        $this->log('adding file "' . $buildFolder . '/php_browscap.ini" to zip  archive');
        $zip->addFile($buildFolder . '/php_browscap.ini', 'php_browscap.ini');

        $this->log('adding file "' . $buildFolder . '/lite_asp_browscap.ini" to zip  archive');
        $zip->addFile($buildFolder . '/lite_asp_browscap.ini', 'lite_asp_browscap.ini');

        $this->log('adding file "' . $buildFolder . '/lite_php_browscap.ini" to zip  archive');
        $zip->addFile($buildFolder . '/lite_php_browscap.ini', 'lite_php_browscap.ini');

        $this->log('adding file "' . $buildFolder . '/browscap.xml" to zip  archive');
        $zip->addFile($buildFolder . '/browscap.xml', 'browscap.xml');

        $this->log('adding file "' . $buildFolder . '/browscap.csv" to zip  archive');
        $zip->addFile($buildFolder . '/browscap.csv', 'browscap.csv');

        $zip->close();
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
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param string $directory
     * @param string $type
     *
     * @return string
     * @throws \Exception
     */
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
     * @param string $message
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    private function log($message)
    {
        if (null === $this->logger) {
            return $this;
        }

        $this->logger->log(Logger::DEBUG, $message);

        return $this;
    }
}
