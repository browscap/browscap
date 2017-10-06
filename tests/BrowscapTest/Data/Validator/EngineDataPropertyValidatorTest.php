<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Validator;

use Browscap\Data\Validator\EngineDataPropertyValidator;

/**
 * Class DataCollectionTestTest
 */
class EngineDataPropertyValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var EngineDataPropertyValidator
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        self::markTestSkipped();
        $this->object = new EngineDataPropertyValidator();
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithEngineProperties() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('error message');

        $properties = ['RenderingEngine_Name' => 'test'];
        $this->object->check($properties, 'error message');
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithoutEngineProperties() : void
    {
        $properties = [];
        $this->object->check($properties, 'error message');
        self::assertTrue(true);
    }
}
