<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Validator;

use Browscap\Data\Validator\PropertiesValidator;
use LogicException;

/**
 * Class DataCollectionTestTest
 */
class PropertiesValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PropertiesValidator
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new PropertiesValidator();
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutVersion() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Version property not found for key "test"');

        $properties = [];
        $this->object->check('test', $properties);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutParent() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Parent property is missing for key "test"');

        $properties = [
            'Version' => 'abc',
        ];

        $this->object->check('test', $properties);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutDeviceType() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('property "Device_Type" is missing for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
        ];

        $this->object->check('test', $properties);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutIsTablet() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('property "isTablet" is missing for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Desktop',
        ];

        $this->object->check('test', $properties);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutIsMobileDevice() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('property "isMobileDevice" is missing for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Desktop',
            'isTablet' => false,
        ];

        $this->object->check('test', $properties);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @group data
     * @group sourcetest
     */
    public function testCheckTabletMismatchIsTablet() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the device of type "Tablet" is NOT marked as Tablet for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Tablet',
            'isTablet' => false,
            'isMobileDevice' => false,
        ];

        $this->object->check('test', $properties);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @group data
     * @group sourcetest
     */
    public function testCheckTabletMismatchIsMobileDevice() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the device of type "Tablet" is NOT marked as Mobile Device for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Tablet',
            'isTablet' => true,
            'isMobileDevice' => false,
        ];

        $this->object->check('test', $properties);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @group data
     * @group sourcetest
     */
    public function testCheckMobileMismatchIsTablet() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the device of type "Mobile Phone" is marked as Tablet for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Mobile Phone',
            'isTablet' => true,
            'isMobileDevice' => false,
        ];

        $this->object->check('test', $properties);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @group data
     * @group sourcetest
     */
    public function testCheckMobileMismatchIsMobileDevice() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the device of type "Mobile Phone" is NOT marked as Mobile Device for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Mobile Phone',
            'isTablet' => false,
            'isMobileDevice' => false,
        ];

        $this->object->check('test', $properties);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @group data
     * @group sourcetest
     */
    public function testCheckDesktopMismatchIsTablet() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the device of type "Desktop" is marked as Tablet for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Desktop',
            'isTablet' => true,
            'isMobileDevice' => true,
        ];

        $this->object->check('test', $properties);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @group data
     * @group sourcetest
     */
    public function testCheckDesktopMismatchIsMobileDevice() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the device of type "Desktop" is marked as Mobile Device for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Desktop',
            'isTablet' => false,
            'isMobileDevice' => true,
        ];

        $this->object->check('test', $properties);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyOkDesktop() : void
    {
        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Desktop',
            'isTablet' => false,
            'isMobileDevice' => false,
        ];

        $this->object->check('test', $properties);

        self::assertTrue(true);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyOkTablet() : void
    {
        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Tablet',
            'isTablet' => true,
            'isMobileDevice' => true,
        ];

        $this->object->check('test', $properties);

        self::assertTrue(true);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyOkMobile() : void
    {
        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
            'Device_Type' => 'Mobile Phone',
            'isTablet' => false,
            'isMobileDevice' => true,
        ];

        $this->object->check('test', $properties);

        self::assertTrue(true);
    }
}
