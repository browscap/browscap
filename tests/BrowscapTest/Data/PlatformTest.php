<?php

declare(strict_types=1);

namespace BrowscapTest\Data;

use Browscap\Data\Platform;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class PlatformTest extends TestCase
{
    /**
     * tests setter and getter for the match property
     *
     * @throws ExpectationFailedException
     */
    public function testGetter(): void
    {
        $match      = 'TestMatchName';
        $properties = ['Platform' => 'def'];

        $object = new Platform($match, $properties, true, false);

        static::assertSame($match, $object->getMatch());
        static::assertSame($properties, $object->getProperties());
        static::assertTrue($object->isLite());
        static::assertFalse($object->isStandard());
    }
}
