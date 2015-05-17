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

use Browscap\Data\PropertyHolder;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use phpbrowscap\Browscap;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer\Factory\PhpWriterFactory;

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
    private static $browscap = null;

    /**
     * @var string
     */
    private static $buildFolder = null;

    /**
     * @var \Browscap\Data\PropertyHolder
     */
    private static $propertyHolder = null;

    /**
     * This method is called before the first test of this test class is run.
     *
     * @since Method available since Release 3.4.0
     */
    public static function setUpBeforeClass()
    {
        // First, generate the INI files
        $buildNumber    = time();
        $resourceFolder = __DIR__ . '/../../resources/';

        self::$buildFolder = __DIR__ . '/../../build/browscap-ua-test-' . $buildNumber;

        // create build folder if it does not exist
        if (!file_exists(self::$buildFolder)) {
            mkdir(self::$buildFolder, 0777, true);
        }

        $logger = new Logger('browscap');
        $logger->pushHandler(new NullHandler(Logger::DEBUG));

        $buildGenerator = new BuildGenerator(
            $resourceFolder,
            self::$buildFolder
        );

        $writerCollectionFactory = new PhpWriterFactory();
        $writerCollection        = $writerCollectionFactory->createCollection($logger, self::$buildFolder);

        $buildGenerator
            ->setLogger($logger)
            ->setCollectionCreator(new CollectionCreator())
            ->setWriterCollection($writerCollection)
        ;

        $buildGenerator->run('test', false);

        // Now, load an INI file into phpbrowscap\Browscap for testing the UAs
        self::$browscap       = new Browscap(self::$buildFolder);
        self::$propertyHolder = new PropertyHolder();
    }

    /**
     * @return array
     */
    public function userAgentDataProvider()
    {
        static $data = array();

        if (count($data)) {
            return $data;
        }

        $checks          = array();
        $sourceDirectory = __DIR__ . '/../fixtures/issues/';

        $iterator = new \RecursiveDirectoryIterator($sourceDirectory);

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
                    throw new \RuntimeException(
                        'UA "' . $test[0] . '" added more than once, now for key "' . $key . '", before for key "'
                        . $checks[$test[0]] . '"'
                    );
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
     * @param string $userAgent
     * @param array  $expectedProperties
     */
    public function testUserAgentsFull($userAgent, $expectedProperties)
    {
        if (!is_array($expectedProperties) || !count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        self::markTestSkipped('Could not run test');

        self::$browscap->iniFilename   = 'full_browscap.ini';
        self::$browscap->localFile     = self::$buildFolder . '/full_php_browscap.ini';
        self::$browscap->cacheFilename = 'cache-full.php';
        self::$browscap->doAutoUpdate  = false;
        self::$browscap->silent        = false;
        self::$browscap->updateMethod  = Browscap::UPDATE_LOCAL;
        static $updatedFullCache       = false;

        if (!$updatedFullCache) {
            self::$browscap->updateCache();
            $updatedFullCache = true;
        }

        $actualProps = (array) self::$browscap->getBrowser($userAgent);

        foreach ($expectedProperties as $propName => $propValue) {
            if (!self::$propertyHolder->isOutputProperty($propName)) {
                continue;
            }

            self::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual properties did not have "' . $propName . '" property'
            );

            self::assertSame(
                $propValue,
                $actualProps[$propName],
                'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName]
                . '"; used pattern: ' . $actualProps['browser_name_pattern'] .')'
            );
        }
    }

    /**
     * @dataProvider userAgentDataProvider
     * @coversNothing
     * @param string $userAgent
     * @param array  $expectedProperties
     */
    public function testUserAgentsStandard($userAgent, $expectedProperties)
    {
        if (!is_array($expectedProperties) || !count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        //self::markTestSkipped('Could not run test');

        self::$browscap->iniFilename   = 'browscap.ini';
        self::$browscap->localFile     = self::$buildFolder . '/php_browscap.ini';
        self::$browscap->cacheFilename = 'cache-standard.php';
        self::$browscap->doAutoUpdate  = false;
        self::$browscap->silent        = false;
        self::$browscap->updateMethod  = Browscap::UPDATE_LOCAL;
        static $updatedStandardCache   = false;

        if (!$updatedStandardCache) {
            self::$browscap->updateCache();
            $updatedStandardCache = true;
        }

        $actualProps = (array) self::$browscap->getBrowser($userAgent);

        foreach ($expectedProperties as $propName => $propValue) {
            if (!self::$propertyHolder->isOutputProperty($propName)) {
                continue;
            }

            if (!self::$propertyHolder->isStandardModeProperty($propName)) {
                continue;
            }

            self::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual properties did not have "' . $propName . '" property'
            );

            self::assertSame(
                $propValue,
                $actualProps[$propName],
                'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName]
                . '"; used pattern: ' . $actualProps['browser_name_pattern'] .')'
            );
        }
    }

    /**
     * @dataProvider userAgentDataProvider
     * @coversNothing
     *
     * @param string $userAgent
     * @param array  $expectedProperties
     * @param bool   $lite
     *
     * @throws \Exception
     * @throws \phpbrowscap\Exception
     */
    public function testUserAgentsLite($userAgent, $expectedProperties, $lite = true)
    {
        if (!is_array($expectedProperties) || !count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        if (!$lite) {
            self::markTestSkipped('Test skipped - Browser not defined for Lite Mode');
        }

        self::$browscap->iniFilename   = 'lite_browscap.ini';
        self::$browscap->localFile     = self::$buildFolder . '/lite_php_browscap.ini';
        self::$browscap->cacheFilename = 'cache-lite.php';
        self::$browscap->doAutoUpdate  = false;
        self::$browscap->silent        = false;
        self::$browscap->updateMethod  = Browscap::UPDATE_LOCAL;
        static $updatedLiteCache       = false;

        if (!$updatedLiteCache) {
            self::$browscap->updateCache();
            $updatedLiteCache = true;
        }

        $actualProps = (array) self::$browscap->getBrowser($userAgent);

        foreach ($expectedProperties as $propName => $propValue) {
            if (!self::$propertyHolder->isOutputProperty($propName)) {
                continue;
            }

            if (!self::$propertyHolder->isLiteModeProperty($propName)) {
                continue;
            }

            self::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual properties did not have "' . $propName . '" property'
            );

            self::assertSame(
                $propValue,
                $actualProps[$propName],
                'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName]
                . '"; used pattern: ' . $actualProps['browser_name_pattern'] .')'
            );
        }
    }
}
