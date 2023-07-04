<?php

declare(strict_types=1);

namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\LoggerHelper;
use InvalidArgumentException;
use Monolog\Logger;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class LoggerHelperTest extends TestCase
{
    private LoggerHelper $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new LoggerHelper();
    }

    /** @throws ExpectationFailedException */
    public function testGetName(): void
    {
        static::assertSame('logger', $this->object->getName());
    }

    /**
     * tests creating a logger instance
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testCreate(): void
    {
        $output = new NullOutput();

        $logger = $this->object->create($output);

        static::assertInstanceOf(Logger::class, $logger);
        self::assertCount(2, $logger->getHandlers());
        self::assertCount(1, $logger->getProcessors());
    }
}
