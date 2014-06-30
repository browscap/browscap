<?php

namespace Browscap\Generator;

use Browscap\Data\DataCollection;
use Psr\Log\LoggerInterface;
use ZipArchive;

/**
 * Class BuildGenerator
 *
 * @package Browscap\Generator
 */
class BuildGenerator
{
    /**@+
     * @var string
     */
    const OUTPUT_FORMAT_PHP = 'php';
    const OUTPUT_FORMAT_ASP = 'asp';
    /**@-*/

    /**@+
     * @var string
     */
    const OUTPUT_TYPE_FULL    = 'full';
    const OUTPUT_TYPE_DEFAULT = 'normal';
    const OUTPUT_TYPE_LITE    = 'lite';
    /**@-*/

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
     * @var \Browscap\Helper\CollectionCreator
     */
    private $collectionCreator = null;

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
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param \Browscap\Helper\CollectionCreator $collectionCreator
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    public function setCollectionCreator($collectionCreator)
    {
        $this->collectionCreator = $collectionCreator;

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
            throw new \Exception('You must specify a ' . $type . ' folder');
        }

        $realDirectory = realpath($directory);

        if ($realDirectory === false) {
            throw new \Exception('The directory "' . $directory . '" does not exist, or we cannot access it');
        }

        if (!is_dir($realDirectory)) {
            throw new \Exception('The path "' . $realDirectory . '" did not resolve to a directory');
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
        $this->getLogger()->info('Resource folder: ' . $this->resourceFolder . '');
        $this->getLogger()->info('Build folder: ' . $this->buildFolder . '');

        $this->getLogger()->info('started creating a data collection');

        $collection = new DataCollection($version);
        $collection->setLogger($this->getLogger());

        $this->collectionCreator
            ->setLogger($this->getLogger())
            ->setDataCollection($collection)
            ->createDataCollection($this->resourceFolder)
        ;

        $this->getLogger()->info('finished creating a data collection');

        $this->getLogger()->info('started initialisation of writers');

        $writerCollection = new \Browscap\Writer\WriterCollection();

        $fullFilter = new \Browscap\Filter\FullFilter();
        $stdFilter  = new \Browscap\Filter\StandartFilter();
        $liteFilter = new \Browscap\Filter\LiteFilter();

        $fullAspWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/full_asp_browscap.ini');
        $formatter     = new \Browscap\Formatter\AspFormatter();
        $fullAspWriter
            ->setLogger($this->getLogger())
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter)
        ;
        $writerCollection->addWriter($fullAspWriter);

