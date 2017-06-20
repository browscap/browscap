<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Browscap\Writer;

use Browscap\Data\Expander;

/**
 * Interface WriterInterface
 *
 * @category   Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
interface WriterNeedsExpanderInterface
{
    /**
     * @param Expander $expander
     *
     * @return \Browscap\Writer\WriterInterface
     */
    public function setExpander(Expander $expander);
}
