<?php

declare(strict_types=1);

namespace Browscap\Command;

use Browscap\Command\Helper\RewriteHelper;
use Browscap\Helper\LoggerHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function is_string;
use function realpath;

class RewriteCoreDivisionsCommand extends Command
{
    private const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    protected function configure(): void
    {
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('rewrite-core-divisions')
            ->setDescription('rewrites the resource files for the core divisions')
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder);
    }

    /**
     * @return int|null null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $loggerHelper = new LoggerHelper();
        $logger       = $loggerHelper->create($output);

        $resources = $input->getOption('resources');
        assert(is_string($resources));

        $coreResourcePath = $resources . '/core';

        $logger->info('Resource folder: ' . $resources);

        $schema = 'file://' . realpath(__DIR__ . '/../../../schema/core-divisions.json');

        $rewriteHelper = $this->getHelper('rewrite');
        assert($rewriteHelper instanceof RewriteHelper);

        $rewriteHelper->rewrite($logger, $coreResourcePath, $schema, false);

        $output->writeln('Done');

        return 0;
    }
}
