<?php

declare(strict_types=1);

namespace BrowscapTest\Filter\Division;

use Browscap\Data\Division as DataDivision;
use Browscap\Filter\Division\StandardDivisionFilter;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class StandardDivisionFilterTest extends TestCase
{
    /**
     * tests detecting if a divion should be in the output
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testIsOutputTrue(): void
    {
        $object = $this->getMockForTrait(StandardDivisionFilter::class);

        $division = $this->getMockBuilder(DataDivision::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isStandard'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('isStandard')
            ->willReturn(true);

        static::assertTrue($object->isOutput($division));
    }

    /**
     * tests detecting if a divion should be in the output
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testIsOutputFalse(): void
    {
        $object = $this->getMockForTrait(StandardDivisionFilter::class);

        $division = $this->getMockBuilder(DataDivision::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isStandard'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('isStandard')
            ->willReturn(false);

        static::assertFalse($object->isOutput($division));
    }
}
