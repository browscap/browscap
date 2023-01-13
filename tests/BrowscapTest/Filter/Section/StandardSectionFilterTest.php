<?php

declare(strict_types=1);

namespace BrowscapTest\Filter\Section;

use Browscap\Filter\Section\StandardSectionFilter;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class StandardSectionFilterTest extends TestCase
{
    /**
     * tests if a section is always in the output
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testIsOutputSectionAlways(): void
    {
        $object = $this->getMockForTrait(StandardSectionFilter::class);

        static::assertTrue($object->isOutputSection([]));
        static::assertFalse($object->isOutputSection(['standard' => false]));
        static::assertTrue($object->isOutputSection(['standard' => true]));
    }
}
