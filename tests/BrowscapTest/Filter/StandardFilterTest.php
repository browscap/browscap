<?php

declare(strict_types=1);

namespace BrowscapTest\Filter;

use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\FilterInterface;
use Browscap\Filter\StandardFilter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;

class StandardFilterTest extends TestCase
{
    /**
     * tests getter for the filter type
     */
    public function testGetType(): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::never())
            ->method('isOutputProperty');

        $object = new StandardFilter($propertyHolder);

        static::assertSame(FilterInterface::TYPE_STANDARD, $object->getType());
    }

    /**
     * tests detecting if a divion should be in the output
     */
    public function testIsOutputTrue(): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::never())
            ->method('isOutputProperty');

        $object = new StandardFilter($propertyHolder);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['isStandard'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('isStandard')
            ->willReturn(true);

        static::assertTrue($object->isOutput($division));
    }

    /**
     * tests detecting if a divion should be in the output
     */
    public function testIsOutputFalse(): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::never())
            ->method('isOutputProperty');

        $object = new StandardFilter($propertyHolder);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['isStandard'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('isStandard')
            ->willReturn(false);

        static::assertFalse($object->isOutput($division));
    }

    /**
     * Data Provider for the test testIsOutputProperty
     *
     * @return array<int, array<int, bool|string>>
     */
    public function outputPropertiesDataProvider(): array
    {
        return [
            ['Comment', true],
            ['Browser', true],
            ['Platform', true],
            ['Platform_Description', false],
            ['Device_Name', false],
            ['Device_Maker', false],
            ['RenderingEngine_Name', false],
            ['RenderingEngine_Description', false],
            ['Parent', true],
            ['Platform_Version', false],
            ['RenderingEngine_Version', false],
            ['Version', true],
            ['MajorVer', true],
            ['MinorVer', true],
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
            ['isMobileDevice', true],
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
     * @dataProvider outputPropertiesDataProvider
     */
    public function testIsOutputProperty(string $propertyName, bool $isExtra): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::once())
            ->method('isOutputProperty')
            ->willReturn($isExtra);

        $object = new StandardFilter($propertyHolder);

        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(static::any())
            ->method('getType')
            ->willReturn(WriterInterface::TYPE_INI);

        $actualValue = $object->isOutputProperty($propertyName, $mockWriterIni);
        static::assertSame($isExtra, $actualValue);
    }

    /**
     * @dataProvider outputPropertiesDataProvider
     */
    public function testIsOutputPropertyWithPropertyHolder(string $propertyName): void
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

        $object = new StandardFilter($propertyHolder);
        static::assertFalse($object->isOutputProperty($propertyName, $mockWriterIni));
    }

    /**
     * tests if a section is always in the output
     */
    public function testIsOutputSectionAlways(): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::never())
            ->method('isOutputProperty');

        $object = new StandardFilter($propertyHolder);

        static::assertTrue($object->isOutputSection([]));
        static::assertFalse($object->isOutputSection(['standard' => false]));
        static::assertTrue($object->isOutputSection(['standard' => true]));
    }
}
