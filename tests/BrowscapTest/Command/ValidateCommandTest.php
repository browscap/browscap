<?php

declare(strict_types=1);

namespace BrowscapTest\Command;

use Browscap\Command\ValidateCommand;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;

class ValidateCommandTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws LogicException
     */
    public function testConstruct(): void
    {
        $object = new ValidateCommand();

        self::assertSame('validate', $object->getName());
        self::assertSame('Meta-Command to validate the resource and test files', $object->getDescription());
        self::assertTrue($object->getDefinition()->hasOption('resources'));
    }
}
