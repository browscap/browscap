<?php

declare(strict_types=1);

namespace BrowscapTest\Filter\Division;

use Browscap\Data\Division as DataDivision;
use Browscap\Filter\Division\FullDivisionFilter;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class FullDivisionFilterTest extends TestCase
{
    /**
     * tests detecting if a divion should be in the output
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testIsOutput(): void
    {
        $object = $this->getMockForTrait(FullDivisionFilter::class);

        $division = $this->createMock(DataDivision::class);

        $division->expects(static::never())
            ->method('isStandard');

        $division->expects(static::never())
            ->method('isLite');

        static::assertTrue($object->isOutput($division));
    }
}
