<?php

declare(strict_types=1);

namespace Browscap\Command\Helper;

use InvalidArgumentException;
use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class LoggerHelper extends Helper
{
    /** @throws void */
    public function getName(): string
    {
        return 'logger';
    }

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

        try {
            $psrHandler->setFormatter(new LineFormatter('%message%'));
        } catch (RuntimeException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        $logger->pushProcessor(new PsrLogMessageProcessor());
        $logger->pushHandler($psrHandler);
        $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, LogLevel::NOTICE));

        ErrorHandler::register($logger);

        return $logger;
    }
}
