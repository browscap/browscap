<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowscapTest\Helper;

use Browscap\Helper\LoggerHelper;
use Monolog\Logger;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Class LoggerHelperTest
 *
 * @category   BrowscapTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class LoggerHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests creating a logger instance
     *
     * @group helper
     * @group sourcetest
     */
    public function testCreate() : void
    {
        $output = new NullOutput();
        $helper = new LoggerHelper();
        self::assertInstanceOf(Logger::class, $helper->create($output));
    }
}
