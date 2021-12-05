<?php

declare(strict_types=1);

namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\RewriteHelper;
use Exception;
use PHPUnit\Framework\TestCase;

class RewriteTest extends TestCase
{
    private RewriteHelper $object;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->object = new RewriteHelper();
    }

    public function testGetName(): void
    {
        static::assertSame('rewrite', $this->object->getName());
    }
}
