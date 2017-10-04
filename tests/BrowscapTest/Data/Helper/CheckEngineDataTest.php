<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Helper;

use Browscap\Data\Helper\CheckEngineData;

/**
 * Class DataCollectionTestTest
 */
class CheckEngineDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Helper\CheckEngineData
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new CheckEngineData();
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
