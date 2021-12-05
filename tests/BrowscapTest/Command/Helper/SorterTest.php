<?php

declare(strict_types=1);

namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\Sorter;
use JsonException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class SorterTest extends TestCase
{
    private Sorter $object;

    /**
     * @throws void
     */
    protected function setUp(): void
    {
        $this->object = new Sorter();
    }

    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testGetName(): void
    {
        static::assertSame('sorter', $this->object->getName());
    }

    /**
     * @throws JsonException
     */
    public function testSort(): void
    {
        $data         = '{"b": "1","a": "2"}';
        $expectedData = '{"a":"2","b":"1"}';

        static::assertSame($expectedData, $this->object->sort($data));
    }
}
