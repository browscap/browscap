<?php

declare(strict_types=1);

namespace BrowscapTest\Command;

use AssertionError;
use Browscap\Command\Helper\LoggerHelper;
use Browscap\Command\Helper\RewriteHelper;
use Browscap\Command\Helper\ValidateHelper;
use Browscap\Command\ValidateEnginesCommand;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function realpath;
use function sprintf;

class ValidateEnginesCommandTest extends TestCase
{
    /**
     * @throws ExpectationFailedException
     * @throws LogicException
     */
    public function testConstruct(): void
    {
        $object = new ValidateEnginesCommand();

        self::assertSame('validate-engines', $object->getName());
        self::assertSame('validates the resource files for the engines', $object->getDescription());
        self::assertTrue($object->getDefinition()->hasOption('resources'));
    }

    /**
     * @throws ExpectationFailedException
     * @throws ReflectionException
     * @throws LogicException
     */
    public function testExecuteWithWongLoggerHelper(): void
    {
        $input = $this->getMockBuilder(InputInterface::class)
            ->getMock();
        $input->expects(self::never())
            ->method('getOption');

        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();
        $output->expects(self::never())
            ->method('writeln');

        $validateHelper = $this->createMock(ValidateHelper::class);

        $helperSet = $this->getMockBuilder(HelperSet::class)
            ->getMock();
        $helperSet->expects(self::once())
            ->method('get')
            ->with('logger')
            ->willReturn($validateHelper);

        $object = new ValidateEnginesCommand();
        $object->setHelperSet($helperSet);

        $function = new ReflectionMethod($object, 'execute');

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert($loggerHelper instanceof LoggerHelper)');

        $function->invoke($object, $input, $output);
    }

    /**
     * @throws ExpectationFailedException
     * @throws ReflectionException
     * @throws LogicException
     */
    public function testExecuteWithoutResourcesOption(): void
    {
        $input = $this->getMockBuilder(InputInterface::class)
            ->getMock();
        $input->expects(self::once())
            ->method('getOption')
            ->with('resources')
            ->willReturn(null);

        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();
        $output->expects(self::never())
            ->method('writeln');

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');
        $logger->expects(self::never())
            ->method('log');

        $loggerHelper = $this->getMockBuilder(LoggerHelper::class)
            ->getMock();
        $loggerHelper->expects(self::once())
            ->method('create')
            ->with($output)
            ->willReturn($logger);

        $helperSet = $this->getMockBuilder(HelperSet::class)
            ->getMock();
        $helperSet->expects(self::once())
            ->method('get')
            ->with('logger')
            ->willReturn($loggerHelper);

        $object = new ValidateEnginesCommand();
        $object->setHelperSet($helperSet);

        $function = new ReflectionMethod($object, 'execute');

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert(is_string($resources))');

        $function->invoke($object, $input, $output);
    }

    /**
     * @throws ExpectationFailedException
     * @throws ReflectionException
     * @throws LogicException
     */
    public function testExecuteWithWongRewriteHelper(): void
    {
        $resources = 'test-resources';

        $input = $this->getMockBuilder(InputInterface::class)
            ->getMock();
        $input->expects(self::once())
            ->method('getOption')
            ->with('resources')
            ->willReturn($resources);

        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();
        $output->expects(self::never())
            ->method('writeln');

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::once())
            ->method('info')
            ->with(sprintf('Resource folder: %s', $resources));
        $logger->expects(self::never())
            ->method('debug');
        $logger->expects(self::never())
            ->method('log');

        $rewriteHelper = $this->createMock(RewriteHelper::class);

        $loggerHelper = $this->getMockBuilder(LoggerHelper::class)
            ->getMock();
        $loggerHelper->expects(self::once())
            ->method('create')
            ->with($output)
            ->willReturn($logger);

        $helperSet = $this->getMockBuilder(HelperSet::class)
            ->getMock();
        $helperSet->expects(self::exactly(2))
            ->method('get')
            ->willReturnMap(
                [
                    ['logger', $loggerHelper],
                    ['validate', $rewriteHelper],
                ],
            );

