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
                $message = 'expected Message \'File "' . $tmpfile . '" had invalid JSON.\' not available, the message was "' . $ex->getMessage() . '"';
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
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Platform "NotExists" does not exist in data
     */
    public function testGetPlatformThrowsExceptionIfPlatformDoesNotExist()
    {
        $this->object->addPlatformsFile($this->getPlatformsJsonFixture());

        $this->object->getPlatform('NotExists');
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
                $message = 'expected Message \'File "' . $tmpfile . '" had invalid JSON.\' not available, the message was "' . $ex->getMessage() . '"';
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
     */
    public function testAddSourceFileThrowsExceptionIfNoDivisionIsAvailable()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "sortIndex": 200,
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Parent": "DefaultProperties",
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      }
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\RuntimeException" not thrown, no exception thrown';
        } catch (\RuntimeException $ex) {
            if ('required attibute "division" is missing' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "required attibute "division" is missing" not available, the message was "' . $ex->getMessage() . '"';
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
     */
    public function testAddSourceFileThrowsExceptionIfNoSortIndexIsAvailable()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Parent": "DefaultProperties",
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      }
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\RuntimeException" not thrown, no exception thrown';
        } catch (\RuntimeException $ex) {
            if ('required attibute "sortIndex" is missing' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "required attibute "sortIndex" is missing" not available, the message was "' . $ex->getMessage() . '"';
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
     */
    public function testAddSourceFileThrowsExceptionIfNoPropertiesAreAvailable()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "sortIndex": 200,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "UA1"
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, no exception thrown';
        } catch (\UnexpectedValueException $ex) {
            if ('the properties entry has to be an array for key "UA1"' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "the properties entry has to be an array for key "UA1"" not available, the message was "' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     */
    public function testAddSourceFileThrowsExceptionIfNoParentPropertyIsAvailable()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "sortIndex": 200,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      }
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, no exception thrown';
        } catch (\UnexpectedValueException $ex) {
            if ('the "Parent" property is missing for key "UA1"' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "the "Parent" property is missing for key "UA1"" not available, the message was "' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     */
    public function testAddSourceFileThrowsExceptionIfPropertiesIncludePlatformData()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "sortIndex": 200,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Parent": "DefaultProperties",
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0",
        "Platform": "xyz"
      }
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\LogicException" not thrown, no exception thrown';
        } catch (\LogicException $ex) {
            if ('the properties array contains platform data for key "UA1", please use the "platform" keyword' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "the properties array contains platform data for key "UA1", please use the "platform" keyword" not available, the message was "' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\LogicException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     */
    public function testAddSourceFileThrowsExceptionIfPropertiesIncludeEngineData()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "sortIndex": 200,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Parent": "DefaultProperties",
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0",
        "RenderingEngine_Name": "xyz"
      }
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\LogicException" not thrown, no exception thrown';
        } catch (\LogicException $ex) {
            if ('the properties array contains engine data for key "UA1", please use the "engine" keyword' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "the properties array contains engine data for key "UA1", please use the "engine" keyword" not available, the message was "' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\LogicException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     */
    public function testAddSourceFileThrowsExceptionIfChildrenIncludeMatchKeyword()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "sortIndex": 200,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Parent": "DefaultProperties",
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      },
      "children": {
        "match": "xyz"
      }
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, no exception thrown';
        } catch (\UnexpectedValueException $ex) {
            if ('the children property has to be an array of arrays for key "UA1"' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "the children property has to be an array of arrays for key "UA1"" not available, the message was "' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     */
    public function testAddSourceFileThrowsExceptionIfChildrenAreNotArrays()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "sortIndex": 200,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Parent": "DefaultProperties",
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      },
      "children": {
        "abc": "cde"
      }
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, no exception thrown';
        } catch (\UnexpectedValueException $ex) {
            if ('each entry of the children property has to be an array for key "UA1"' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "each entry of the children property has to be an array for key "UA1"" not available, the message was "' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     */
    public function testAddSourceFileThrowsExceptionIfChildrenDoesNotHaveMatchKeyword()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "sortIndex": 200,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Parent": "DefaultProperties",
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      },
      "children": [
        {
          "abc": "cde"
        }
      ]
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, no exception thrown';
        } catch (\UnexpectedValueException $ex) {
            if ('each entry of the children property requires an "match" entry for key "UA1"' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "each entry of the children property requires an "match" entry for key "UA1"" not available, the message was "' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     */
    public function testAddSourceFileThrowsExceptionIfChildrenPropertiesAreNotArrays()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "sortIndex": 200,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Parent": "DefaultProperties",
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      },
      "children": [
        {
          "match": "cde",
          "properties": "efg"
        }
      ]
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, no exception thrown';
        } catch (\UnexpectedValueException $ex) {
            if ('the properties entry has to be an array for key "cde"' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "the properties entry has to be an array for key "cde"" not available, the message was "' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHasParentProperty()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "sortIndex": 200,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Parent": "DefaultProperties",
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      },
      "children": [
        {
          "match": "cde",
          "properties": {
            "Parent": "efg"
          }
        }
      ]
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, no exception thrown';
        } catch (\UnexpectedValueException $ex) {
            if ('the Parent property must not set inside the children array for key "cde"' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "the Parent property must not set inside the children array for key "cde"' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\UnexpectedValueException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHasPlatformProperties()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "sortIndex": 200,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Parent": "DefaultProperties",
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      },
      "children": [
        {
          "match": "cde",
          "properties": {
            "Platform_Description": "efg"
          }
        }
      ]
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\LogicException" not thrown, no exception thrown';
        } catch (\LogicException $ex) {
            if ('the properties array contains platform data for key "cde", please use the "platforms" keyword' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'expected Message "the properties array contains platform data for key "cde", please use the "platforms" keyword' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\LogicException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if a exception is thrown if the sortindex property is missing
     */
    public function testAddSourceFileThrowsExceptionIfChildrenHasEngineProperties()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Division1",
  "sortIndex": 200,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "UA1",
      "properties": {
        "Parent": "DefaultProperties",
        "Comment": "UA1",
        "Browser": "UA1",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      },
      "children": [
        {
          "match": "cde",
          "properties": {
            "RenderingEngine_Maker": "efg"
          }
        }
      ]
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        $fail    = false;
        $message = '';

        try {
            $this->object->addSourceFile($tmpfile);
            $fail    = true;
            $message = 'expected Exception "\LogicException" not thrown, no exception thrown';
        } catch (\LogicException $ex) {
            if ('the properties array contains engine data for key "cde", please use the "engine" keyword' !== $ex->getMessage()) {
                $fail    = true;
                $message = 'the properties array contains engine data for key "cde", please use the "engine" keyword' . $ex->getMessage() . '"';
            }
        } catch (\Exception $ex) {
            $fail    = true;
            $message = 'expected Exception "\LogicException" not thrown, Exception ' . get_class($ex) .' thrown';
        }

        unlink($tmpfile);

        if ($fail) {
            $this->fail($message);
        }
    }

    /**
     * checks if the default properties are added sucessfully
     */
    public function testAddDefaultProperties()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "Defaultproperties",
  "sortIndex": 0,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "Defaultproperties",
      "properties": {
        "Comment": "Defaultproperties",
        "Browser": "Defaultproperties",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      }
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        self::assertSame($this->object, $this->object->addDefaultProperties($tmpfile));

        unlink($tmpfile);

        $division = $this->object->getDefaultProperties();

        self::assertInstanceOf('\Browscap\Data\Division', $division);
        self::assertSame('Defaultproperties', $division->getName());
    }

    /**
     * checks if the default browser is added sucessfully
     */
    public function testAddDefaultBrowser()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
{
  "division": "*",
  "sortIndex": 0,
  "lite": true,
  "userAgents": [
    {
      "userAgent": "*",
      "properties": {
        "Comment": "Default Browser",
        "Browser": "Default Browser",
        "Version": "1.0",
        "MajorVer": "1",
        "MinorVer": "0"
      }
    }
  ]
}
HERE;

        file_put_contents($tmpfile, $in);

        self::assertSame($this->object, $this->object->addDefaultBrowser($tmpfile));

        unlink($tmpfile);

        $division = $this->object->getDefaultBrowser();

        self::assertInstanceOf('\Browscap\Data\Division', $division);
        self::assertSame('*', $division->getName());
    }
}
