<?php

declare(strict_types=1);

namespace BrowscapTest\Filter\Section;

use Browscap\Filter\Section\LiteSectionFilter;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class LiteSectionFilterTest extends TestCase
{
    /**
     * tests if a section is always in the output, if the lite flag is true
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testIsOutputSectionOnlyWhenLite(): void
    {
        $object = $this->getMockForTrait(LiteSectionFilter::class);

        static::assertFalse($object->isOutputSection([]));
        static::assertFalse($object->isOutputSection(['lite' => false]));
        static::assertTrue($object->isOutputSection(['lite' => true]));
    }
}
