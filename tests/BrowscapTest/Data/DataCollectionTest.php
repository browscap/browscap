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
     * @var \Browscap\Data\DataCollection
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    public function setUp()
    {
        $this->logger = new Logger('browscapTest', array(new NullHandler()));
        $this->object = new DataCollection('1234');
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

    /**
     * tests the setter and the getter for a logger
     */
    public function testSetGetLogger()
    {
        $logger = $this->getMock('\Monolog\Logger', array(), array(), '', false);

        self::assertSame($this->object, $this->object->setLogger($logger));
        self::assertSame($logger, $this->object->getLogger());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage File "/hopefully/this/file/does/not/exist" does not exist
     */
    public function testAddPlatformsFileThrowsExceptionIfFileDoesNotExist()
    {
        $file = '/hopefully/this/file/does/not/exist';

        $this->object->addPlatformsFile($file);
    }

    public function testAddPlatformsFileThrowsExceptionIfFileContainsInvalidJson()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
this is not valid JSON
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addPlatformsFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\RuntimeException" not thrown, no exception thrown';
        } catch (\RuntimeException $ex) {
            if ('File "' . $tmpfile . '" had invalid JSON.' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message \'File "' . $tmpfile
                . '" had invalid JSON.\' not available, the message was "' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\RuntimeException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage required "platforms" structure is missing
     */
    public function testAddPlatformsFileThrowsExceptionIfFileContainsNoData()
    {
        $this->object->addPlatformsFile(__DIR__ . '/../../fixtures/platforms/platforms-without-data.json');
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage required attibute "match" is missing
     */
    public function testAddPlatformsFileThrowsExceptionIfFileContainsNoMatch()
    {
        $this->object->addPlatformsFile(__DIR__ . '/../../fixtures/platforms/platforms-without-match.json');
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage required attibute "properties" is missing
     */
    public function testAddPlatformsFileThrowsExceptionIfFileContainsNoProperties()
    {
        $this->object->addPlatformsFile(__DIR__ . '/../../fixtures/platforms/platforms-without-properties.json');
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Platform "NotExists" does not exist in data
     */
    public function testGetPlatformThrowsExceptionIfPlatformDoesNotExist()
    {
        $this->object->addPlatformsFile($this->getPlatformsJsonFixture());

        self::assertInternalType('array', $this->object->getPlatforms());
        $this->object->getPlatform('NotExists');
    }

    public function testGetPlatform()
    {
        $this->object->addPlatformsFile($this->getPlatformsJsonFixture());

        self::assertInternalType('array', $this->object->getPlatforms());
        $platform = $this->object->getPlatform('Platform1');

        self::assertInstanceOf('\Browscap\Data\Platform', $platform);

        $properties = $platform->getProperties();

        self::assertInternalType('array', $properties);
        self::assertArrayHasKey('Platform', $properties);
        self::assertSame('Platform1', $properties['Platform']);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage File "/hopefully/this/file/does/not/exist" does not exist
     */
    public function testAddEnginesFileThrowsExceptionIfFileDoesNotExist()
    {
        $file = '/hopefully/this/file/does/not/exist';

        $this->object->addEnginesFile($file);
    }

    public function testAddEnginesFileThrowsExceptionIfFileContainsInvalidJson()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
this is not valid JSON
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addEnginesFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\RuntimeException" not thrown, no exception thrown';
        } catch (\RuntimeException $ex) {
            if ('File "' . $tmpfile . '" had invalid JSON.' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message \'File "' . $tmpfile
                . '" had invalid JSON.\' not available, the message was "' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\RuntimeException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage required "engines" structure is missing
     */
    public function testAddEnginesFileThrowsExceptionIfFileContainsNoData()
    {
        $this->object->addEnginesFile(__DIR__ . '/../../fixtures/engines/engines-without-data.json');
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage required attibute "properties" is missing
     */
    public function testAddEnginesFileThrowsExceptionIfFileContainsNoProperties()
    {
        $this->object->addEnginesFile(__DIR__ . '/../../fixtures/engines/engines-without-properties.json');
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Rendering Engine "NotExists" does not exist in data
     */
    public function testGetEngineThrowsExceptionIfPlatformDoesNotExist()
    {
        $this->object->addEnginesFile($this->getEngineJsonFixture());

        self::assertInternalType('array', $this->object->getEngines());
        $this->object->getEngine('NotExists');
    }

    public function testGetEngine()
    {
        $this->object->addEnginesFile($this->getEngineJsonFixture());

        self::assertInternalType('array', $this->object->getEngines());
        $engine = $this->object->getEngine('Foobar');

        self::assertInstanceOf('\Browscap\Data\Engine', $engine);
        $properties = $engine->getProperties();

        self::assertInternalType('array', $properties);
        self::assertArrayHasKey('RenderingEngine_Name', $properties);
        self::assertSame('Foobar', $properties['RenderingEngine_Name']);
    }

    public function testGetVersion()
    {
        self::assertSame('1234', $this->object->getVersion());
    }

    public function testGetGenerationDate()
    {
        // Time isn't always exact, so allow a few seconds grace either way...
        $currentTime = time();
        $minTime = $currentTime - 3;
        $maxTime = $currentTime + 3;

        $testDateTime = $this->object->getGenerationDate();

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
        $files = $this->getUserAgentFixtures();

        foreach ($files as $file) {
            $this->object->addSourceFile($file);
        }
    }

    /**
     * checks if a source file is added successful
     */
    public function testAddSourceFileOk()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/test1.json');

        $divisions = $this->object->getDivisions();

        self::assertInternalType('array', $divisions);
        self::assertArrayHasKey(0, $divisions);
        self::assertInstanceOf('\Browscap\Data\Division', $divisions[0]);
    }

    /**
     * checks if a source file is added successful
     */
    public function testAddSourceFileOkWithLiteAndVersions()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/test2.json');

        $divisions = $this->object->getDivisions();

        self::assertInternalType('array', $divisions);
        self::assertArrayHasKey(0, $divisions);
        self::assertInstanceOf('\Browscap\Data\Division', $divisions[0]);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage File "/hopefully/this/file/does/not/exist" does not exist
     */
    public function testAddSourceFileThrowsExceptionIfFileDoesNotExist()
    {
        $file = '/hopefully/this/file/does/not/exist';

        $this->object->addSourceFile($file);
    }

    /**
     * checks if a exception is thrown if the source file had invalid json content
     */
    public function testAddSourceFileThrowsExceptionIfFileContainsInvalidJson()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
this is not valid JSON
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\RuntimeException" not thrown, no exception thrown';
        } catch (\RuntimeException $ex) {
            if ('File "' . $tmpfile . '" had invalid JSON.' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message \'File "' . $tmpfile
                . '" had invalid JSON.\' not available, the message was "' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\RuntimeException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage required attibute "division" is missing
     */
    public function testAddSourceFileThrowsExceptionIfNoDivisionIsAvailable()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-divisions.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage required attibute "sortIndex" is missing
     */
    public function testAddSourceFileThrowsExceptionIfNoSortIndexIsAvailable()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-sortindex.json');
    }

    /**
     * checks if a exception is thrown if the lite property is missing
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage required attibute "lite" is missing
     */
    public function testAddSourceFileThrowsExceptionIfNoLitePropertyIsAvailable()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-lite-property.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage the properties entry has to be an array for key "UA1"
     */
    public function testAddSourceFileThrowsExceptionIfNoPropertiesAreAvailable()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-properties.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage the "Parent" property is missing for key "UA1"
     */
    public function testAddSourceFileThrowsExceptionIfNoParentPropertyIsAvailable()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-parent.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage the "Comment" property is missing for key "UA1"
     */
    public function testAddSourceFileThrowsExceptionIfNoCommentPropertyIsAvailable()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-without-comment.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage the properties array contains platform data for key "UA1", please use the "platform" keyword
     */
    public function testAddSourceFileThrowsExceptionIfPropertiesIncludePlatformData()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-platformdata.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage the properties array contains engine data for key "UA1", please use the "engine" keyword
     */
    public function testAddSourceFileThrowsExceptionIfPropertiesIncludeEngineData()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-enginedata.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage the children property shall not have the "match" entry for key "UA1"
     */
    public function testAddSourceFileThrowsExceptionIfChildrenIsNotAnArray()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-match.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage each entry of the children property has to be an array for key "UA1"
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHaveMatchProperty()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-no-array.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage each entry of the children property requires an "match" entry for key "UA1"
     */
    public function testAddSourceFileThrowsExceptionIfChildrenDoesNotHaveMatchKeyword()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-without-match.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage the properties entry has to be an array for key "cde"
     */
    public function testAddSourceFileThrowsExceptionIfChildrenPropertiesAreNotArrays()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-without-properties-array.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage the Parent property must not set inside the children array for key "cde"
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHasParentProperty()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-parent-property.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage the properties array contains platform data for key "cde", please use the "platforms" keyword
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHasPlatformProperties()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-platform-properties.json');
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage the properties array contains engine data for key "cde", please use the "engine" keyword
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHasEngineProperties()
    {
        $this->object->addSourceFile(__DIR__ . '/../../fixtures/ua/ua-with-children-with-engine-properties.json');
    }

    /**
     * checks if the default properties are added sucessfully
     */
    public function testAddDefaultProperties()
    {
        self::assertSame(
            $this->object,
            $this->object->addDefaultProperties(__DIR__ . '/../../fixtures/ua/default-properties.json')
        );

        $division = $this->object->getDefaultProperties();

        self::assertInstanceOf('\Browscap\Data\Division', $division);
        self::assertSame('DefaultProperties', $division->getName());
    }

    /**
     * checks if the default browser is added sucessfully
     */
    public function testAddDefaultBrowser()
    {
        self::assertSame(
            $this->object,
            $this->object->addDefaultBrowser(__DIR__ . '/../../fixtures/ua/default-browser.json')
        );

        $division = $this->object->getDefaultBrowser();

        self::assertInstanceOf('\Browscap\Data\Division', $division);
        self::assertSame('Default Browser', $division->getName());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Version property not found for key "test"
     */
    public function testCheckPropertyWithoutVersion()
    {
        $this->object->setLogger($this->logger);

        $properties = array();
        $this->object->checkProperty('test', $properties);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Parent property is missing for key "test"
     */
    public function testCheckPropertyWithoutParent()
    {
        $this->object->setLogger($this->logger);

        $properties = array(
            'Version' => 'abc'
        );

        $this->object->checkProperty('test', $properties);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage property "Device_Type" is missing for key "test"
     */
    public function testCheckPropertyWithoutDeviceType()
    {
        $this->object->setLogger($this->logger);

        $properties = array(
            'Version' => 'abc',
            'Parent'  => '123',
        );

        $this->object->checkProperty('test', $properties);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage property "isTablet" is missing for key "test"
     */
    public function testCheckPropertyWithoutIsTablet()
    {
        $this->object->setLogger($this->logger);

        $properties = array(
            'Version'     => 'abc',
            'Parent'      => '123',
            'Device_Type' => 'Desktop',
        );

        $this->object->checkProperty('test', $properties);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage property "isMobileDevice" is missing for key "test"
     */
    public function testCheckPropertyWithoutIsMobileDevice()
    {
        $this->object->setLogger($this->logger);

        $properties = array(
            'Version'     => 'abc',
            'Parent'      => '123',
            'Device_Type' => 'Desktop',
            'isTablet'    => false,
        );

        $this->object->checkProperty('test', $properties);
    }

    public function testCheckPropertyOk()
    {
        $this->object->setLogger($this->logger);

        $properties = array(
            'Version'        => 'abc',
            'Parent'         => '123',
            'Device_Type'    => 'Desktop',
            'isTablet'       => false,
            'isMobileDevice' => false,
        );

        self::assertTrue($this->object->checkProperty('test', $properties));
    }
}
