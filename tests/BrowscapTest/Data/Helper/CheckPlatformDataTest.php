<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Helper;

use Browscap\Data\Helper\CheckPlatformData;

/**
 * Class DataCollectionTestTest
 */
class CheckPlatformDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Helper\CheckPlatformData
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new CheckPlatformData();
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testCheckPropertyWithPlatformProperties() : void
    {
        $this->expectException('\LogicException');
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
