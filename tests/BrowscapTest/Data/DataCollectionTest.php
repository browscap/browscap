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
     */
    public function setUp() : void
    {
        $logger       = new Logger('browscapTest', [new NullHandler()]);
        $this->object = new DataCollection('1234', $logger);
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

    private function getUserAgentFixtures()
    {
        $dir = __DIR__ . '/../../fixtures/ua';

        return [
            $dir . '/test1.json',
            $dir . '/test2.json',
            $dir . '/test3.json',
        ];
    }

    /**
     * @group data
     * @group sourcetest
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
     * tests getting the version
     *
     * @group data
     * @group sourcetest
     */
    public function testGetVersion() : void
    {
        self::assertSame('1234', $this->object->getVersion());
    }

    /**
     * tests getting the generation date
     *
     * @group data
     * @group sourcetest
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
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNoDivisionIsAvailable() : void
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('required attibute "division" is missing');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-divisions.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNoSortIndexIsAvailable() : void
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('required attibute "sortIndex" is missing');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-sortindex.json');
    }

    /**
     * checks if a exception is thrown if the lite property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNoLitePropertyIsAvailable() : void
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('required attibute "lite" is missing');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-lite-property.json');
    }

    /**
     * checks if a exception is thrown if the lite property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNoStandardPropertyIsAvailable() : void
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('required attibute "standard" is missing');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-standard-property.json');
    }

    /**
     * checks if a exception is thrown if the lite property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNoNameIsAvailableForUseragent() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('Name for Division is missing');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-useragent-name.json');
    }

    /**
     * checks if a exception is thrown if the lite property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNameHasInvalidCharsForUseragent() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('Name of Division "[UA1" includes invalid characters');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-invalid-useragent-name.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNoVersionsAreDefinedWithVersionPlaceholders() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('Division "UA1 #MAJORVER#.#MINORVER#" is defined with version placeholders, but no versions are set');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-defined-versions-with-placeholders.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNoPropertiesAreAvailable() : void
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('the properties entry is missing for key "UA1"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-properties.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfPropertiesEntryIsNotAnArray() : void
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('the properties entry has to be an array for key "UA1"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-properties-as-array.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNoParentPropertyIsAvailable() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the "Parent" property is missing for key "UA1"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-parent.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfParentPropertyIsNotDefaultProperties() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the "Parent" property is not linked to the "DefaultProperties" for key "UA1"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-default-as-parent.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNoCommentPropertyIsAvailable() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the "Comment" property is missing for key "UA1"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-comment.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNoVersionsAreDefinedButVersionPropertyIsAvailable() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the "Version" property is set for key "UA1", but no versions are defined');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-version-property-but-no-versions.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfNoChildrenPropertyIsAvailable() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the children property is missing for key "UA1"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-children.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenPropertyIsNotAnArray() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the children property has to be an array for key "UA1"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-children-array.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenIsNotAnArray() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the children property shall not have the "match" entry for key "UA1"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-match.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfPropertiesIncludePlatformData() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the properties array contains platform data for key "UA1", please use the "platform" keyword');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-platformdata.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfPropertiesIncludeEngineData() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the properties array contains engine data for key "UA1", please use the "engine" keyword');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-enginedata.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfPropertiesIncludeDeviceData() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the properties array contains device data for key "UA1", please use the "device" keyword');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-devicedata.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenAreNotArrays() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('each entry of the children property has to be an array for key "UA1"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-no-array.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenDoesNotHaveMatchKeyword() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('each entry of the children property requires an "match" entry for key "UA1"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-without-match.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHaveAnInvalidMatchKeyword() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('key "[cde" includes invalid characters');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-invalid-match.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenMatchKeywordHasVersionPlaceHolderWithoutVersions() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the key "cde #MAJORVER#.#MINORVER#" is defined with version placeholders, but no versions are set');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-version-property-but-no-versions.json');
    }

    /**
     * checks if an exception is thrown if the platforms property is missing, but the #PLATFORM# placeholder exists
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenMatchKeywordHasPlatformPlaceHolderWithoutPlatforms() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the key "cde #PLATFORM#" is defined with platform placeholder, but no platforms are assigned');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-platform-placeholder-but-no-platforms.json');
    }

    /**
     * checks if a exception is thrown if the devices property is missing, but the #DEVICE# placeholder exists
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenMatchKeywordHasDevicePlaceHolderWithoutDevices() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the key "cde #DEVICE#" is defined with device placeholder, but no devices are assigned');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-device-placeholder-but-no-devices.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenPropertiesAreNotArrays() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the properties entry has to be an array for key "cde"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-without-properties-array.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHasParentProperty() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the Parent property must not set inside the children array for key "cde"');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-parent-property.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHasPlatformProperties() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the properties array contains platform data for key "cde", please use the "platforms" keyword');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-platform-properties.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHasEngineProperties() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the properties array contains engine data for key "cde", please use the "engine" keyword');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-engine-properties.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHasDeviceProperties() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the properties array contains device data for key "cde", please use the "device" keyword');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-device-properties.json');
    }

    /**
     * checks if a exception is thrown if the device and devices keys are set
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfChildHasDeviceAndDevicesKeys() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('a child may not define both the "device" and the "devices" entries');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-device-and-devices.json');
    }

    /**
     * checks if a exception is thrown if the devices entry is not an array
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfDevicesEntryIsNotAnArray() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the "devices" entry has to be an array');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-devices-not-array.json');
    }

    /**
     * checks if an exception is thrown if the devices entry has multiple items and there is no #DEVICE# token
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfDevicesEntryHasMultipleDevicesAndNoDeviceToken() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "devices" entry contains multiple devices but there is no #DEVICE# token for key');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-devices-no-token.json');
    }

    /**
     * checks if an exception is thrown if the platforms entry has multiple items and there is no #PLATFORM# token
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfPlatformsEntryHasMultiplePlatformsAndNoPlatformToken() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "platforms" entry contains multiple platforms but there is no #PLATFORM# token for key');

        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-platforms-no-token.json');
    }

    /**
     * checks if a exception is thrown if a division is defined twice in the source files
     *
     * @group data
     * @group sourcetest
     */
    public function testAddSourceFileThrowsExceptionIfDivisionIsAddedTwice() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('Division "UA2" is defined twice');

        $files = $this->getUserAgentFixtures();

        foreach ($files as $file) {
            $this->object->addSourceFile($file);
        }
    }

    /**
     * checks if a source file is added successful
     *
     * @group data
     * @group sourcetest
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
     */
    public function testAddDefaultBrowser() : void
    {
        $this->object->addDefaultBrowser(__DIR__ . '/../../fixtures/ua/default-browser.json');

        $division = $this->object->getDefaultBrowser();

        self::assertInstanceOf(Division::class, $division);
        self::assertSame('Default Browser', $division->getName());
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutVersion() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('Version property not found for key "test"');

        $properties = [];
        $this->object->checkProperty('test', $properties);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutParent() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('Parent property is missing for key "test"');

        $properties = [
            'Version' => 'abc',
        ];

        $this->object->checkProperty('test', $properties);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutDeviceType() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('property "Device_Type" is missing for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
        ];

        $this->object->checkProperty('test', $properties);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutIsTablet() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('property "isTablet" is missing for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Desktop',
        ];

        $this->object->checkProperty('test', $properties);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutIsMobileDevice() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('property "isMobileDevice" is missing for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Desktop',
            'isTablet' => false,
        ];

        $this->object->checkProperty('test', $properties);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyOk() : void
    {
        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Desktop',
            'isTablet' => false,
            'isMobileDevice' => false,
        ];

        $this->object->checkProperty('test', $properties);
        // if the test fails, this will not reached
        self::assertTrue(true);
    }
}
