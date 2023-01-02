<?php

declare(strict_types=1);

namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\ValidateHelper;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class ValidateTest extends TestCase
{
    private ValidateHelper $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new ValidateHelper();
    }

    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testGetName(): void
    {
        static::assertSame('validate', $this->object->getName());
    }
}
