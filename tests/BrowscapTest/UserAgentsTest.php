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
use Browscap\Generator\BuildGenerator;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use phpbrowscap\Browscap;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer\Factory\FullCollectionFactory;

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
     * @var string
     */
    private static $buildFolder = '';

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

        self::$buildFolder = __DIR__ . '/../../build/browscap-ua-test-' . $buildNumber;

        // create build folder if it does not exist
        if (!file_exists(self::$buildFolder)) {
            mkdir(self::$buildFolder, 0777, true);
        }

        $logger = new Logger('browscap');
        $logger->pushHandler(new NullHandler(Logger::DEBUG));

        $buildGenerator = new BuildGenerator($resourceFolder, self::$buildFolder);

        $writerCollectionFactory = new FullCollectionFactory();
        $writerCollection        = $writerCollectionFactory->createCollection($logger, self::$buildFolder);

        $buildGenerator
            ->setLogger($logger)
            ->setCollectionCreator(new CollectionCreator())
            ->setWriterCollection($writerCollection)
        ;

        $buildGenerator->run('test', false);

        // Now, load an INI file into phpbrowscap\Browscap for testing the UAs
        self::$browscap = new Browscap(self::$buildFolder);
    }

    public function userAgentDataProvider()
    {
        $data            = array();
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
            $this->markTestSkipped('Could not run test - no properties were defined to test');
        }

        $iniFile                   = self::$buildFolder . '/full_php_browscap.ini';
        self::$browscap->localFile = $iniFile;
        $actualProps               = (array) self::$browscap->getBrowser($userAgent);

        foreach ($expectedProperties as $propName => $propValue) {
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
            $this->markTestSkipped('Could not run test - no properties were defined to test');
        }

        $iniFile                   = self::$buildFolder . '/php_browscap.ini';
        self::$browscap->localFile = $iniFile;
        $actualProps               = (array) self::$browscap->getBrowser($userAgent);

        $propertyHolder = new PropertyHolder();

        foreach ($expectedProperties as $propName => $propValue) {
            if (!$propertyHolder->isStandardModeProperty($propName)) {
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
    public function testUserAgentsLite($userAgent, $expectedProperties)
    {
        if (!is_array($expectedProperties) || !count($expectedProperties)) {
            $this->markTestSkipped('Could not run test - no properties were defined to test');
        }

        $iniFile                   = self::$buildFolder . '/lite_php_browscap.ini';
        self::$browscap->localFile = $iniFile;
        $actualProps               = (array) self::$browscap->getBrowser($userAgent);

        $propertyHolder = new PropertyHolder();

        foreach ($expectedProperties as $propName => $propValue) {
            if (!$propertyHolder->isLiteModeProperty($propName)) {
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
