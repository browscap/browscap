<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\Engine;

/**
 * Class EngineTest
 *
 * @category   BrowscapTest
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class EngineTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests setter and getter for the engine properties
     *
     * @group data
     * @group sourcetest
     */
    public function testGetProperties() : void
    {
        $properties = ['abc' => 'def'];

        $object = new Engine($properties);

        self::assertSame($properties, $object->getProperties());
    }
}
