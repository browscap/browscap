<?php

namespace Browscap\Helper;

use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @package Browscap\Helper
 * @author Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class LoggerHelper
{
    /**
     * creates a \Monolo\Logger instance
     *
     * @return \Monolog\Logger
     */
    public function create()
    {
        $stream = new StreamHandler('php://output', Logger::INFO);
        $stream->setFormatter(new LineFormatter('%message%' . "\n"));

        $logger = new Logger('browscap');
        $logger->pushHandler($stream);
        $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::NOTICE));

        ErrorHandler::register($logger);

        return $logger;
    }
}
