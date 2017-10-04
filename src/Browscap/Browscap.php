<?php
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
