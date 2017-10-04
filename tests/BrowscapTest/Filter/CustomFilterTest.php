<?php
declare(strict_types = 1);
namespace BrowscapTest\Filter;

use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\CustomFilter;
use Browscap\Filter\FilterInterface;

/**
 * Class CustomFilterTestTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class CustomFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Filter\CustomFilter
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

        $this->object = new CustomFilter($propertyHolder, ['Parent']);
    }

    /**
     * tests getter for the filter type
     *
     * @group filter
     * @group sourcetest
     */
    public function testGetType() : void
    {
        self::assertSame(FilterInterface::TYPE_CUSTOM, $this->object->getType());
    }

    /**
     * tests detecting if a divion should be in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutput() : void
    {
        $division = $this->createMock(Division::class);

        self::assertTrue($this->object->isOutput($division));
    }

    /**
     * Data Provider for the test testIsOutputProperty
     *
     * @return array<string|boolean>[]
     */
    public function outputPropertiesDataProvider()
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
        self::assertSame($isExtra, $this->object->isOutputProperty($propertyName));
    }

    /**
     * @dataProvider outputPropertiesDataProvider
     *
     * @group filter
     * @group sourcetest
     *
     * @param mixed $propertyName
     */
    public function testIsOutputPropertyModified($propertyName) : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(self::any())
            ->method('isOutputProperty')
            ->will(self::returnValue(false));

        $object = new CustomFilter($propertyHolder, ['Parent']);
        self::assertFalse($object->isOutputProperty($propertyName));
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

        $object = new CustomFilter($propertyHolder, ['Parent']);
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
        $this->assertTrue($this->object->isOutputSection(['lite' => false]));
        $this->assertTrue($this->object->isOutputSection(['lite' => true]));
    }
}
