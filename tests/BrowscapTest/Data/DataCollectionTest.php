<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\DataCollection;
use Browscap\Data\Device;
use Browscap\Data\Division;
use Browscap\Data\Engine;
use Browscap\Data\Platform;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class DataCollectionTest
 *
 * @category   BrowscapTest
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class DataCollectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\DataCollection
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp() : void
    {
        $logger = new Logger('browscap');
        $logger->pushHandler(new NullHandler(Logger::DEBUG));
        $this->object = new DataCollection($logger);
    }

    private function getPlatformsJsonFixture()
    {
        return __DIR__ . '/../../fixtures/platforms/platforms.json';
    }

    private function getEngineJsonFixture()
    {
        return __DIR__ . '/../../fixtures/engines/engines.json';
    }

    private function getDevicesJsonFixture()
    {
        return __DIR__ . '/../../fixtures/devices/devices.json';
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddPlatformsFileThrowsExceptionIfFileDoesNotExist() : void
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('File "/hopefully/this/file/does/not/exist" does not exist');

        $file = '/hopefully/this/file/does/not/exist';

        $this->object->addPlatformsFile($file);
    }

    /**
     * tests if a specific exception is thrown in case of error while adding a platform json file
     *
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddPlatformsFileThrowsExceptionIfFileContainsInvalidJson() : void
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('File "' . $tmpfile . '" had invalid JSON.');

        $in = <<<'HERE'
this is not valid JSON
HERE;

        file_put_contents($tmpfile, $in);

        $this->object->addPlatformsFile($tmpfile);
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddPlatformsFileThrowsExceptionIfFileContainsNoData() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('required "platforms" structure is missing');

        $this->object->addPlatformsFile(__DIR__ . '/../../fixtures/platforms/platforms-without-data.json');
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddPlatformsFileThrowsExceptionIfFileContainsNoMatch() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('required attibute "match" is missing');

        $this->object->addPlatformsFile(__DIR__ . '/../../fixtures/platforms/platforms-without-match.json');
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddPlatformsFileThrowsExceptionIfFileContainsNoProperties() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('required attibute "properties" is missing');

        $this->object->addPlatformsFile(__DIR__ . '/../../fixtures/platforms/platforms-without-properties.json');
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testGetPlatformThrowsExceptionIfPlatformDoesNotExist() : void
    {
        $this->expectException('\OutOfBoundsException');
        $this->expectExceptionMessage('Platform "NotExists" does not exist in data');

        $this->object->addPlatformsFile($this->getPlatformsJsonFixture());

        self::assertInternalType('array', $this->object->getPlatforms());
        $this->object->getPlatform('NotExists');
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testGetEngineThrowsExceptionIfEngineDoesNotExist() : void
    {
        $this->expectException('\OutOfBoundsException');
        $this->expectExceptionMessage('Rendering Engine "NotExists" does not exist in data');

        $this->object->addEnginesFile($this->getEngineJsonFixture());

        $this->object->getEngine('NotExists');
    }

    /**
     * tests getting an exiting platform
     *
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testGetPlatform() : void
    {
        $this->object->addPlatformsFile($this->getPlatformsJsonFixture());

        self::assertInternalType('array', $this->object->getPlatforms());
        $platform = $this->object->getPlatform('Platform1');

        self::assertInstanceOf(Platform::class, $platform);

        $properties = $platform->getProperties();

        self::assertInternalType('array', $properties);
        self::assertArrayHasKey('Platform', $properties);
        self::assertSame('Platform1', $properties['Platform']);
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddEnginesFileThrowsExceptionIfFileDoesNotExist() : void
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('File "/hopefully/this/file/does/not/exist" does not exist');

        $file = '/hopefully/this/file/does/not/exist';

        $this->object->addEnginesFile($file);
    }

    /**
     * tests if a specific exception is thrown in case of error while adding an engine json file
     *
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddEnginesFileThrowsExceptionIfFileContainsInvalidJson() : void
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('File "' . $tmpfile . '" had invalid JSON.');

        $in = <<<'HERE'
this is not valid JSON
HERE;

        file_put_contents($tmpfile, $in);

        $this->object->addEnginesFile($tmpfile);
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddEnginesFileThrowsExceptionIfFileContainsNoData() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('required "engines" structure is missing');

        $this->object->addEnginesFile(__DIR__ . '/../../fixtures/engines/engines-without-data.json');
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddEnginesFileThrowsExceptionIfFileContainsNoProperties() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('required attibute "properties" is missing');

        $this->object->addEnginesFile(__DIR__ . '/../../fixtures/engines/engines-without-properties.json');
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testGetDeviceThrowsExceptionIfDeviceDoesNotExist() : void
    {
        $this->expectException('\OutOfBoundsException');
        $this->expectExceptionMessage('Device "NotExists" does not exist in data');

        $this->object->addDevicesFile($this->getDevicesJsonFixture());

        $this->object->getDevice('NotExists');
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testGetDeviceOk() : void
    {
        $this->object->addDevicesFile($this->getDevicesJsonFixture());

        $device = $this->object->getDevice('unknown');

        self::assertInstanceOf(Device::class, $device);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testGetDevicesOk() : void
    {
        $this->object->addDevicesFile($this->getDevicesJsonFixture());

        $devices = $this->object->getDevices();

        self::assertInternalType('array', $devices);

        foreach ($devices as $device) {
            self::assertInstanceOf(Device::class, $device);
        }
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testGetEngineThrowsExceptionIfPlatformDoesNotExist() : void
    {
        $this->expectException('\OutOfBoundsException');
        $this->expectExceptionMessage('Rendering Engine "NotExists" does not exist in data');

        $this->object->addEnginesFile($this->getEngineJsonFixture());

        self::assertInternalType('array', $this->object->getEngines());
        $this->object->getEngine('NotExists');
    }

    /**
     * tests getting an existing engine
     *
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testGetEngine() : void
    {
        $this->object->addEnginesFile($this->getEngineJsonFixture());

        self::assertInternalType('array', $this->object->getEngines());
        $engine = $this->object->getEngine('Foobar');

        self::assertInstanceOf(Engine::class, $engine);
        $properties = $engine->getProperties();

        self::assertInternalType('array', $properties);
        self::assertArrayHasKey('RenderingEngine_Name', $properties);
        self::assertSame('Foobar', $properties['RenderingEngine_Name']);
    }

    /**
     * tests getting the generation date
     *
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testGetGenerationDate() : void
    {
        // Time isn't always exact, so allow a few seconds grace either way...
        $currentTime = time();
        $minTime     = $currentTime - 3;
        $maxTime     = $currentTime + 3;

        $testDateTime = $this->object->getGenerationDate();

        self::assertInstanceOf('\DateTime', $testDateTime);

        $testTime = $testDateTime->getTimestamp();
        self::assertGreaterThanOrEqual($minTime, $testTime);
        self::assertLessThanOrEqual($maxTime, $testTime);
    }

    /**
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddSourceFileThrowsExceptionIfFileDoesNotExist() : void
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('File "/hopefully/this/file/does/not/exist" does not exist');

        $file = '/hopefully/this/file/does/not/exist';

        $this->object->addSourceFile($file);
    }

    /**
     * checks if a exception is thrown if the source file had invalid json content
     *
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddSourceFileThrowsExceptionIfFileContainsInvalidJson() : void
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('File "' . $tmpfile . '" had invalid JSON.');

        $in = <<<'HERE'
this is not valid JSON
HERE;

        file_put_contents($tmpfile, $in);

        $this->object->addSourceFile($tmpfile);
    }

    /**
     * checks if a source file is added successful
     *
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddSourceFileOk() : void
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/test1.json');

        $divisions = $this->object->getDivisions();

        self::assertInternalType('array', $divisions);
        self::assertArrayHasKey(0, $divisions);
        self::assertInstanceOf(Division::class, $divisions[0]);
    }

    /**
     * checks if a source file is added successful
     *
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddSourceFileOkWithLiteAndVersions() : void
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/test2.json');

        $divisions = $this->object->getDivisions();

        self::assertInternalType('array', $divisions);
        self::assertArrayHasKey(0, $divisions);
        self::assertInstanceOf(Division::class, $divisions[0]);
    }

    /**
     * checks if the default properties are added sucessfully
     *
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddDefaultProperties() : void
    {
        $this->object->addDefaultProperties(__DIR__ . '/../../fixtures/ua/default-properties.json');

        $division = $this->object->getDefaultProperties();

        self::assertInstanceOf(Division::class, $division);
        self::assertSame('DefaultProperties', $division->getName());
    }

    /**
     * checks if the default browser is added sucessfully
     *
     * @group data
     * @group sourcetest
     *
     * @return void
     */
    public function testAddDefaultBrowser() : void
    {
        $this->object->addDefaultBrowser(__DIR__ . '/../../fixtures/ua/default-browser.json');

        $division = $this->object->getDefaultBrowser();

        self::assertInstanceOf(Division::class, $division);
        self::assertSame('Default Browser', $division->getName());
    }
}
