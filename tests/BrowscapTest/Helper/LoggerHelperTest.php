<?php

namespace BrowscapTest\Helper;

use Browscap\Helper\LoggerHelper;
class LoggerHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $helper = new LoggerHelper();
        self::assertInstanceOf('\\Monolog\\Logger', $helper->create());
    }
}
