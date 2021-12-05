<?php

declare(strict_types=1);

namespace BrowscapTest\Formatter;

use Browscap\Data\PropertyHolder;
use Browscap\Formatter\FormatterInterface;
use Browscap\Formatter\XmlFormatter;
use Exception;
use PHPUnit\Framework\TestCase;

use function sprintf;

class XmlFormatterTest extends TestCase
{
    /** @var XmlFormatter */
    private $object;

    protected function setUp(): void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(static::never())
            ->method('isOutputProperty');

        $this->object = new XmlFormatter($propertyHolder);
    }

    /**
     * tests getter for the formatter type
     */
    public function testGetType(): void
    {
        static::assertSame(FormatterInterface::TYPE_XML, $this->object->getType());
    }

    /**
     * tests formatting a property name
     */
    public function testFormatPropertyName(): void
    {
        static::assertSame('text', $this->object->formatPropertyName('text'));
    }

    /**
     * Data Provider for the test testGetPropertyType
     *
     * @return array<array<string>>
     */
    public function propertyNameTypeDataProvider(): array
    {
        return [
            ['Comment', 'test', 'test'],
            ['Browser', 'test', 'test'],
            ['Platform', 'test', 'test'],
            ['Platform_Description', 'test', 'test'],
            ['Device_Name', 'test', 'test'],
            ['Device_Maker', 'test', 'test'],
            ['RenderingEngine_Name', 'test', 'test'],
            ['RenderingEngine_Description', 'test', 'test'],
            ['Parent', 'test', 'test'],
            ['Platform_Version', 'test', 'test'],
            ['RenderingEngine_Version', 'test', 'test'],
            ['Version', 'test', 'test'],
            ['MajorVer', 'test', 'test'],
            ['MinorVer', 'test', 'test'],
            ['CssVersion', 'test', 'test'],
            ['AolVersion', 'test', 'test'],
            ['Alpha', 'true', 'true'],
            ['Beta', 'false', 'false'],
            ['Win16', 'test', ''],
            ['Win32', 'test', ''],
            ['Win64', 'test', ''],
            ['Frames', 'test', ''],
            ['IFrames', 'test', ''],
            ['Tables', 'test', ''],
            ['Cookies', 'test', ''],
            ['BackgroundSounds', 'test', ''],
            ['JavaScript', 'test', ''],
            ['VBScript', 'test', ''],
            ['JavaApplets', 'test', ''],
            ['ActiveXControls', 'test', ''],
            ['isMobileDevice', 'test', ''],
            ['isSyndicationReader', 'test', ''],
            ['Crawler', 'test', ''],
            ['Browser_Type', 'Browser', 'Browser'],
            ['Device_Type', 'Tablet', 'Tablet'],
            ['Device_Pointing_Method', 'test', ''],
        ];
    }

    /**
     * tests formatting a property value
     *
     * @throws Exception
     *
     * @dataProvider propertyNameTypeDataProvider
     */
    public function testFormatPropertyValue(string $propertyName, string $inputValue, string $expectedValue): void
    {
        $actualValue = $this->object->formatPropertyValue($inputValue, $propertyName);
        static::assertSame($expectedValue, $actualValue, sprintf('Property %s should be %s (was %s)', $propertyName, $expectedValue, $actualValue));
    }

    /**
     * tests formatting a property value
     *
     * @throws Exception
     */
    public function testFormatPropertyValueWithException(): void
    {
        $actualValue = $this->object->formatPropertyValue('Browserx', 'Device_Pointing_Method');
        static::assertSame('', $actualValue);
    }

    /**
     * tests formatting a property value
     *
     * @throws Exception
     */
    public function testFormatPropertyValueWithUnknownValue(): void
    {
        $actualValue = $this->object->formatPropertyValue('unknown', 'Browser_Type');
        static::assertSame('', $actualValue);
    }
}
