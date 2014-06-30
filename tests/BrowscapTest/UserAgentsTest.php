<?php

namespace BrowscapTest;

use Browscap\Data\DataCollection;
use Browscap\Generator\BrowscapIniGenerator;
use Browscap\Generator\BuildGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Helper\CollectionCreator;
use Browscap\Helper\Generator;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use phpbrowscap\Browscap;

/**
 * Class UserAgentsTest
 *
 * @package BrowscapTest
 */
class UserAgentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \phpbrowscap\Browscap
     */
    protected static $browscap;

    public static function setUpBeforeClass()
    {
        // First, generate the INI files
        $buildNumber = time();

        $resourceFolder = __DIR__ . '/../../resources/';

        $buildFolder = sys_get_temp_dir() . '/browscap-ua-test-' . $buildNumber;
        mkdir($buildFolder, 0777, true);

        $logger = new Logger('browscap');
        $logger->pushHandler(new NullHandler(Logger::DEBUG));

        $collectionCreator = new CollectionCreator();

        $iniFile = $buildFolder . '/full_php_browscap.ini';

        $collection = new DataCollection('test');
        $collection->setLogger($logger);

        $collectionCreator
            ->setLogger($logger)
            ->setDataCollection($collection)
            ->createDataCollection($resourceFolder)
        ;

        $writerCollection = new \Browscap\Writer\WriterCollection();
        $fullFilter       = new \Browscap\Filter\FullFilter();

        $fullPhpWriter = new \Browscap\Writer\IniWriter($buildFolder . '/full_php_browscap.ini');
        $formatter     = new \Browscap\Formatter\PhpFormatter();
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
                ->renderSectionBody($section)
                ->renderSectionFooter()
            ;
        }

        $writerCollection->renderDivisionFooter();

        foreach ($collection->getDivisions() as $division) {
            /** @var \Browscap\Data\Division $division */
            $writerCollection->setSilent();

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
                        ->renderSectionBody($section)
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
                ->renderSectionBody($section)
                ->renderSectionFooter()
            ;
        }

        $writerCollection
            ->renderDivisionFooter()
            ->renderAllDivisionsFooter($collection)
        ;

        $writerCollection
            ->fileEnd()
            ->close()
        ;

        // Now, load an INI file into phpbrowscap\Browscap for testing the UAs
        $browscap = new Browscap($buildFolder);
        $browscap->localFile = $iniFile;

        self::$browscap = $browscap;
    }

    public function userAgentDataProvider()
    {
        $data = array();
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

                $data[$key] = $test;
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

        $actualProps = self::$browscap->getBrowser($ua, true);

        foreach ($props as $propName => $propValue) {
            self::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual properties did not have "' . $propName . '" property'
            );

            self::assertSame(
                $propValue,
                $actualProps[$propName],
                'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName] . '")'
            );
        }
    }
}
