<?php

namespace Browscap\Helper;

use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\MemoryUsageProcessor;

/**
 * @package Browscap\Helper
 * @author Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class LoggerHelper
{
    /**
     * creates a \Monolo\Logger instance
     *
     * @param boolean $debug If true the debug logging mode will be enabled
     *
     * @return \Monolog\Logger
     */
    public function create($debug = false)
    {
        $logger = new Logger('browscap');

        if ($debug) {
            $stream = new StreamHandler('php://output', Logger::DEBUG);
            $stream->setFormatter(
                new LineFormatter('[%datetime%] %channel%.%level_name%: %message% %context% %extra%' . "\n")
            );

            $memoryProcessor = new MemoryUsageProcessor();
            $logger->pushProcessor($memoryProcessor);
        } else {
            $stream = new StreamHandler('php://output', Logger::INFO);
            $stream->setFormatter(new LineFormatter('%message% %extra%' . "\n"));

            /** @var callable $peakMemoryProcessor */
            $peakMemoryProcessor = new MemoryPeakUsageProcessor(true);
            $logger->pushProcessor($peakMemoryProcessor);
        }

        $logger->pushHandler($stream);
        $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::NOTICE));

        ErrorHandler::register($logger);

        return $logger;
    }
}
