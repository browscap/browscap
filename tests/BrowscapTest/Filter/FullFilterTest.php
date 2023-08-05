<?php

declare(strict_types=1);

namespace BrowscapTest\Filter;

use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\FilterInterface;
use Browscap\Filter\FullFilter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterInterface;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class FullFilterTest extends TestCase
{
    /**
     * tests getter for the filter type
     *
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

        $object = new FullFilter($propertyHolder);

        static::assertSame(FilterInterface::TYPE_FULL, $object->getType());
    }

    /**
     * tests detecting if a divion should be in the output
     *
     * @throws ExpectationFailedException
     */
    public function testIsOutput(): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::never())
            ->method('isOutputProperty');

        $object = new FullFilter($propertyHolder);

        $division = $this->createMock(Division::class);

        static::assertTrue($object->isOutput($division));
    }

    /** @throws ExpectationFailedException */
    public function testIsOutputProperty(): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::once())
            ->method('isOutputProperty')
            ->willReturn(true);

        $object = new FullFilter($propertyHolder);

        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(static::never())
            ->method('getType')
            ->willReturn(WriterInterface::TYPE_INI);

        static::assertTrue($object->isOutputProperty('Comment', $mockWriterIni));
    }

    /** @throws ExpectationFailedException */
    public function testIsOutputPropertyModified(): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::once())
            ->method('isOutputProperty')
            ->willReturn(false);

        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(static::never())
            ->method('getType');

        $object = new FullFilter($propertyHolder);
        static::assertFalse($object->isOutputProperty('Comment', $mockWriterIni));
    }

    /**
     * tests if a section is always in the output
     *
     * @throws ExpectationFailedException
     */
    public function testIsOutputSectionAlways(): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::never())
            ->method('isOutputProperty');

        $object = new FullFilter($propertyHolder);

        static::assertTrue($object->isOutputSection([]));
        static::assertTrue($object->isOutputSection(['full' => false]));
        static::assertTrue($object->isOutputSection(['full' => true]));
    }
}
