<?php

declare(strict_types=1);

namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\RewriteHelper;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class RewriteTest extends TestCase
{
    private RewriteHelper $object;

    /**
     * @throws void
     */
    protected function setUp(): void
    {
        $this->object = new RewriteHelper();
    }

    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testGetName(): void
    {
        static::assertSame('rewrite', $this->object->getName());
    }
}
