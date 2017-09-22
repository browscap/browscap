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
namespace Browscap\Writer;

use Browscap\Data\Expander;

/**
 * Interface WriterInterface
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
interface WriterNeedsExpanderInterface
{
    /**
     * @param Expander $expander
     *
     * @return void
     */
    public function setExpander(Expander $expander) : void;
}
