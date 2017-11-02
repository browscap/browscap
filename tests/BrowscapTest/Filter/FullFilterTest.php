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

    public function setUp() : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(self::any())
            ->method('isOutputProperty')
            ->will(self::returnValue(true));

        $this->object = new FullFilter($propertyHolder);
    }

    /**
     * tests getter for the filter type
     */
    public function testGetType() : void
    {
        self::assertSame(FilterInterface::TYPE_FULL, $this->object->getType());
    }

    /**
     * tests detecting if a divion should be in the output
     */
    public function testIsOutput() : void
    {
        $division = $this->createMock(Division::class);

        self::assertTrue($this->object->isOutput($division));
    }

    public function testIsOutputProperty() : void
    {
        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(self::never())
            ->method('getType')
            ->will(self::returnValue(WriterInterface::TYPE_INI));

        self::assertTrue($this->object->isOutputProperty('Comment', $mockWriterIni));
    }

    public function testIsOutputPropertyModified() : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(self::any())
            ->method('isOutputProperty')
            ->will(self::returnValue(false));

        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(self::any())
            ->method('getType')
            ->will(self::returnValue(WriterInterface::TYPE_INI));

        $object = new FullFilter($propertyHolder);
        self::assertFalse($object->isOutputProperty('Comment', $mockWriterIni));
    }

    /**
     * tests if a section is always in the output
     */
    public function testIsOutputSectionAlways() : void
    {
        $this->assertTrue($this->object->isOutputSection([]));
        $this->assertTrue($this->object->isOutputSection(['full' => false]));
        $this->assertTrue($this->object->isOutputSection(['full' => true]));
    }
}
