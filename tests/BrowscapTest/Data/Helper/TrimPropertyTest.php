<?php

declare(strict_types=1);

namespace BrowscapTest\Data\Helper;

use Browscap\Data\Helper\TrimProperty;
use PHPUnit\Framework\TestCase;

class TrimPropertyTest extends TestCase
{
    /** @var TrimProperty */
    private $object;

    protected function setUp(): void
    {
        $this->object = new TrimProperty();
    }

    public function testTrue(): void
    {
        static::assertTrue($this->object->trim('true'));
    }

    public function testFalse(): void
    {
        static::assertFalse($this->object->trim('false'));
    }

    public function testDefault(): void
    {
        static::assertSame('abc', $this->object->trim('  abc '));
    }
}
