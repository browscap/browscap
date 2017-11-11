<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\PropertyHolder;
use Browscap\Writer\CsvWriter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PropertyHolderTest extends TestCase
{
    /**
     * @var PropertyHolder
     */
    private $object;

    public function setUp() : void
    {
        $this->object = new PropertyHolder();
    }

    /**
     * Data Provider for the test testGetPropertyType
     *
     * @return array<string|string>[]
     */
    public function propertyNameTypeDataProvider()
    {
        return [
            ['Comment', PropertyHolder::TYPE_STRING],
            ['Browser', PropertyHolder::TYPE_STRING],
            ['Platform', PropertyHolder::TYPE_STRING],
            ['Platform_Description', PropertyHolder::TYPE_STRING],
            ['Device_Name', PropertyHolder::TYPE_STRING],
            ['Device_Maker', PropertyHolder::TYPE_STRING],
            ['RenderingEngine_Name', PropertyHolder::TYPE_STRING],
            ['RenderingEngine_Description', PropertyHolder::TYPE_STRING],
            ['Parent', PropertyHolder::TYPE_STRING],
            ['Platform_Version', PropertyHolder::TYPE_GENERIC],
            ['RenderingEngine_Version', PropertyHolder::TYPE_GENERIC],
            ['Version', PropertyHolder::TYPE_NUMBER],
            ['MajorVer', PropertyHolder::TYPE_NUMBER],
            ['MinorVer', PropertyHolder::TYPE_NUMBER],
            ['CssVersion', PropertyHolder::TYPE_NUMBER],
            ['AolVersion', PropertyHolder::TYPE_NUMBER],
            ['Alpha', PropertyHolder::TYPE_BOOLEAN],
            ['Beta', PropertyHolder::TYPE_BOOLEAN],
            ['Win16', PropertyHolder::TYPE_BOOLEAN],
            ['Win32', PropertyHolder::TYPE_BOOLEAN],
            ['Win64', PropertyHolder::TYPE_BOOLEAN],
            ['Frames', PropertyHolder::TYPE_BOOLEAN],
            ['IFrames', PropertyHolder::TYPE_BOOLEAN],
            ['Tables', PropertyHolder::TYPE_BOOLEAN],
            ['Cookies', PropertyHolder::TYPE_BOOLEAN],
            ['BackgroundSounds', PropertyHolder::TYPE_BOOLEAN],
            ['JavaScript', PropertyHolder::TYPE_BOOLEAN],
            ['VBScript', PropertyHolder::TYPE_BOOLEAN],
            ['JavaApplets', PropertyHolder::TYPE_BOOLEAN],
            ['ActiveXControls', PropertyHolder::TYPE_BOOLEAN],
            ['isMobileDevice', PropertyHolder::TYPE_BOOLEAN],
            ['isSyndicationReader', PropertyHolder::TYPE_BOOLEAN],
            ['isFake', PropertyHolder::TYPE_BOOLEAN],
            ['isAnonymized', PropertyHolder::TYPE_BOOLEAN],
            ['isModified', PropertyHolder::TYPE_BOOLEAN],
            ['Crawler', PropertyHolder::TYPE_BOOLEAN],
            ['Browser_Type', PropertyHolder::TYPE_IN_ARRAY],
            ['Device_Type', PropertyHolder::TYPE_IN_ARRAY],
            ['Device_Pointing_Method', PropertyHolder::TYPE_IN_ARRAY],
            ['PatternId', PropertyHolder::TYPE_STRING],
            ['PropertyName', PropertyHolder::TYPE_STRING],
        ];
    }

    /**
     * @dataProvider propertyNameTypeDataProvider
     *
     * @param string $propertyName
     * @param string $expectedType
     */
    public function testGetPropertyType(string $propertyName, string $expectedType) : void
    {
        $actualType = $this->object->getPropertyType($propertyName);
        self::assertSame($expectedType, $actualType, "Property {$propertyName} should be {$expectedType} (was {$actualType})");
    }

    public function testGetPropertyTypeThrowsExceptionIfPropertyNameNotMapped() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property Foobar did not have a defined property type');

        $this->object->getPropertyType('Foobar');
    }

    /**
     * Data Provider for the test testIsLiteModeProperty
     *
     * @return array<string|boolean>[]
     */
    public function litePropertiesDataProvider()
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
            ['isFake', false],
            ['isAnonymized', false],
            ['isModified', false],
            ['Crawler', false],
            ['Browser_Type', false],
            ['Device_Type', true],
            ['Device_Pointing_Method', false],
            ['Browser_Maker', false],
            ['isTablet', true],
            ['PatternId', false],
        ];
    }

    /**
     * @dataProvider litePropertiesDataProvider
     *
     * @param string $propertyName
     * @param bool   $isExtra
     */
    public function testIsLiteModeProperty(string $propertyName, bool $isExtra) : void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriter
            ->expects(self::any())
            ->method('getType')
            ->will(self::returnValue(WriterInterface::TYPE_CSV));

        $actualValue = $this->object->isLiteModeProperty($propertyName, $mockWriter);
        self::assertSame($isExtra, $actualValue);
    }

    /**
     * tests detecting a standard mode property
     */
    public function testIsLiteModePropertyWithWriter() : void
    {
        $mockWriter = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue(WriterInterface::TYPE_INI));

        self::assertTrue($this->object->isLiteModeProperty('PatternId', $mockWriter));
    }

    /**
     * Data Provider for the test testIsStandardModeProperty
     *
     * @return array<string|boolean>[]
     */
    public function standardPropertiesDataProvider()
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
            ['Parent', false],
            ['Platform_Version', false],
            ['RenderingEngine_Version', false],
            ['Version', false],
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
            ['isMobileDevice', false],
            ['isSyndicationReader', false],
            ['isFake', false],
            ['isAnonymized', false],
            ['isModified', false],
            ['Crawler', true],
            ['Browser_Type', false],
            ['Device_Type', false],
            ['Device_Pointing_Method', true],
            ['Browser_Maker', true],
            ['isTablet', false],
            ['PatternId', false],
        ];
    }

    /**
     * tests detecting a standard mode property
     */
    public function testIsLiteModePropertyWithIniWriter() : void
    {
        $mockWriter = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue(WriterInterface::TYPE_INI));

        self::assertTrue($this->object->isLiteModeProperty('PatternId', $mockWriter));
    }

    /**
     * @dataProvider standardPropertiesDataProvider
     *
     * @param string $propertyName
     * @param bool   $isExtra
     */
    public function testIsStandardModeProperty(string $propertyName, bool $isExtra) : void
    {
        $mockWriter = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriter
            ->expects(self::any())
            ->method('getType')
            ->will(self::returnValue(WriterInterface::TYPE_INI));

        $actualValue = $this->object->isStandardModeProperty($propertyName, $mockWriter);
        self::assertSame($isExtra, $actualValue);
    }

    /**
     * tests detecting a standard mode property
     */
    public function testIsStandardModePropertyWithWriter() : void
    {
        $mockWriter = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriter
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue(WriterInterface::TYPE_CSV));

        self::assertTrue($this->object->isStandardModeProperty('PropertyName', $mockWriter));
    }

    /**
     * Data Provider for the test testIsOutputProperty
     *
     * @return array<string|boolean>[]
     */
    public function outputPropertiesDataProvider()
    {
        return [
            ['Comment', true],
            ['Browser', true],
            ['Platform', true],
            ['Platform_Description', true],
            ['Device_Name', true],
            ['Device_Maker', true],
            ['RenderingEngine_Name', true],
            ['RenderingEngine_Description', true],
            ['Parent', true],
            ['Platform_Version', true],
            ['RenderingEngine_Version', true],
            ['Version', true],
            ['MajorVer', true],
            ['MinorVer', true],
            ['CssVersion', true],
            ['AolVersion', true],
            ['Alpha', true],
            ['Beta', true],
            ['Win16', true],
            ['Win32', true],
            ['Win64', true],
            ['Frames', true],
            ['IFrames', true],
            ['Tables', true],
            ['Cookies', true],
            ['BackgroundSounds', true],
            ['JavaScript', true],
            ['VBScript', true],
            ['JavaApplets', true],
            ['ActiveXControls', true],
            ['isMobileDevice', true],
            ['isSyndicationReader', true],
            ['isFake', true],
            ['isAnonymized', true],
            ['isModified', true],
            ['Crawler', true],
            ['lite', false],
            ['sortIndex', false],
            ['Parents', false],
            ['division', false],
            ['Browser_Type', true],
            ['Device_Type', true],
            ['Device_Pointing_Method', true],
            ['Browser_Maker', true],
            ['isTablet', true],
            ['PatternId', false],
        ];
    }

    /**
     * @dataProvider outputPropertiesDataProvider
     *
     * @param string $propertyName
     * @param bool   $isExtra
     */
    public function testIsOutputProperty(string $propertyName, bool $isExtra) : void
    {
        $mockWriterCsv = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterCsv
            ->expects(self::any())
            ->method('getType')
            ->will(self::returnValue(WriterInterface::TYPE_CSV));

        $actualValue = $this->object->isOutputProperty($propertyName, $mockWriterCsv);
        self::assertSame($isExtra, $actualValue);
    }

    /**
     * tests detecting a output property if a writer is given
     */
    public function testIsOutputPropertyWithCsvWriter() : void
    {
        $mockWriterCsv = $this->getMockBuilder(CsvWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterCsv
            ->expects(self::once())
            ->method('getType')
            ->will(self::returnValue(WriterInterface::TYPE_CSV));

        self::assertTrue($this->object->isOutputProperty('PropertyName', $mockWriterCsv));

        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(self::exactly(2))
            ->method('getType')
            ->will(self::returnValue(WriterInterface::TYPE_INI));

        self::assertTrue($this->object->isOutputProperty('PatternId', $mockWriterIni));
    }

    /**
     * tests detecting a output property if a writer is given
     */
    public function testIsOutputPropertyWithIniWriter() : void
    {
        $mockWriterIni = $this->getMockBuilder(IniWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $mockWriterIni
            ->expects(self::exactly(2))
            ->method('getType')
            ->will(self::returnValue(WriterInterface::TYPE_INI));

        self::assertTrue($this->object->isOutputProperty('PatternId', $mockWriterIni));
    }

    /**
     * Data Provider for the test testCheckValueInArray
     *
     * @return array<string|string>[]
     */
    public function checkValueInArrayProvider()
    {
        return [
            ['Browser_Type', 'Browser'],
            ['Device_Type', 'Tablet'],
            ['Device_Pointing_Method', 'touchscreen'],
            ['Browser_Bits', '32'],
            ['Platform_Bits', '64'],
        ];
    }

    /**
     * @dataProvider checkValueInArrayProvider
     *
     * @param string $propertyName
     * @param string $propertyValue
     */
    public function testCheckValueInArray(string $propertyName, string $propertyValue) : void
    {
        $actualValue = $this->object->checkValueInArray($propertyName, $propertyValue);
        self::assertSame($propertyValue, $actualValue);
    }

    public function testCheckValueInArrayExceptionUndfinedProperty() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property "abc" is not defined to be validated');

        $this->object->checkValueInArray('abc', 'bcd');
    }

    public function testCheckValueInArrayExceptionWrongValue() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid value given for Property "Browser_Type": given value "bcd", allowed: ["Useragent Anonymizer","Browser","Offline Browser","Multimedia Player","Library","Feed Reader","Email Client","Bot\/Crawler","Application","Tool","unknown"]');

        $this->object->checkValueInArray('Browser_Type', 'bcd');
    }

    /**
     * Data Provider for the test isDeprecatedProperty
     *
     * @return array<string|boolean>[]
     */
    public function deprecatedPropertiesDataProvider()
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
            ['Parent', false],
            ['Platform_Version', false],
            ['RenderingEngine_Version', false],
            ['Version', false],
            ['MajorVer', true],
            ['MinorVer', true],
            ['CssVersion', false],
            ['AolVersion', true],
            ['Alpha', false],
            ['Beta', false],
            ['Win16', true],
            ['Win32', true],
            ['Win64', true],
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
            ['isFake', false],
            ['isAnonymized', false],
            ['isModified', false],
            ['Crawler', true],
            ['lite', false],
            ['sortIndex', false],
            ['Parents', false],
            ['division', false],
            ['Browser_Type', false],
            ['Device_Type', false],
            ['Device_Pointing_Method', false],
            ['Browser_Maker', false],
            ['isTablet', true],
            ['PatternId', false],
        ];
    }

    /**
     * @dataProvider deprecatedPropertiesDataProvider
     *
     * @param string $propertyName
     * @param bool   $isDeprecated
     */
    public function testIsDeprecatedProperty(string $propertyName, bool $isDeprecated) : void
    {
        self::assertSame($isDeprecated, $this->object->isDeprecatedProperty($propertyName));
    }
}
