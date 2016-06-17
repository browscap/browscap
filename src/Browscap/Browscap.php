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

namespace Browscap;

use Symfony\Component\Console\Application;

/**
 * Class Browscap
 *
 * @category   Browscap
 * @author     James Titcumb <james@asgrim.com>
 */
class Browscap extends Application
{
    public function __construct()
    {
        parent::__construct('Browser Capabilities Project', 'dev-master');

        $commands = [
            new Command\BuildCommand(),
            new Command\DiffCommand(),
            new Command\GrepCommand(),
        ];

        foreach ($commands as $command) {
            $this->add($command);
        }
    }
}
