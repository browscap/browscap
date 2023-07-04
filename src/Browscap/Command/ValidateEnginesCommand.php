<?php

declare(strict_types=1);

namespace Browscap\Command;

use Browscap\Command\Helper\LoggerHelper;
use Browscap\Command\Helper\ValidateHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function is_string;
use function realpath;

class ValidateEnginesCommand extends Command
{
    private const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    /** @throws InvalidArgumentException */
    protected function configure(): void
    {
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('validate-engines')
            ->setDescription('validates the resource files for the engines')
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder);
    }

    /**
     * @return int 0 if everything went fine, or an error code
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write('validate engine files ');

        $loggerHelper = $this->getHelper('logger');
        assert($loggerHelper instanceof LoggerHelper);
        $logger = $loggerHelper->create($output);

        $resources = $input->getOption('resources');
        assert(is_string($resources));

        $enginesResourcePath = $resources . '/engines';

        $logger->info('Resource folder: ' . $resources);

        $schema = 'file://' . realpath(__DIR__ . '/../../../schema/engines.json');

        $validateHelper = $this->getHelper('validate');
        assert($validateHelper instanceof ValidateHelper);

        $failed = $validateHelper->validate($logger, $enginesResourcePath, $schema);

        if ($failed) {
            $output->writeln('<fg=red>invalid</>');

            return Command::FAILURE;
        }

        $output->writeln('<fg=green>valid</>');

        return Command::SUCCESS;
    }
}
