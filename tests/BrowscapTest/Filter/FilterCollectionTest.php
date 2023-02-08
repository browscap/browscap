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

use function range;

class FilterCollectionTest extends TestCase
{
    private FilterCollection $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new FilterCollection();
    }

    /**
     * tests getter for the filter type with filters of a different types, and without filters
     *
     * @dataProvider dataProviderForTestGetType
     */
    public function testGetType(int $noOfFilters): void
    {
        for ($i = 0; $i < $noOfFilters; $i++) {
            $mockFilter = $this->createMock(FilterInterface::class);
            $mockFilter
                ->expects(static::never())
                ->method('getType');
            $mockFilter
                ->expects(static::never())
                ->method('isOutput');
            $mockFilter
                ->expects(static::never())
                ->method('isOutputSection');
            $mockFilter
                ->expects(static::never())
                ->method('isOutputProperty');

            $this->object->addFilter($mockFilter);
        }

        static::assertSame(FilterInterface::TYPE_COLLECTION, $this->object->getType());
    }

    /**
     * Data Provider for testGetType
     *
     * @return array<array<int>>
     */
    public static function dataProviderForTestGetType(): array
    {
        $funcArgs = [];

        foreach (range(0, 10) as $noOfFilters) {
            $funcArgs[] = [$noOfFilters];
        }

        return $funcArgs;
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
