<?php

declare(strict_types=1);

namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\ValidateHelper;
use Exception;
use PHPUnit\Framework\TestCase;

class ValidateTest extends TestCase
{
    /** @var ValidateHelper */
    private $object;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->object = new ValidateHelper();
    }

    public function testGetName(): void
    {
        static::assertSame('validate', $this->object->getName());
    }
}
