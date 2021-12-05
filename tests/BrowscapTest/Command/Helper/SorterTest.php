<?php

declare(strict_types=1);

namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\Sorter;
use Exception;
use PHPUnit\Framework\TestCase;

class SorterTest extends TestCase
{
    private Sorter $object;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->object = new Sorter();
    }

    public function testGetName(): void
    {
        static::assertSame('sorter', $this->object->getName());
    }

    public function testSort(): void
    {
        $data         = '{"b": "1","a": "2"}';
        $expectedData = '{"a":"2","b":"1"}';

        static::assertSame($expectedData, $this->object->sort($data));
    }
}
