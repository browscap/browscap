<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Browscap\Parser;

use RuntimeException;

interface ParserInterface
{
    /**
     * @return array<array<array<string>|string>>
     *
     * @throws RuntimeException
     */
    public function parse(): array;

    /**
     * @return array<array<string>>
     *
     * @throws void
     */
    public function getParsed(): array;

    /** @throws void */
    public function getFilename(): string;
}
