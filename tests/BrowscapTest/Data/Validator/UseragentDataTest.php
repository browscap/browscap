<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Validator;

use Browscap\Data\Division;
use Browscap\Data\Helper\DeviceDataPropertyValidator;
use Browscap\Data\Helper\EngineDataPropertyValidator;
use Browscap\Data\Helper\PlatformDataPropertyValidator;
use Browscap\Data\UserAgent;
use Browscap\Data\Validator\UseragentDataValidator;

/**
 * Class UseragentDataTestTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class UseragentDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Validator\UseragentDataValidator
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

        $this->object = new UseragentDataValidator();

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
    public function testUserAgentPropertyIsNotAvailable() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('Name for Division is missing in file "abc.json"');

        $useragentData = [];

        $versions     = [];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testUserAgentPropertyIsNotString() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('Name of Division has to be a string in file "abc.json"');

        $useragentData = [
            'userAgent' => [],
        ];

        $versions     = [];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testUserAgentPropertyHasInvalidCharacters() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('Name of Division "abc[" includes invalid characters in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc[',
        ];

        $versions     = [];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testUserAgentPropertisDefinedTwicy() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('Division "abc" is defined twice in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
        ];

        $versions     = [];
        $allDivisions = ['abc'];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testUserAgentPropertyHasVersionPlaceholdersButNoVersions() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('Division "abc#MAJORVER#" is defined with version placeholders, but no versions are set in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc#MAJORVER#',
        ];

        $versions     = ['0.0'];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testUserAgentPropertyHasNoVersionPlaceholdersButMultipleVersions() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('Division "abc" is defined without version placeholders, but there are versions set in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
        ];

        $versions     = ['1', '2'];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testPropertiesPropertyIsNotAvailable() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the properties entry is missing for key "abc" in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
        ];

        $versions     = [];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testPropertiesPropertyIsNotArray() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the properties entry has to be an non-empty array for key "abc" in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
            'properties' => 'bcd',
        ];

        $versions     = [];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testParentPropertyIsMissingInProperties() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "Parent" property is missing for key "abc" in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['abc'],
        ];

        $versions     = [];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testWrongParentProperty() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "Parent" property is not linked to the "DefaultProperties" for key "abc" in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['Parent' => 'abc'],
        ];

        $versions     = [];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCommentPropertyIsMissingInProperties() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "Comment" property is missing for key "abc" in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['Parent' => 'DefaultProperties'],
        ];

        $versions     = [];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCommentPropertyIsNotString() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "Comment" property has to be a string for key "abc" in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['Parent' => 'DefaultProperties', 'Comment' => []],
        ];

        $versions     = [];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testVersionPropertyIsMissingInProperties() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "Version" property is missing for key "abc" in file "abc.json", but there are defined versions');

        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test'],
        ];

        $versions     = ['1'];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testOkForCore() : void
    {
        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test'],
        ];

        $versions     = ['0.0'];
        $allDivisions = [];
        $isCore       = true;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
        self::assertTrue(true);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testVersionPropertyIsNotString() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "Version" property has to be a string for key "abc" in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => []],
        ];

        $versions     = ['0.0'];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testVersionPropertyHasPlaceholdersButNoVersions() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "Version" property has version placeholders for key "abc" in file "abc.json", but no versions are defined');

        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '#MAJORVER#'],
        ];

        $versions     = ['0.0'];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testVersionPropertyHasNoPlaceholdersButMultipleVersions() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the "Version" property has no version placeholders for key "abc#MAJORVER#" in file "abc.json", but versions are defined');

        $useragentData = [
            'userAgent' => 'abc#MAJORVER#',
            'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
        ];

        $versions     = ['1', '2'];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testChildrenPropertyIsMissingInProperties() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the children property is missing for key "abc" in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
        ];

        $versions     = ['1'];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testChildrenPropertyIsNotAnArray() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the children property has to be an array for key "abc" in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
            'children' => 'xyz',
        ];

        $versions     = ['1'];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testChildrenPropertyHasDirectMatch() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('the children property shall not have the "match" entry for key "abc" in file "abc.json"');

        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
            'children' => ['match' => '123'],
        ];

        $versions     = ['1'];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testOk() : void
    {
        $useragentData = [
            'userAgent' => 'abc',
            'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
            'children' => [['match' => '123']],
        ];

        $versions     = ['1'];
        $allDivisions = [];
        $isCore       = false;
        $filename     = 'abc.json';

        $this->object->validate($useragentData, $versions, $allDivisions, $isCore, $filename);
        self::assertTrue(true);
    }
}
