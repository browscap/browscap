<?php

declare(strict_types=1);

namespace BrowscapTest\Command;

use Browscap\Command\CheckDuplicateTestsCommand;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\LogicException;

class CheckDuplicateTestsCommandTest extends TestCase
{
    /**
     * @throws ExpectationFailedException
     * @throws LogicException
     */
    public function testConstruct(): void
    {
        $object = new CheckDuplicateTestsCommand();

        self::assertSame('check-duplicate-tests', $object->getName());
        self::assertSame('checks the test cases for duplicates', $object->getDescription());
    }
}
