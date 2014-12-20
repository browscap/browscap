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
 * @package    Filter
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Filter;

use Browscap\Filter\LiteFilter;

/**
 * Class LiteFilterTest
 *
 * @category   BrowscapTest
 * @package    Filter
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class LiteFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Browscap\Filter\LiteFilter
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    public function setUp()
    {
        $this->object = new LiteFilter();
    }

    public function testGetType()
    {
        self::assertSame('LITE', $this->object->getType());
    }

    public function testIsOutput()
    {
        $mockDivision = $this->getMock('\Browscap\Data\Division', array('isLite'), array(), '', false);
        $mockDivision
            ->expects(self::once())
            ->method('isLite')
            ->will(self::returnValue(false))
        ;

        self::assertFalse($this->object->isOutput($mockDivision));
    }

    /**
     * Data Provider for the test testIsOutputProperty
     *
     * @return array
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
     * @dataProvider outputPropertiesDataProvider
     */
    public function testIsOutputProperty($propertyName, $isExtra)
    {
        $actualValue = $this->object->isOutputProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }
}