        $object = new ValidateEnginesCommand();
        $object->setHelperSet($helperSet);

        $function = new ReflectionMethod($object, 'execute');

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert($validateHelper instanceof ValidateHelper)');

        $function->invoke($object, $input, $output);
    }

    /**
     * @throws ExpectationFailedException
     * @throws ReflectionException
     * @throws LogicException
     */
    public function testExecuteValidationFailed(): void
    {
        $resources = 'test-resources';
        $schema    = 'file://' . realpath(__DIR__ . '/../../../schema/engines.json');

        $input = $this->getMockBuilder(InputInterface::class)
            ->getMock();
        $input->expects(self::once())
            ->method('getOption')
            ->with('resources')
            ->willReturn($resources);

        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();
        $output->expects(self::once())
            ->method('writeln')
            ->with('<fg=red>invalid</>', 0);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::once())
            ->method('info')
            ->with(sprintf('Resource folder: %s', $resources));
        $logger->expects(self::never())
            ->method('debug');
        $logger->expects(self::never())
            ->method('log');

        $loggerHelper = $this->getMockBuilder(LoggerHelper::class)
            ->getMock();
        $loggerHelper->expects(self::once())
            ->method('create')
            ->with($output)
            ->willReturn($logger);

        $validateHelper = $this->getMockBuilder(ValidateHelper::class)
            ->getMock();
        $validateHelper->expects(self::once())
            ->method('validate')
            ->with($logger, $resources . '/engines', $schema)
            ->willReturn(true);

        $helperSet = $this->getMockBuilder(HelperSet::class)
            ->getMock();
        $helperSet->expects(self::exactly(2))
            ->method('get')
            ->willReturnMap(
                [
                    ['logger', $loggerHelper],
                    ['validate', $validateHelper],
                ],
            );

        $object = new ValidateEnginesCommand();
        $object->setHelperSet($helperSet);

        $function = new ReflectionMethod($object, 'execute');

        self::assertSame(Command::FAILURE, $function->invoke($object, $input, $output));
    }

    /**
     * @throws ExpectationFailedException
     * @throws ReflectionException
     * @throws LogicException
     */
    public function testExecuteValidationSuccess(): void
    {
        $resources = 'test-resources';
        $schema    = 'file://' . realpath(__DIR__ . '/../../../schema/engines.json');

        $input = $this->getMockBuilder(InputInterface::class)
            ->getMock();
        $input->expects(self::once())
            ->method('getOption')
            ->with('resources')
            ->willReturn($resources);

        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();
        $output->expects(self::once())
            ->method('writeln')
            ->with('<fg=green>valid</>', 0);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::once())
            ->method('info')
            ->with(sprintf('Resource folder: %s', $resources));
        $logger->expects(self::never())
            ->method('debug');
        $logger->expects(self::never())
            ->method('log');

        $loggerHelper = $this->getMockBuilder(LoggerHelper::class)
            ->getMock();
        $loggerHelper->expects(self::once())
            ->method('create')
            ->with($output)
            ->willReturn($logger);

        $validateHelper = $this->getMockBuilder(ValidateHelper::class)
            ->getMock();
        $validateHelper->expects(self::once())
            ->method('validate')
            ->with($logger, $resources . '/engines', $schema)
            ->willReturn(false);

        $helperSet = $this->getMockBuilder(HelperSet::class)
            ->getMock();
        $helperSet->expects(self::exactly(2))
            ->method('get')
            ->willReturnMap(
                [
                    ['logger', $loggerHelper],
                    ['validate', $validateHelper],
                ],
            );

        $object = new ValidateEnginesCommand();
        $object->setHelperSet($helperSet);

        $function = new ReflectionMethod($object, 'execute');

        self::assertSame(Command::SUCCESS, $function->invoke($object, $input, $output));
    }
}
