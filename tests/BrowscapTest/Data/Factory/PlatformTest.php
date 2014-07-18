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
 * @package    Data
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Data\Factory;

use Browscap\Data\Factory\Platform;

/**
 * Class PlatformTest
 *
 * @category   BrowscapTest
 * @package    Data
 * @author     James Titcumb <james@asgrim.com>
 */
class PlatformTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Browscap\Data\Factory\Platform
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    public function setUp()
    {
        $this->object = new Platform();
    }

    public function testBuild()
    {
        $platformData = array('abc' => 'def');
        $json         = array();
        $platformName = 'Test';

        self::assertInstanceOf('\Browscap\Data\Platform', $this->object->build($platformData, $json, $platformName));
    }
}