        $fullPhpWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/full_php_browscap.ini');
        $formatter = new \Browscap\Formatter\PhpFormatter();
        $fullPhpWriter
            ->setLogger($this->getLogger())
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter)
        ;
        $writerCollection->addWriter($fullPhpWriter);

        $stdAspWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/browscap.ini');
        $formatter    = new \Browscap\Formatter\AspFormatter();
        $stdAspWriter
            ->setLogger($this->getLogger())
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter)
        ;
        $writerCollection->addWriter($stdAspWriter);

        $stdPhpWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/php_browscap.ini');
        $formatter    = new \Browscap\Formatter\PhpFormatter();
        $stdPhpWriter
            ->setLogger($this->getLogger())
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter)
        ;
        $writerCollection->addWriter($stdPhpWriter);

        $liteAspWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/lite_asp_browscap.ini');
        $formatter     = new \Browscap\Formatter\AspFormatter();
        $liteAspWriter
            ->setLogger($this->getLogger())
            ->setFormatter($formatter->setFilter($liteFilter))
            ->setFilter($liteFilter)
        ;
        $writerCollection->addWriter($liteAspWriter);

        $litePhpWriter = new \Browscap\Writer\IniWriter($this->buildFolder . '/lite_php_browscap.ini');
        $formatter     = new \Browscap\Formatter\PhpFormatter();
        $litePhpWriter
            ->setLogger($this->getLogger())
            ->setFormatter($formatter->setFilter($liteFilter))
            ->setFilter($liteFilter)
        ;
        $writerCollection->addWriter($litePhpWriter);

        $csvWriter = new \Browscap\Writer\CsvWriter($this->buildFolder . '/browscap.csv');
        $formatter = new \Browscap\Formatter\CsvFormatter();
        $csvWriter
            ->setLogger($this->getLogger())
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter)
        ;
        $writerCollection->addWriter($csvWriter);

        $xmlWriter = new \Browscap\Writer\XmlWriter($this->buildFolder . '/browscap.xml');
        $formatter = new \Browscap\Formatter\XmlFormatter();
        $xmlWriter
            ->setLogger($this->getLogger())
            ->setFormatter($formatter->setFilter($stdFilter))
            ->setFilter($stdFilter)
        ;
        $writerCollection->addWriter($xmlWriter);

        $expander = new \Browscap\Data\Expander();
        $expander
            ->setDataCollection($collection)
            ->setLogger($this->getLogger())
        ;

        $this->getLogger()->info('finished initialisation of writers');

        $this->getLogger()->info('started output of header and version');

        $comments = array(
            'Provided courtesy of http://browscap.org/',
            'Created on ' . $collection->getGenerationDate()->format('l, F j, Y \a\t h:i A T'),
            'Keep up with the latest goings-on with the project:',
            'Follow us on Twitter <https://twitter.com/browscap>, or...',
            'Like us on Facebook <https://facebook.com/browscap>, or...',
            'Collaborate on GitHub <https://github.com/browscap>, or...',
            'Discuss on Google Groups <https://groups.google.com/forum/#!forum/browscap>.'
        );

        $writerCollection
            ->fileStart()
            ->renderHeader($comments)
            ->renderVersion($version, $collection)
        ;

        $this->getLogger()->info('finished output of header and version');

        $this->getLogger()->info('started output of divisions');

        $writerCollection->renderAllDivisionsHeader($collection);

        $division = $collection->getDefaultProperties();

        $this->getLogger()->info('handle division ' . $division->getName());

        $writerCollection->renderDivisionHeader($division->getName());

        $ua       = $division->getUserAgents();
        $sections = array($ua[0]['userAgent'] => $ua[0]['properties']);

        foreach ($sections as $sectionName => $section) {
            $writerCollection
                ->renderSectionHeader($sectionName)
                ->renderSectionBody($section)
                ->renderSectionFooter()
            ;
        }

        $writerCollection->renderDivisionFooter();

        foreach ($collection->getDivisions() as $division) {
            /** @var \Browscap\Data\Division $division */
            $writerCollection->setSilent($division);

            $versions = $division->getVersions();

            foreach ($versions as $version) {
                list($majorVer, $minorVer) = $expander->getVersionParts($version);

                $userAgents = json_encode($division->getUserAgents());
                $userAgents = $expander->parseProperty($userAgents, $majorVer, $minorVer);
                $userAgents = json_decode($userAgents, true);

                $divisionName = $expander->parseProperty($division->getName(), $majorVer, $minorVer);

                $this->getLogger()->info('handle division ' . $divisionName);

                $writerCollection->renderDivisionHeader($divisionName);

                $sections = $expander->expand($division, $majorVer, $minorVer, $divisionName);

                foreach ($sections as $sectionName => $section) {
                    $writerCollection
                        ->renderSectionHeader($sectionName)
                        ->renderSectionBody($section)
                        ->renderSectionFooter()
                    ;
                }

                $writerCollection->renderDivisionFooter();

                unset($userAgents, $divisionName, $majorVer, $minorVer);
            }
        }

        $division = $collection->getDefaultBrowser();

        $this->getLogger()->info('handle division ' . $division->getName());

        $writerCollection->renderDivisionHeader($division->getName());

        $ua       = $division->getUserAgents();
        $sections = array(
            $ua[0]['userAgent'] => array_merge(
                array('Parent' => 'DefaultProperties'),
                $ua[0]['properties']
            )
        );

        foreach ($sections as $sectionName => $section) {
            $writerCollection
                ->renderSectionHeader($sectionName)
                ->renderSectionBody($section)
                ->renderSectionFooter()
            ;
        }

        $writerCollection
            ->renderDivisionFooter()
            ->renderAllDivisionsFooter($collection)
        ;

        $this->getLogger()->info('finished output of divisions');

        $this->getLogger()->info('started closing writers');

        $writerCollection
            ->fileEnd()
            ->close()
        ;

        $this->getLogger()->info('finished closing writers');

        $this->getLogger()->info('started creating the zip archive');

        $zip = new ZipArchive();
        $zip->open($this->buildFolder . '/browscap.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFile($this->buildFolder . '/full_asp_browscap.ini', 'full_asp_browscap.ini');
        $zip->addFile($this->buildFolder . '/full_php_browscap.ini', 'full_php_browscap.ini');
        $zip->addFile($this->buildFolder . '/browscap.ini', 'browscap.ini');
        $zip->addFile($this->buildFolder . '/php_browscap.ini', 'php_browscap.ini');
        $zip->addFile($this->buildFolder . '/lite_asp_browscap.ini', 'lite_asp_browscap.ini');
        $zip->addFile($this->buildFolder . '/lite_php_browscap.ini', 'lite_php_browscap.ini');
        $zip->addFile($this->buildFolder . '/browscap.xml', 'browscap.xml');
        $zip->addFile($this->buildFolder . '/browscap.csv', 'browscap.csv');

        $zip->close();

        $this->getLogger()->info('finished creating the zip archive');
    }
}
