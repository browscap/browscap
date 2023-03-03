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
    public function testAddFilterAndIsOutput(): void
    {
        $division = $this->createMock(Division::class);

        $mockFilter = $this->createMock(FilterInterface::class);
        $mockFilter
            ->expects(static::once())
            ->method('isOutput')
            ->with($division)
            ->willReturn(true);

        $this->object->addFilter($mockFilter);

        self::assertTrue($this->object->isOutput($division));
    }

    /**
     * tests setting and getting a writer
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testAddFilterAndIsOutput2(): void
    {
        $division = $this->createMock(Division::class);

        $mockFilter1 = $this->createMock(FilterInterface::class);
        $mockFilter1
            ->expects(static::once())
            ->method('isOutput')
            ->with($division)
            ->willReturn(true);

        $mockFilter2 = $this->createMock(FilterInterface::class);
        $mockFilter2
            ->expects(static::once())
            ->method('isOutput')
            ->with($division)
            ->willReturn(false);

        $mockFilter3 = $this->createMock(FilterInterface::class);
        $mockFilter3
            ->expects(static::never())
            ->method('isOutput');

        $this->object->addFilter($mockFilter1);
        $this->object->addFilter($mockFilter2);
        $this->object->addFilter($mockFilter3);

        self::assertFalse($this->object->isOutput($division));
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

        self::assertTrue($this->object->isOutputSection($section));
    }

    /**
     * tests setting and getting a writer
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testAddFilterAndIsOutputSection2(): void
    {
        $section = [];

        $mockFilter1 = $this->createMock(FilterInterface::class);
        $mockFilter1
            ->expects(static::once())
            ->method('isOutputSection')
            ->with($section)
            ->willReturn(true);

        $mockFilter2 = $this->createMock(FilterInterface::class);
        $mockFilter2
            ->expects(static::once())
            ->method('isOutputSection')
            ->with($section)
            ->willReturn(false);

        $mockFilter3 = $this->createMock(FilterInterface::class);
        $mockFilter3
            ->expects(static::never())
            ->method('isOutputSection');

        $this->object->addFilter($mockFilter1);
        $this->object->addFilter($mockFilter2);
        $this->object->addFilter($mockFilter3);

        self::assertFalse($this->object->isOutputSection($section));
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

        self::assertTrue($this->object->isOutputProperty($property, $mockWriter));
    }

    /**
     * tests setting and getting a writer
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testAddFilterAndIsOutputProperty2(): void
    {
        $mockWriter = $this->createMock(WriterInterface::class);
        $property   = 'test';

        $mockFilter1 = $this->createMock(FilterInterface::class);
        $mockFilter1
            ->expects(static::once())
            ->method('isOutputProperty')
            ->with($property, $mockWriter)
            ->willReturn(true);

        $mockFilter2 = $this->createMock(FilterInterface::class);
        $mockFilter2
            ->expects(static::once())
            ->method('isOutputProperty')
            ->with($property, $mockWriter)
            ->willReturn(false);

        $mockFilter3 = $this->createMock(FilterInterface::class);
        $mockFilter3
            ->expects(static::never())
            ->method('isOutputProperty');

        $this->object->addFilter($mockFilter1);
        $this->object->addFilter($mockFilter2);
        $this->object->addFilter($mockFilter3);

        self::assertFalse($this->object->isOutputProperty($property, $mockWriter));
    }
}
