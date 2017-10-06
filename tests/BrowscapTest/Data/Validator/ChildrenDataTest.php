<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Validator;

use Browscap\Data\Device;
use Browscap\Data\Helper\DeviceDataPropertyValidator;
use Browscap\Data\Helper\EngineDataPropertyValidator;
use Browscap\Data\Helper\PlatformDataPropertyValidator;
use Browscap\Data\Platform;
use Browscap\Data\UserAgent;
use Browscap\Data\Validator\ChildrenDataValidator;

/**
 * Class ChildrenDataTestTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class ChildrenDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Validator\ChildrenDataValidator
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        self::markTestSkipped();
        $checkDeviceData   = $this->createMock(DeviceDataPropertyValidator::class);
        $checkEngineData   = $this->createMock(EngineDataPropertyValidator::class);
        $checkPlatformData = $this->createMock(PlatformDataPropertyValidator::class);

        $this->object = new ChildrenDataValidator();

        $property = new \ReflectionProperty($this->object, 'checkDeviceData');
        $property->setAccessible(true);
        $property->setValue($this->object, $checkDeviceData);

        $property = new \ReflectionProperty($this->object, 'checkEngineData');
        $property->setAccessible(true);
        $property->setValue($this->object, $checkEngineData);

        $property = new \ReflectionProperty($this->object, 'checkPlatformData');
        $property->setAccessible(true);
        $property->setValue($this->object, $checkPlatformData);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testDeviceAndDevicesPropertiesAreAvailable() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('a child may not define both the "device" and the "devices" entries for key "testUA", for child data: {"device":[],"devices":"def"}');

        $childData = [
            'device' => [],
            'devices' => 'def',
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testDevicesPropertyIsNotAnArray() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "devices" entry for key "testUA" has to be an array for child data: {"devices":"def"}');

        $childData = [
            'devices' => 'def',
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testDevicePropertyIsNotString() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "device" entry has to be a string for key "testUA", for child data: {"device":[]}');

        $childData = [
            'device' => [],
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testMatchPropertyIsNotAvailable() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('each entry of the children property requires an "match" entry for key "testUA", missing for child data: {"device":"abc"}');

        $childData = [
            'device' => 'abc',
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testMatchPropertyIsNotString() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "match" entry for key "testUA" has to be a string for child data: {"device":"abc","match":[]}');

        $childData = [
            'device' => 'abc',
            'match' => [],
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testMatchPropertyIncludesInvalidCharacters() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('key "[abc" includes invalid characters');

        $childData = [
            'device' => 'abc',
            'match' => '[abc',
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testMatchPropertyIncludesPlatformPlaceholder() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the key "abc#PLATFORM#" is defined with platform placeholder, but no platforms are assigned');

        $childData = [
            'device' => 'abc',
            'match' => 'abc#PLATFORM#',
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testPlatformsPropertyIsNotAnArray() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "platforms" entry for key "testUA" has to be an array for child data: {"device":"abc","match":"abc","platforms":"abc"}');

        $childData = [
            'device' => 'abc',
            'match' => 'abc',
            'platforms' => 'abc',
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testMultiplePlatformsWithoutPlatformPlaceholder() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "platforms" entry contains multiple platforms but there is no #PLATFORM# token for key "testUA", for child data: {"device":"abc","match":"abc","platforms":["abc","def"]}');

        $childData = [
            'device' => 'abc',
            'match' => 'abc',
            'platforms' => ['abc', 'def'],
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testVersionPlaceholderIsAvailableButNoVersions() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the key "abc#MAJORVER#" is defined with version placeholders, but no versions are set');

        $childData = [
            'device' => 'abc',
            'match' => 'abc#MAJORVER#',
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = ['0.0'];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testNoVersionPlaceholderIsAvailableButMultipleVersions() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the key "abc" is defined without version placeholders, but there are versions set');

        $childData = [
            'device' => 'abc',
            'match' => 'abc',
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = ['1', '2'];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testNoVersionPlaceholderIsAvailableButMultipleVersionsAndNoDynamicPlatform() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the key "abc#PLATFORM#" is defined without version placeholders, but there are versions set');

        $childData = [
            'device' => 'abc',
            'match' => 'abc#PLATFORM#',
            'platforms' => ['abc', 'def'],
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = ['1', '2'];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testNoVersionPlaceholderIsAvailableButMultipleVersionsButWithDynamicPlatform() : void
    {
        $childData = [
            'device' => 'abc',
            'match' => 'abc#PLATFORM#',
            'platforms' => ['abc', 'def_dynamic'],
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = ['1', '2'];

        $this->object->validate($childData, $useragentData, $versions);
        self::assertTrue(true);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testDevicePlaceholderIsAvailableButNoDevices() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the key "abc#DEVICE#" is defined with device placeholder, but no devices are assigned');

        $childData = [
            'device' => 'abc',
            'match' => 'abc#DEVICE#',
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testNoDevicePlaceholderIsAvailableButMultipleDevices() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "devices" entry contains multiple devices but there is no #DEVICE# token for key "testUA", for child data: {"match":"abc","devices":["cdf","xyz"]}');

        $childData = [
            'match' => 'abc',
            'devices' => ['cdf', 'xyz'],
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testOkWithoutProperties() : void
    {
        $childData = [
            'match' => 'abc#DEVICE#',
            'devices' => ['cdf', 'xyz'],
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
        self::assertTrue(true);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testPropertiesPropertyIsNotAnArray() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the properties entry has to be an array for key "abc"');

        $childData = [
            'device' => 'abc',
            'match' => 'abc',
            'properties' => 'test',
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testPropertiesPropertyHasParent() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the Parent property must not set inside the children array for key "abc"');

        $childData = [
            'device' => 'abc',
            'match' => 'abc',
            'properties' => ['Parent' => 'test'],
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testPropertiesPropertyHasVersionSameAsParent() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "Version" property is set for key "abc", but was already set for its parent "testUA" with the same value');

        $childData = [
            'device' => 'abc',
            'match' => 'abc',
            'properties' => ['Version' => 'test'],
        ];

        $useragentData = [
            'userAgent' => 'testUA',
            'properties' => ['Version' => 'test'],
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testOkWithProperties() : void
    {
        $childData = [
            'device' => 'abc',
            'match' => 'abc',
            'properties' => ['Version' => 'test'],
        ];

        $useragentData = [
            'userAgent' => 'testUA',
        ];

        $versions = [];

        $this->object->validate($childData, $useragentData, $versions);
        self::assertTrue(true);
    }
}
