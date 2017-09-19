<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace Browscap;

use Symfony\Component\Console\Application;

/**
 * Class Browscap
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class Browscap extends Application
{
    public function __construct()
    {
        parent::__construct('Browser Capabilities Project', 'dev-master');

        $this->add(new Command\BuildCommand());
    }
}
