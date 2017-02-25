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

namespace BrowscapTest\Helper;

use Browscap\Helper\LoggerHelper;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Class LoggerHelperTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class LoggerHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests creating a logger instance
     *
     * @group helper
     * @group sourcetest
     */
    public function testCreate()
    {
        $output = new NullOutput();
        $helper = new LoggerHelper();
        self::assertInstanceOf('\Monolog\Logger', $helper->create($output));
    }
}
