<?php

declare(strict_types=1);

namespace BrowscapTest\Command;

use Browscap\Command\RewriteDivisionsCommand;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\LogicException;

class RewriteDivisionsCommandTest extends TestCase
{
    /**
     * @throws ExpectationFailedException
     * @throws LogicException
     */
    public function testConstruct(): void
    {
        $object = new RewriteDivisionsCommand();

        self::assertSame('rewrite-divisions', $object->getName());
        self::assertSame('rewrites the resource files for the divisions', $object->getDescription());
        self::assertTrue($object->getDefinition()->hasOption('resources'));
    }
}
