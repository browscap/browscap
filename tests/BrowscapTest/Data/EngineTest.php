<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\Engine;

/**
 * Class EngineTestTest
 */
class EngineTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests setter and getter for the engine properties
     */
    public function testGetProperties() : void
    {
        $properties = ['abc' => 'def'];

        $object = new Engine($properties);

        self::assertSame($properties, $object->getProperties());
    }
}
