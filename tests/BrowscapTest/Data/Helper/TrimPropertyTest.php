<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Helper;

use Browscap\Data\Helper\TrimProperty;
use PHPUnit\Framework\TestCase;

class TrimPropertyTest extends TestCase
{
    /**
     * @var TrimProperty
     */
    private $object;

    public function setUp() : void
    {
        $this->object = new TrimProperty();
    }

    public function testTrue() : void
    {
        self::assertTrue($this->object->trimProperty('true'));
    }

    public function testFalse() : void
    {
        self::assertFalse($this->object->trimProperty('false'));
    }

    public function testDefault() : void
    {
        self::assertSame('abc', $this->object->trimProperty('  abc '));
    }
}
