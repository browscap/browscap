<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   BrowscapTest
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Filter;

use Browscap\Filter\FullFilter;

/**
 * Class FullFilterTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class FullFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Filter\FullFilter
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new FullFilter();
    }

    /**
     * tests getter for the filter type
     *
     * @group filter
     * @group sourcetest
     */
    public function testGetType()
    {
        self::assertSame('FULL', $this->object->getType());
    }

    /**
     * tests detecting if a divion should be in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutput()
    {
        $division = $this->createMock(\Browscap\Data\Division::class);

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
            ['Crawler', true],
            ['lite', false],
            ['sortIndex', false],
            ['Parents', false],
            ['division', false],
            ['Browser_Type', true],
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
     */
    public function testIsOutputProperty($propertyName, $isExtra)
    {
        $actualValue = $this->object->isOutputProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }

    /**
     * tests if a section is always in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutputSectionAlways()
    {
        $this->assertTrue($this->object->isOutputSection([]));
        $this->assertTrue($this->object->isOutputSection(['lite' => false]));
        $this->assertTrue($this->object->isOutputSection(['lite' => true]));
    }
}
