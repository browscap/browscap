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
 * @package    Data
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Data;

use Browscap\Data\DataCollection;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class DataCollectionTest
 *
 * @category   BrowscapTest
 * @package    Data
 * @author     James Titcumb <james@asgrim.com>
 */
class DataCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    public function setUp()
    {
        $this->logger = new Logger('browscapTest', array(new NullHandler()));
    }

    public function getPlatformsJsonFixture()
    {
        return __DIR__ . '/../../fixtures/platforms/platforms.json';
    }

    public function getEngineJsonFixture()
    {
        return __DIR__ . '/../../fixtures/engines/engines.json';
    }

    private function getUserAgentFixtures()
    {
        $dir = __DIR__ . '/../../fixtures/ua';

        return [
            $dir . '/test1.json',
            $dir . '/test2.json',
            $dir . '/test3.json',
        ];
    }

    public function testAddPlatformsFile()
    {
        $data = new DataCollection('1234');

        $data->addPlatformsFile($this->getPlatformsJsonFixture());

        $platforms = $data->getPlatforms();

        $expected = [
            'Platform1' => [
                'match' => '*Platform1*',
                'properties' => [
                    'Platform' => 'Platform1',
                    'Platform_Description' => 'The first test platform',
                    'Win32' => 'false',
                ],
            ],
            'Platform2' => [
                'match' => '*Platform2*',
                'properties' => [
                    'Platform' => 'Platform2',
                    'Win32' => 'false',
                ],
            ],
        ];

        self::assertSame($expected, $platforms);

        self::assertSame($expected['Platform1'], $data->getPlatform('Platform1'));
        self::assertSame($expected['Platform2'], $data->getPlatform('Platform2'));
    }

    public function testAddEngineFile()
    {
        $data = new DataCollection('1234');

        $data->addEnginesFile($this->getEngineJsonFixture());

        $expected = [
            'Foobar' => [
                'properties' => [
                    'RenderingEngine_Name' => 'Foobar',
                ],
            ],
            'Foo' => [
                'properties' => [
                    'RenderingEngine_Name' => 'Foobar',
                ],
            ],
        ];

        self::assertSame($expected['Foobar'], $data->getEngine('Foobar'));
        self::assertSame($expected['Foo'], $data->getEngine('Foo'));
    }

    public function testAddPlatformsFileThrowsExceptionIfFileDoesNotExist()
    {
        $data = new DataCollection('1234');

        $file = '/hopefully/this/file/does/not/exist';

        $this->setExpectedException('\RuntimeException', 'File "' . $file . '" does not exist');
        $data->addPlatformsFile($file);
    }

    public function testAddPlatformsFileThrowsExceptionIfFileContainsInvalidJson()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
this is not valid JSON
HERE;

        file_put_contents($tmpfile, $in);

        $data = new DataCollection('1234');

        $this->setExpectedException('\RuntimeException', 'File "' . $tmpfile . '" had invalid JSON.');
        $data->addPlatformsFile($tmpfile);

        unlink($tmpfile);
    }

    public function testGetPlatformThrowsExceptionIfPlatformDoesNotExist()
    {
        $data = new DataCollection('1234');

        $data->addPlatformsFile($this->getPlatformsJsonFixture());

        $this->setExpectedException('\OutOfBoundsException', 'Platform "NotExists" does not exist in data');
        $data->getPlatform('NotExists');
    }

    public function testGetVersion()
    {
        $data = new DataCollection('1234');
        self::assertSame('1234', $data->getVersion());
    }

    public function testGetGenerationDate()
    {
        $data = new DataCollection('1234');

        // Time isn't always exact, so allow a few seconds grace either way...
        $currentTime = time();
        $minTime = $currentTime - 3;
        $maxTime = $currentTime + 3;

        $testDateTime = $data->getGenerationDate();

        self::assertInstanceOf('\DateTime', $testDateTime);

        $testTime = $testDateTime->getTimestamp();
        self::assertGreaterThanOrEqual($minTime, $testTime);
        self::assertLessThanOrEqual($maxTime, $testTime);
    }

    /**
     * checks if a exception is thrown if a division is defined twice in the source files
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Division "UA2" is defined twice
     */
    public function testAddSourceFileFail()
    {
        $data = new DataCollection('1234');

        $files = $this->getUserAgentFixtures();

        foreach ($files as $file) {
            $data->addSourceFile($file);
        }
    }

    /**
     * checks if a source file is added successful
     */
    public function testAddSourceFileOk()
    {
        $data = new DataCollection('1234');

        $data->addSourceFile(__DIR__ . '/../../fixtures/ua/test1.json');

        $divisions = $data->getDivisions();

        $expected = require_once __DIR__ . '/../../fixtures/DataCollectionTestArray.php';

        self::assertEquals($expected, $divisions);
    }

    public function testAddSourceFileThrowsExceptionIfFileDoesNotExist()
    {
        $data = new DataCollection('1234');

        $file = '/hopefully/this/file/does/not/exist';

        $this->setExpectedException('\RuntimeException', 'File "' . $file . '" does not exist');
        $data->addSourceFile($file);
    }

    public function testAddSourceFileThrowsExceptionIfFileContainsInvalidJson()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
this is not valid JSON
HERE;

        file_put_contents($tmpfile, $in);

        $data = new DataCollection('1234');

        $this->setExpectedException('\RuntimeException', 'File "' . $tmpfile . '" had invalid JSON.');
        $data->addSourceFile($tmpfile);

        unlink($tmpfile);
    }
}
