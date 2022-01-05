<?php

declare(strict_types=1);

namespace BrowscapTest\Data;

use Browscap\Data\Engine;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class EngineTest extends TestCase
{
    /**
     * tests setter and getter for the engine properties
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testGetProperties(): void
    {
        $properties = ['abc' => 'def'];

        $object = new Engine($properties);

        static::assertSame($properties, $object->getProperties());
    }
}
