<?php

declare(strict_types=1);

namespace Browscap\Helper;

use InvalidArgumentException;
use Monolog\ErrorHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class LoggerHelper
{
    /**
     * creates an instance of a PSR-3 Logger
     *
     * @throws InvalidArgumentException
     */
    public function create(OutputInterface $output): LoggerInterface
    {
        $logger        = new Logger('browscap');
        $consoleLogger = new ConsoleLogger($output);
        $psrHandler    = new PsrHandler($consoleLogger);

        $logger->pushHandler($psrHandler);
        $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, LogLevel::NOTICE));

        ErrorHandler::register($logger);

        return $logger;
    }
}
