<?php

declare(strict_types=1);

namespace BrowscapTest\Filter;

use Browscap\Data\PropertyHolder;
use Browscap\Filter\FilterInterface;
use Browscap\Filter\StandardFilter;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class StandardFilterTest extends TestCase
{
    /**
     * tests getter for the filter type
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testGetType(): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::never())
            ->method('isOutputProperty');

        $object = new StandardFilter($propertyHolder);

        static::assertSame(FilterInterface::TYPE_STANDARD, $object->getType());
    }
}
