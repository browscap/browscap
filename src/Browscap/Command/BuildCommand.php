<?php

declare(strict_types=1);

namespace Browscap\Command;

use Assert\AssertionFailedException;
use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\LoggerHelper;
use Browscap\Writer\Factory\FullCollectionFactory;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function is_string;
use function sprintf;

use const DATE_ATOM;

class BuildCommand extends Command
{
    private const DEFAULT_BUILD_FOLDER = '/../../../build';

    private const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    private const DEFAULT_GENERATION_DATE = 'now';

    /** @throws InvalidArgumentException */
    protected function configure(): void
    {
        $defaultBuildFolder    = __DIR__ . self::DEFAULT_BUILD_FOLDER;
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('build')
            ->setDescription('Parses the JSON source files and builds the INI files')
            ->addArgument('version', InputArgument::REQUIRED, 'Version number to apply')
            ->addOption('generation-date', null, InputOption::VALUE_OPTIONAL, 'Override the generation date (defaults to "now")', self::DEFAULT_GENERATION_DATE)
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Where to output the build files to', $defaultBuildFolder)
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder)
            ->addOption('coverage', null, InputOption::VALUE_NONE, 'Collect and build with pattern ids useful for coverage')
            ->addOption('no-zip', null, InputOption::VALUE_NONE, 'Skip creating the zipped collection');
    }

    /**
     * @return int null or 0 if everything went fine, or an error code
     *
     * @throws Exception
     * @throws AssertionFailedException
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $loggerHelper = new LoggerHelper();
        $logger       = $loggerHelper->create($output);

        $version = $input->getArgument('version');
        assert(is_string($version));

        $rawGenerationDate = $input->getOption('generation-date');
        assert(is_string($rawGenerationDate));

        $generationDate = new DateTimeImmutable($rawGenerationDate, new DateTimeZone('UTC'));

        $logger->info(sprintf('Build started (%s, generated %s).', $version, $generationDate->format(DATE_ATOM)));

        $buildFolder = $input->getOption('output');
        assert(is_string($buildFolder));

        $writerCollectionFactory = new FullCollectionFactory();
        $writerCollection        = $writerCollectionFactory->createCollection($logger, $buildFolder);
        $dataCollectionFactory   = new DataCollectionFactory($logger);

        $resources = $input->getOption('resources');
        assert(is_string($resources));

        $buildGenerator = new BuildGenerator(
            $resources,
            $buildFolder,
            $logger,
            $writerCollection,
            $dataCollectionFactory,
        );

        if ($input->getOption('coverage') !== false) {
            $buildGenerator->setCollectPatternIds(true);
        }

        $createZip = true;

        if ($input->getOption('no-zip') !== false) {
            $createZip = false;
        }

        $buildGenerator->run($version, $generationDate, $createZip);

        $logger->info('Build done.');

        return self::SUCCESS;
    }
}
