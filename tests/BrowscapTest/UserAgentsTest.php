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
use BrowscapPHP\Browscap;
use WurflCache\Adapter\File;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer\Factory\PhpWriterFactory;

/**
 * Class UserAgentsTest
 *
 * @category   BrowscapTest
 * @package    Test
 * @author     James Titcumb <james@asgrim.com>
 * @group      useragenttest
 */
class UserAgentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \BrowscapPHP\Browscap
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
     */
    public static function setUpBeforeClass()
    {
        // First, generate the INI files
        $buildNumber    = time();
        $resourceFolder = __DIR__ . '/../../resources/';

        self::$buildFolder = __DIR__ . '/../../build/browscap-ua-test-' . $buildNumber . '/build/';
        $cacheFolder       = __DIR__ . '/../../build/browscap-ua-test-' . $buildNumber . '/cache/';

        // create build folder if it does not exist
        if (!file_exists(self::$buildFolder)) {
            mkdir(self::$buildFolder, 0777, true);
        }
        if (!file_exists($cacheFolder)) {
            mkdir($cacheFolder, 0777, true);
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

        $buildGenerator->run($buildNumber, false);

        $cache = new File(array(File::DIR => $cacheFolder));
        // Now, load an INI file into BrowscapPHP\Browscap for testing the UAs
        self::$browscap = new Browscap();
        self::$browscap
            ->setCache($cache)
            ->setLogger($logger)
        ;

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
     *
     * @param string $userAgent
     * @param array  $expectedProperties
     * @param bool   $lite
     * @param bool   $standard
     *
     * @throws \Exception
     * @throws \phpbrowscap\Exception
     * @group  integration
     * @group  useragenttest
     * @group  full
     */
    public function testUserAgentsFull($userAgent, $expectedProperties, $lite = true, $standard = true)
    {
        if (!is_array($expectedProperties) || !count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        static $updatedFullCache = false;

        if (!$updatedFullCache) {
            self::$browscap->getCache()->flush();
            self::$browscap->convertFile(self::$buildFolder . '/full_php_browscap.ini');
            $updatedFullCache = true;
        }

        $actualProps = (array) self::$browscap->getBrowser($userAgent);

        foreach ($expectedProperties as $propName => $propValue) {
            if (!self::$propertyHolder->isOutputProperty($propName)) {
                continue;
            }

            $propName = strtolower($propName);

            self::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual result does not have "' . $propName . '" property'
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
     * @param bool   $standard
     *
     * @throws \Exception
     * @throws \phpbrowscap\Exception
     * @group  integration
     * @group  useragenttest
     * @group  standard
     */
    public function testUserAgentsStandard($userAgent, $expectedProperties, $lite = true, $standard = true)
    {
        if (!is_array($expectedProperties) || !count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        if (!$standard) {
            self::markTestSkipped('Test skipped - Browser/Platform/Version not defined for Standard Mode');
        }

        static $updatedStandardCache = false;

        if (!$updatedStandardCache) {
            self::$browscap->getCache()->flush();
            self::$browscap->convertFile(self::$buildFolder . '/php_browscap.ini');
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

            $propName = strtolower($propName);

            self::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual result does not have "' . $propName . '" property'
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
     * @param bool   $standard
     *
     * @throws \Exception
     * @throws \BrowscapPHP\Exception
     *
     * @group intergration
     * @group useragenttest
     * @group lite
     */
    public function testUserAgentsLite($userAgent, $expectedProperties, $lite = true, $standard = true)
    {
        if (!is_array($expectedProperties) || !count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        if (!$lite) {
            self::markTestSkipped('Test skipped - Browser/Platform/Version not defined for Lite Mode');
        }

        static $updatedLiteCache = false;

        if (!$updatedLiteCache) {
            self::$browscap->getCache()->flush();
            self::$browscap->convertFile(self::$buildFolder . '/lite_php_browscap.ini');
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

            $propName = strtolower($propName);

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
