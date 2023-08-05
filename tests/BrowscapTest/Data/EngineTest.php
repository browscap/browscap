<?php

declare(strict_types=1);

namespace BrowscapTest\Data;

use Browscap\Data\Engine;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
    /**
     * tests setter and getter for the engine properties
     *
     * @throws ExpectationFailedException
     */
    public function testGetProperties(): void
    {
        $properties = ['RenderingEngine_Name' => 'def'];

        $object = new Engine($properties);

        static::assertSame($properties, $object->getProperties());
    }
}
