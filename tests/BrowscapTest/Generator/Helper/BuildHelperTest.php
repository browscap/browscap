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
 * @package    Generator
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Generator\Helper;

use Browscap\Generator\Helper\BuildHelper;
use Monolog\Logger;

/**
 * Class BuildGeneratorTest
 *
 * @category   BrowscapTest
 * @package    Generator
 * @author     James Titcumb <james@asgrim.com>
 */
class BuildHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $logger            = $this->getMock('\Monolog\Logger', array(), array(), '', false);
        $writerCollection  = $this->getMock(
            '\Browscap\Writer\WriterCollection\WriterCollection',
            array(),
            array(),
            '',
            false
        );
        $collectionCreator = $this->getMock('\Browscap\Helper\CollectionCreator', array(), array(), '', false);

        BuildHelper::run('test', '.', $logger, $writerCollection, $collectionCreator);
    }
}
