<?php
declare(strict_types = 1);
namespace Browscap\Helper;

use Monolog\ErrorHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LoggerHelper
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class LoggerHelper
{
    /**
     * creates a \Monolo\Logger instance
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function create(OutputInterface $output) : LoggerInterface
    {
        $logger        = new Logger('browscap');
        $consoleLogger = new ConsoleLogger($output);
        $psrHandler    = new PsrHandler($consoleLogger);

        $logger->pushHandler($psrHandler);
        $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::NOTICE));

        ErrorHandler::register($logger);

        return $logger;
    }
}
