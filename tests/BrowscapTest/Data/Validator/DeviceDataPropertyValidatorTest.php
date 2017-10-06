<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Validator;

use Browscap\Data\Validator\DeviceDataPropertyValidator;

/**
 * Class DataCollectionTestTest
 */
class DeviceDataPropertyValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DeviceDataPropertyValidator
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new DeviceDataPropertyValidator();
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithDeviceProperties() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('error message');

        $properties = ['Device_Name' => 'test'];
        $this->object->check($properties, 'error message');
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutDeviceProperties() : void
    {
        $properties = [];
        $this->object->check($properties, 'error message');
        self::assertTrue(true);
    }
}
