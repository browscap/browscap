<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Validator;

use Browscap\Data\Validator\PropertiesValidator;
use LogicException;
use PHPUnit\Framework\TestCase;

class PropertiesValidatorTest extends TestCase
{
    /**
     * @var PropertiesValidator
     */
    private $object;

    public function setUp() : void
    {
        $this->object = new PropertiesValidator();
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testCheckPropertyWithoutVersion() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Version property not found for key "test"');

        $properties = [];
        $this->object->validate($properties, 'test');
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testCheckPropertyWithoutParent() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Parent property is missing for key "test"');

        $properties = [
            'Version' => 'abc',
        ];

        $this->object->validate($properties, 'test');
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testCheckPropertyWithoutDeviceType() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('property "Device_Type" is missing for key "test"');

        $properties = [
            'Version' => 'abc',
            'Parent' => '123',
        ];

        $this->object->validate($properties, 'test');
    }

    /**
     * @throws \Assert\AssertionFailedException
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

        $this->object->validate($properties, 'test');
    }

    /**
     * @throws \Assert\AssertionFailedException
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

        $this->object->validate($properties, 'test');
    }

    /**
     * @throws \Assert\AssertionFailedException
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

        $this->object->validate($properties, 'test');
    }

    /**
     * @throws \Assert\AssertionFailedException
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

        $this->object->validate($properties, 'test');
    }

    /**
     * @throws \Assert\AssertionFailedException
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

        $this->object->validate($properties, 'test');
    }

    /**
     * @throws \Assert\AssertionFailedException
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

        $this->object->validate($properties, 'test');
    }

    /**
     * @throws \Assert\AssertionFailedException
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

        $this->object->validate($properties, 'test');
    }

    /**
     * @throws \Assert\AssertionFailedException
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

        $this->object->validate($properties, 'test');
    }

    /**
     * tests if no error is raised if all went well
     *
     * @throws \Assert\AssertionFailedException
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

        $this->object->validate($properties, 'test');

        self::assertTrue(true);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @throws \Assert\AssertionFailedException
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

        $this->object->validate($properties, 'test');

        self::assertTrue(true);
    }

    /**
     * tests if no error is raised if all went well
     *
     * @throws \Assert\AssertionFailedException
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

        $this->object->validate($properties, 'test');

        self::assertTrue(true);
    }
}
