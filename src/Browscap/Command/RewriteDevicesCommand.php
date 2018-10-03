<?php
declare(strict_types = 1);
namespace Browscap\Command;

use Browscap\Helper\LoggerHelper;
use Localheinz\Json\Normalizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class RewriteDevicesCommand extends Command
{
    /**
     * @var string
     */
    private const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    protected function configure() : void
    {
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('rewrite-devices')
            ->setDescription('rewrites the resource files for the devices')
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $loggerHelper = new LoggerHelper();
        $logger       = $loggerHelper->create($output);

        /** @var string $resources */
        $resources = $input->getOption('resources');

        $deviceResourcePath = $resources . '/devices';

        $logger->info('Resource folder: ' . $resources);

        $schema = 'file://' . realpath(__DIR__ . '/../../../schema/devices.json');

        /** @var \Browscap\Command\Helper\Rewrite $rewriteHelper */
        $rewriteHelper = $this->getHelper('rewrite');

        $rewriteHelper->rewrite($logger, $deviceResourcePath, $schema, true);

        $output->writeln('Done');

        return 0;
    }
}
