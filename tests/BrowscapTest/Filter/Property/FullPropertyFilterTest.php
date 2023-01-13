<?php

declare(strict_types=1);

namespace BrowscapTest\Filter\Property;

use Browscap\Data\PropertyHolder;
use Browscap\Filter\Property\FullPropertyFilter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterInterface;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class FullPropertyFilterTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
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

        $object = $this->getMockForTrait(
            FullPropertyFilter::class,
            [$propertyHolder],
        );

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

    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
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

        $object = $this->getMockForTrait(
            FullPropertyFilter::class,
            [$propertyHolder],
        );
        static::assertFalse($object->isOutputProperty('Comment', $mockWriterIni));
    }
}
