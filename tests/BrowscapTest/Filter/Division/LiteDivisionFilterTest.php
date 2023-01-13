<?php

declare(strict_types=1);

namespace BrowscapTest\Filter\Division;

use Browscap\Data\Division as DataDivision;
use Browscap\Filter\Division\LiteDivisionFilter;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class LiteDivisionFilterTest extends TestCase
{
    /**
     * tests detecting if a divion should be in the output
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testIsOutput(): void
    {
        $object = $this->getMockForTrait(LiteDivisionFilter::class);

        $division = $this->getMockBuilder(DataDivision::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isLite'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('isLite')
            ->willReturn(false);

        static::assertFalse($object->isOutput($division));
    }
}
