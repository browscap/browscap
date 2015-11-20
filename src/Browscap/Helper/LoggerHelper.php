<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   Browscap
 * @package    Helper
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Helper;

use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\MemoryUsageProcessor;

/**
 * Class LoggerHelper
 *
 * @category   Browscap
 * @package    Helper
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
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
                new LineFormatter('[%datetime%] %channel%.%level_name%: %message% %extra%' . "\n")
            );

            /** @var callable $memoryProcessor */
            $memoryProcessor = new MemoryUsageProcessor(true);
            $logger->pushProcessor($memoryProcessor);

            /** @var callable $peakMemoryProcessor */
            $peakMemoryProcessor = new MemoryPeakUsageProcessor(true);
            $logger->pushProcessor($peakMemoryProcessor);
        } else {
            $stream = new StreamHandler('php://output', Logger::WARNING);
            $stream->setFormatter(new LineFormatter('[%datetime%] %message% %extra%' . "\n"));

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
