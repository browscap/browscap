<?php

declare(strict_types=1);

namespace BrowscapTest\Helper;

use Browscap\Helper\LoggerHelper;
use Monolog\Logger;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Symfony\Component\Console\Output\NullOutput;

class LoggerHelperTest extends TestCase
{
    /**
     * tests creating a logger instance
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testCreate(): void
    {
        $output = new NullOutput();
        $helper = new LoggerHelper();
        static::assertInstanceOf(Logger::class, $helper->create($output));
    }
}
