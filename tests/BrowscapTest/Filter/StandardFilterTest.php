<?php
declare(strict_types = 1);
namespace BrowscapTest\Filter;

use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\FilterInterface;
use Browscap\Filter\StandardFilter;

/**
 * Class StandardFilterTestTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class StandardFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Filter\StandardFilter
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
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

        $this->object = new StandardFilter($propertyHolder);
    }

    /**
     * tests getter for the filter type
     *
     * @group filter
     * @group sourcetest
     */
    public function testGetType() : void
    {
        self::assertSame(FilterInterface::TYPE_STANDARD, $this->object->getType());
    }

    /**
     * tests detecting if a divion should be in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutputTrue() : void
    {
        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['isStandard'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('isStandard')
            ->will(self::returnValue(true));

        self::assertTrue($this->object->isOutput($division));
    }

    /**
     * tests detecting if a divion should be in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutputFalse() : void
    {
        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['isStandard'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('isStandard')
            ->will(self::returnValue(false));

        self::assertFalse($this->object->isOutput($division));
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
            ['Crawler', true],
            ['lite', false],
            ['sortIndex', false],
            ['Parents', false],
            ['division', false],
            ['Browser_Type', false],
            ['Device_Type', true],
            ['Device_Pointing_Method', true],
            ['isTablet', true],
            ['Browser_Maker', true],
        ];
    }

    /**
     * @dataProvider outputPropertiesDataProvider
     *
     * @group filter
     * @group sourcetest
     *
     * @param string $propertyName
     * @param bool   $isExtra
     */
    public function testIsOutputProperty(string $propertyName, bool $isExtra) : void
    {
        $actualValue = $this->object->isOutputProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }

    /**
     * @dataProvider outputPropertiesDataProvider
     *
     * @group filter
     * @group sourcetest
     *
     * @param string $propertyName
     */
    public function testIsOutputPropertyWithPropertyHolder(string $propertyName) : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(self::once())
            ->method('isOutputProperty')
            ->will(self::returnValue(false));

        $object = new StandardFilter($propertyHolder);
        self::assertFalse($object->isOutputProperty($propertyName));
    }

    /**
     * tests if a section is always in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutputSectionAlways() : void
    {
        $this->assertTrue($this->object->isOutputSection([]));
        $this->assertFalse($this->object->isOutputSection(['standard' => false]));
        $this->assertTrue($this->object->isOutputSection(['standard' => true]));
    }
}
