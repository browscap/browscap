<?php

declare(strict_types=1);

namespace BrowscapTest\Filter\Property;

use Browscap\Data\PropertyHolder;
use Browscap\Filter\Property\LitePropertyFilter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterInterface;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class LitePropertyFilterTest extends TestCase
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
            ['isMobileDevice', true],
            ['isSyndicationReader', false],
            ['Crawler', false],
            ['lite', false],
            ['sortIndex', false],
            ['Parents', false],
            ['division', false],
            ['Browser_Type', false],
            ['Device_Type', true],
            ['Device_Pointing_Method', false],
            ['isTablet', true],
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
            ->willReturn(true);

        $object = $this->getMockForTrait(
            LitePropertyFilter::class,
            [$propertyHolder],
        );

        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(static::any())
            ->method('getType')
            ->willReturn(WriterInterface::TYPE_INI);

        $actualValue = $object->isOutputProperty($propertyName, $mockWriterIni);
        static::assertSame($isExtra, $actualValue);
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
            LitePropertyFilter::class,
            [$propertyHolder],
        );
        static::assertFalse($object->isOutputProperty($propertyName, $mockWriterIni));
    }
}
