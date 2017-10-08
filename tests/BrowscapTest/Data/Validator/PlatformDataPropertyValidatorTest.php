<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Validator;

use Browscap\Data\Validator\PlatformDataPropertyValidator;
use LogicException;

/**
 * Class DataCollectionTestTest
 */
class PlatformDataPropertyValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PlatformDataPropertyValidator
     */
    private $object;

    public function setUp() : void
    {
        self::markTestSkipped();
        $this->object = new PlatformDataPropertyValidator();
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithPlatformProperties() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('error message');

        $properties = ['Platform' => 'test'];
        $this->object->check($properties, 'error message');
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutPlatformProperties() : void
    {
        $properties = [];
        $this->object->check($properties, 'error message');
        self::assertTrue(true);
    }
}
