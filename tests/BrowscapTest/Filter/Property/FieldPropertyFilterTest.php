<?php

declare(strict_types=1);

namespace BrowscapTest\Filter\Property;

use Browscap\Data\PropertyHolder;
use Browscap\Filter\Property\FieldPropertyFilter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterInterface;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class FieldPropertyFilterTest extends TestCase
{
    /**
     * Data Provider for the test testIsOutputProperty
     *
     * @return array<int, array<int, bool|string>>
     *
     * @throws void
     */
    public function outputPropertiesDataProvider(): array
    {
        return [
            ['Comment', false],
            ['Browser', false],
            ['Platform', false],
            ['Platform_Description', false],
            ['Device_Name', false],
            ['Device_Maker', false],
            ['RenderingEngine_Name', false],
            ['RenderingEngine_Description', false],
            ['Parent', true],
            ['Platform_Version', false],
            ['RenderingEngine_Version', false],
            ['Version', false],
            ['MajorVer', false],
            ['MinorVer', false],
            ['CssVersion', false],
            ['AolVersion', false],
            ['Alpha', false],
            ['Beta', false],
            ['Win16', false],
            ['Win32', false],
            ['Win64', false],
            ['Frames', false],
            ['IFrames', false],
            ['Tables', false],
            ['Cookies', false],
            ['BackgroundSounds', false],
            ['JavaScript', false],
            ['VBScript', false],
            ['JavaApplets', false],
            ['ActiveXControls', false],
            ['isMobileDevice', false],
            ['isSyndicationReader', false],
            ['Crawler', false],
            ['lite', false],
            ['sortIndex', false],
            ['Parents', false],
            ['division', false],
            ['Browser_Type', false],
            ['Device_Type', false],
            ['Device_Pointing_Method', false],
            ['isTablet', false],
            ['Browser_Maker', false],
        ];
    }

    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @dataProvider outputPropertiesDataProvider
     */
    public function testIsOutputProperty(string $propertyName, bool $isExtra): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::once())
            ->method('isOutputProperty')
            ->willReturn($isExtra);

        $object = $this->getMockForTrait(
            FieldPropertyFilter::class,
            [$propertyHolder, ['Parent']],
        );

        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(static::never())
            ->method('getType')
            ->willReturn(WriterInterface::TYPE_INI);

        static::assertSame($isExtra, $object->isOutputProperty($propertyName, $mockWriterIni));
    }

    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @dataProvider outputPropertiesDataProvider
     */
    public function testIsOutputPropertyModified(string $propertyName): void
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
            ->method('getType')
            ->willReturn(WriterInterface::TYPE_INI);

        $object = $this->getMockForTrait(
            FieldPropertyFilter::class,
            [$propertyHolder, ['Parent']],
        );
        static::assertFalse($object->isOutputProperty($propertyName, $mockWriterIni));
    }

    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @dataProvider outputPropertiesDataProvider
     */
    public function testIsOutputPropertyWithPropertyHolder(string $propertyName): void
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
            FieldPropertyFilter::class,
            [$propertyHolder, ['Parent']],
        );
        static::assertFalse($object->isOutputProperty($propertyName, $mockWriterIni));
    }
}
