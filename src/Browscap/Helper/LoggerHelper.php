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
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Helper;

use Monolog\ErrorHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LoggerHelper
 *
 * @category   Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class LoggerHelper
{
    /**
     * creates a \Monolo\Logger instance
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Monolog\Logger
     */
    public function create(OutputInterface $output)
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
