<?php
declare(strict_types = 1);
namespace BrowscapTest\Filter;

use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\FilterInterface;
use Browscap\Filter\FullFilter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;

class FullFilterTest extends TestCase
{
    /**
     * tests getter for the filter type
     */
    public function testGetType() : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
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
     * @throws \ReflectionException
     */
    public function testIsOutput() : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::never())
            ->method('isOutputProperty');

        $object = new FullFilter($propertyHolder);

        $division = $this->createMock(Division::class);

        static::assertTrue($object->isOutput($division));
    }

    public function testIsOutputProperty() : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::once())
            ->method('isOutputProperty')
            ->willReturn(true);

        $object = new FullFilter($propertyHolder);

        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(static::never())
            ->method('getType')
            ->willReturn(WriterInterface::TYPE_INI);

        static::assertTrue($object->isOutputProperty('Comment', $mockWriterIni));
    }

    public function testIsOutputPropertyModified() : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::once())
            ->method('isOutputProperty')
            ->willReturn(false);

        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(static::never())
            ->method('getType');

        $object = new FullFilter($propertyHolder);
        static::assertFalse($object->isOutputProperty('Comment', $mockWriterIni));
    }

    /**
     * tests if a section is always in the output
     */
    public function testIsOutputSectionAlways() : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
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
