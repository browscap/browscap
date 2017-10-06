<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Browscap\Data\DataCollection;
use Browscap\Data\Device;
use Browscap\Data\Factory\PlatformFactory;
use Browscap\Data\Platform;

/**
 * Class PlatformFactoryTestTest
 */
class PlatformFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Factory\PlatformFactory
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        self::markTestSkipped();
        $this->object = new PlatformFactory();
    }

    /**
     * tests the creating of an platform factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildMissingLiteProperty() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the value for "lite" key is missing for the platform with the key "Test"');

        $platformData = ['abc' => 'def'];
        $json         = [];
        $platformName = 'Test';

        $this->object->build($platformData, $json, $platformName);
    }

    /**
     * tests the creating of an platform factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildMissingStandardProperty() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the value for "standard" key is missing for the platform with the key "Test"');

        $platformData = ['abc' => 'def', 'lite' => false];
        $json         = [];
        $platformName = 'Test';

        $this->object->build($platformData, $json, $platformName);
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildWithoutMatchProperty() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the value for the "match" key is missing for the platform with the key "Test"');

        $platformData = ['properties' => ['abc' => 'def'], 'lite' => false, 'standard' => false, 'inherits' => 'abc'];
        $json         = [
            'platforms' => [
                'abc' => [
                    'properties' => ['abc' => 'def'],
                    'standard' => false,
                    'lite' => false,
                ],
            ],
        ];
        $platformName = 'Test';

        $this->object->build($platformData, $json, $platformName);
    }

    /**
     * tests the creating of an platform factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildMissingParentPlatform() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('parent Platform "abc" is missing for platform "Test"');

        $platformData = ['abc' => 'def', 'match' => 'test*', 'lite' => false, 'standard' => false, 'inherits' => 'abc'];
        $json         = [];
        $platformName = 'Test';

        $this->object->build($platformData, $json, $platformName);
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildWithRepeatingProperties() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the value for property "abc" has the same value in the keys "Test" and its parent "abc"');

        $platformData = ['properties' => ['abc' => 'def'], 'match' => 'test*', 'lite' => false, 'standard' => false, 'inherits' => 'abc'];
        $json         = [
            'platforms' => [
                'abc' => [
                    'match' => 'test*',
                    'properties' => ['abc' => 'def'],
                    'standard' => false,
                    'lite' => false,
                ],
            ],
        ];
        $platformName = 'Test';

        $this->object->build($platformData, $json, $platformName);
    }

    /**
     * tests the creating of an platform factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuild() : void
    {
        $platformData = ['abc' => 'def', 'match' => 'test*', 'lite' => true, 'standard' => true];
        $json         = [];
        $platformName = 'Test';

        $deviceData = ['Device_Name' => 'TestDevice'];

        $deviceMock = $this->getMockBuilder(Device::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $deviceMock->expects(self::any())
            ->method('getProperties')
            ->will(self::returnValue($deviceData));

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDevice'])
            ->getMock();

        $collection->expects(self::any())
            ->method('getDevice')
            ->will(self::returnValue($deviceMock));

        self::assertInstanceOf(
            Platform::class,
            $this->object->build($platformData, $json, $platformName)
        );
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildOk() : void
    {
        $platformData = ['properties' => ['abc' => 'zyx'], 'match' => 'test*', 'lite' => true, 'standard' => true, 'inherits' => 'abc'];
        $json         = [
            'platforms' => [
                'abc' => [
                    'match' => 'test*',
                    'properties' => ['abc' => 'def'],
                    'standard' => false,
                    'lite' => false,
                ],
            ],
        ];
        $platformName = 'Test';

        $platform = $this->object->build($platformData, $json, $platformName);

        self::assertInstanceOf(Platform::class, $platform);
        self::assertFalse($platform->isLite());
        self::assertFalse($platform->isStandard());
    }
}
