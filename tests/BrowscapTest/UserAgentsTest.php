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

use Browscap\Generator\BuildFullFileOnlyGenerator;
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

        $builder = new BuildFullFileOnlyGenerator($resourceFolder, $buildFolder);
        $builder
            ->setLogger($logger)
            ->run('test', $iniFile)
        ;

        //$cache = new File($buildFolder);
        $cache = new Memory();
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

            self::assertSame(
                $propValue,
                $actualProps[$propName],
                'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName]
                . '"; used pattern: ' . $actualProps['browser_name_pattern'] .')'
            );
        }
    }
}
