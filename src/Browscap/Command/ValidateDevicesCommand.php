<?php

declare(strict_types=1);

namespace Browscap\Command;

use Browscap\Command\Helper\ValidateHelper;
use Browscap\Helper\LoggerHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function is_string;
use function realpath;

class ValidateDevicesCommand extends Command
{
    private const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    /** @throws InvalidArgumentException */
    protected function configure(): void
    {
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('validate-devices')
            ->setDescription('validates the resource files for the devices')
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
        $output->write('validate device files ');

        $loggerHelper = new LoggerHelper();
        $logger       = $loggerHelper->create($output);

        $resources = $input->getOption('resources');
        assert(is_string($resources));

        $devicesResourcePath = $resources . '/devices';

        $logger->info('Resource folder: ' . $resources);

        $schema = 'file://' . realpath(__DIR__ . '/../../../schema/devices.json');

        $validateHelper = $this->getHelper('validate');
        assert($validateHelper instanceof ValidateHelper);

        $failed = $validateHelper->validate($logger, $devicesResourcePath, $schema);

        if ($failed) {
            $output->writeln('<fg=red>invalid</>');
        } else {
            $output->writeln('<fg=green>valid</>');
        }

        return (int) $failed;
    }
}
