<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\Browser;
use PHPUnit\Framework\TestCase;

class BrowserTest extends TestCase
{
    /**
     * tests setter and getter for the match property
     */
    public function testGetter() : void
    {
        $properties = ['abc' => 'def'];
        $standard   = false;
        $lite       = true;
        $type       = 'tablet';

        $object = new Browser($properties, $type, $lite, $standard);

        self::assertSame($properties, $object->getProperties());
        self::assertFalse($object->isStandard());
        self::assertTrue($object->isLite());
        self::assertSame($type, $object->getType());
    }
}
