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
     * @var FullFilter
     */
    private $object;

    protected function setUp() : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::any())
            ->method('isOutputProperty')
            ->willReturn(true);

        $this->object = new FullFilter($propertyHolder);
    }

    /**
     * tests getter for the filter type
     */
    public function testGetType() : void
    {
        static::assertSame(FilterInterface::TYPE_FULL, $this->object->getType());
    }

    /**
     * tests detecting if a divion should be in the output
     *
     * @throws \ReflectionException
     */
    public function testIsOutput() : void
    {
        $division = $this->createMock(Division::class);

        static::assertTrue($this->object->isOutput($division));
    }

    public function testIsOutputProperty() : void
    {
        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(static::never())
            ->method('getType')
            ->willReturn(WriterInterface::TYPE_INI);

        static::assertTrue($this->object->isOutputProperty('Comment', $mockWriterIni));
    }

    public function testIsOutputPropertyModified() : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::any())
            ->method('isOutputProperty')
            ->willReturn(false);

        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(static::any())
            ->method('getType')
            ->willReturn(WriterInterface::TYPE_INI);

        $object = new FullFilter($propertyHolder);
        static::assertFalse($object->isOutputProperty('Comment', $mockWriterIni));
    }

    /**
     * tests if a section is always in the output
     */
    public function testIsOutputSectionAlways() : void
    {
        static::assertTrue($this->object->isOutputSection([]));
        static::assertTrue($this->object->isOutputSection(['full' => false]));
        static::assertTrue($this->object->isOutputSection(['full' => true]));
    }
}
