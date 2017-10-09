<?php
declare(strict_types = 1);
namespace BrowscapTest\Helper;

use Browscap\Helper\LoggerHelper;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class LoggerHelperTest extends TestCase
{
    /**
     * tests creating a logger instance
     */
    public function testCreate() : void
    {
        $output = new NullOutput();
        $helper = new LoggerHelper();
        self::assertInstanceOf(Logger::class, $helper->create($output));
    }
}
