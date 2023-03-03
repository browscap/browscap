<?php

declare(strict_types=1);

namespace BrowscapTest\Command;

use Browscap\Command\ValidateDivisionsCommand;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;

class ValidateDivisionsCommandTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws LogicException
     */
    public function testConstruct(): void
    {
        $object = new ValidateDivisionsCommand();

        self::assertSame('validate-divisions', $object->getName());
        self::assertSame('validates the resource files for the core-divisions', $object->getDescription());
        self::assertTrue($object->getDefinition()->hasOption('resources'));
    }
}
