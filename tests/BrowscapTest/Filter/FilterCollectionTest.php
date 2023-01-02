<?php

declare(strict_types=1);

namespace BrowscapTest\Filter;

use Browscap\Data\Division;
use Browscap\Filter\FilterCollection;
use Browscap\Filter\FilterInterface;
use Browscap\Writer\WriterInterface;
use PHPUnit\Framework\MockObject\MethodCannotBeConfiguredException;
use PHPUnit\Framework\MockObject\MethodNameAlreadyConfiguredException;
use PHPUnit\Framework\TestCase;

class FilterCollectionTest extends TestCase
{
    private FilterCollection $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new FilterCollection();
    }

    /**
     * tests setting and getting a writer
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testAddFilterAndIsSilent(): void
    {
        $division = $this->createMock(Division::class);

        $mockFilter = $this->createMock(FilterInterface::class);
        $mockFilter
            ->expects(static::once())
            ->method('isOutput')
            ->with($division)
            ->willReturn(true);

        $this->object->addFilter($mockFilter);

        $this->object->isOutput($division);
    }

    /**
     * tests setting and getting a writer
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testAddFilterAndIsOutputSection(): void
    {
        $section = [];

        $mockFilter = $this->createMock(FilterInterface::class);
        $mockFilter
            ->expects(static::once())
            ->method('isOutputSection')
            ->with($section)
            ->willReturn(true);

        $this->object->addFilter($mockFilter);

        $this->object->isOutputSection($section);
    }

    /**
     * tests setting and getting a writer
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testAddFilterAndIsOutputProperty(): void
    {
        $mockWriter = $this->createMock(WriterInterface::class);
        $property   = 'test';

        $mockFilter = $this->createMock(FilterInterface::class);
        $mockFilter
            ->expects(static::once())
            ->method('isOutputProperty')
            ->with($property, $mockWriter)
            ->willReturn(true);

        $this->object->addFilter($mockFilter);

        $this->object->isOutputProperty($property, $mockWriter);
    }
}
