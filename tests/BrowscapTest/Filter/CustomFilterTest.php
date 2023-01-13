<?php

declare(strict_types=1);

namespace BrowscapTest\Filter;

use Browscap\Data\PropertyHolder;
use Browscap\Filter\CustomFilter;
use Browscap\Filter\FilterInterface;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class CustomFilterTest extends TestCase
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

        $object = new CustomFilter($propertyHolder, ['Parent']);

        static::assertSame(FilterInterface::TYPE_CUSTOM, $object->getType());
    }
}
