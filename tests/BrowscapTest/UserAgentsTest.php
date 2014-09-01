<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   BrowscapTest
 * @package    Test
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest;

use Browscap\Data\DataCollection;
use Browscap\Data\Expander;
use Browscap\Filter\FullFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use phpbrowscap\Browscap;
use WurflCache\Adapter\Memory;
use WurflCache\Adapter\File;

/**
 * Class UserAgentsTest
 *
 * @category   BrowscapTest
 * @package    Test
 * @author     James Titcumb <james@asgrim.com>
 */
class UserAgentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \phpbrowscap\Browscap
     */
    private static $browscap;

    /**
     * This method is called before the first test of this test class is run.
     *
     * @since Method available since Release 3.4.0
     */
    public static function setUpBeforeClass()
    {
        // First, generate the INI files
        $buildNumber = time();

        $resourceFolder = __DIR__ . '/../../resources/';

        $buildFolder = __DIR__ . '/../../build/browscap-ua-test-' . $buildNumber;
        $iniFile     = $buildFolder . '/full_php_browscap.ini';
        
        mkdir($buildFolder, 0777, true);

        $logger = new Logger('browscap');
        $logger->pushHandler(new NullHandler(Logger::DEBUG));
        
        $builder = new \Browscap\Generator\BuildFullFileOnlyGenerator($resourceFolder, $buildFolder);
        $builder
            ->setLogger($logger)
            ->run('test', $iniFile)
        ;
        
        /*
        $collection = new DataCollection('test');
        $collection->setLogger($logger);

        $expander = new Expander();
        $expander
            ->setDataCollection($collection)
            ->setLogger($logger)
        ;

        $collectionCreator
            ->setLogger($logger)
            ->setDataCollection($collection)
            ->createDataCollection($resourceFolder)
        ;

        $writerCollection = new WriterCollection();
        $fullFilter       = new FullFilter();

        $fullPhpWriter = new IniWriter($iniFile);
        $formatter     = new PhpFormatter();
        $fullPhpWriter
            ->setLogger($logger)
            ->setFormatter($formatter->setFilter($fullFilter))
            ->setFilter($fullFilter)
        ;
        $writerCollection->addWriter($fullPhpWriter);

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
            ->renderVersion('test', $collection)
        ;

        $writerCollection->renderAllDivisionsHeader($collection);

        $division = $collection->getDefaultProperties();

        $writerCollection->renderDivisionHeader($division->getName());

        $ua       = $division->getUserAgents();
        $sections = array($ua[0]['userAgent'] => $ua[0]['properties']);

        foreach ($sections as $sectionName => $section) {
            $writerCollection
                ->renderSectionHeader($sectionName)
                ->renderSectionBody($section, $collection, $sections)
                ->renderSectionFooter()
            ;
        }

        $writerCollection->renderDivisionFooter();

        foreach ($collection->getDivisions() as $division) {
            $writerCollection->setSilent($division);

            $versions = $division->getVersions();

            foreach ($versions as $version) {
                list($majorVer, $minorVer) = $expander->getVersionParts($version);

                $userAgents = json_encode($division->getUserAgents());
                $userAgents = $expander->parseProperty($userAgents, $majorVer, $minorVer);
                $userAgents = json_decode($userAgents, true);

                $divisionName = $expander->parseProperty($division->getName(), $majorVer, $minorVer);

                $writerCollection->renderDivisionHeader($divisionName);

                $sections = $expander->expand($division, $majorVer, $minorVer, $divisionName);

                foreach ($sections as $sectionName => $section) {
                    $writerCollection
                        ->renderSectionHeader($sectionName)
                        ->renderSectionBody($section, $collection, $sections)
                        ->renderSectionFooter()
                    ;
                }

                $writerCollection->renderDivisionFooter();

                unset($userAgents, $divisionName, $majorVer, $minorVer);
            }
        }

        $division = $collection->getDefaultBrowser();

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
                ->renderSectionBody($section, $collection, $sections)
                ->renderSectionFooter()
            ;
        }

        $writerCollection
            ->renderDivisionFooter()
            ->renderAllDivisionsFooter()
        ;

        $writerCollection
            ->fileEnd()
            ->close()
        ;
        /**/

        $cache = new File($buildFolder);
        // Now, load an INI file into phpbrowscap\Browscap for testing the UAs
        self::$browscap = new Browscap();
        self::$browscap
            ->setCache($cache)
            ->setLogger($logger)
            ->convertFile($iniFile)
        ;
    }

    public function userAgentDataProvider()
    {
        $data              = array();
        $checks            = array();
        $uaSourceDirectory = __DIR__ . '/../fixtures/issues/';

        $iterator = new \RecursiveDirectoryIterator($uaSourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || $file->getExtension() != 'php') {
                continue;
            }

            $tests = require_once $file->getPathname();

            foreach ($tests as $key => $test) {
                if (isset($data[$key])) {
                    throw new \RuntimeException('Test data is duplicated for key "' . $key . '"');
                }
                if (isset($checks[$test[0]])) {
                    //throw new \RuntimeException('Test data is duplicated for key "' . $key . '"');
                    echo 'UA "' . $test[0] . '" added more than once, now for key "' . $key . '", before for key "' . $checks[$test[0]] . '"' . "\n";
                }

                $data[$key]       = $test;
                $checks[$test[0]] = $key;
            }
        }

        return $data;
    }

    /**
     * @dataProvider userAgentDataProvider
     * @coversNothing
     */
    public function testUserAgents($ua, $props)
    {
        if (!is_array($props) || !count($props)) {
            $this->markTestSkipped('Could not run test - no properties were defined to test');
        }

        $actualProps = (array) self::$browscap->getBrowser($ua);

        foreach ($props as $propName => $propValue) {
            $propName = strtolower($propName);

            self::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual properties did not have "' . $propName . '" property'
            );

            if ($propValue !== $actualProps[$propName]) {
                var_dump($ua, 'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName] . '")', $actualProps);
                //exit;
            }

            self::assertSame(
                $propValue,
                $actualProps[$propName],
                'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName]
                . '"; used pattern: ' . $actualProps['browser_name_pattern'] .')'
            );
        }
    }
}
