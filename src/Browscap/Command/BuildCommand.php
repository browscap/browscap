<?php
declare(strict_types = 1);
namespace Browscap\Command;

use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\LoggerHelper;
use Browscap\Writer\Factory\FullCollectionFactory;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    /**
     * @var string
     */
    private const DEFAULT_BUILD_FOLDER = '/../../../build';

    /**
     * @var string
     */
    private const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    /** @var string */
    private const DEFAULT_GENERATION_DATE = 'now';

    protected function configure() : void
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     * @throws \Assert\AssertionFailedException
     *
     * @return int|null null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $loggerHelper = new LoggerHelper();
        $logger       = $loggerHelper->create($output);

        /** @var string $version */
        $version = $input->getArgument('version');

        /** @var string $rawGenerationDate */
        $rawGenerationDate = $input->getOption('generation-date');

        $generationDate = new DateTimeImmutable($rawGenerationDate, new DateTimeZone('UTC'));

        $logger->info(sprintf('Build started (%s, generated %s).', $version, $generationDate->format(DATE_ATOM)));

        /** @var string $buildFolder */
        $buildFolder = $input->getOption('output');

        $writerCollectionFactory = new FullCollectionFactory();
        $writerCollection        = $writerCollectionFactory->createCollection($logger, $buildFolder);
        $dataCollectionFactory   = new DataCollectionFactory($logger);

        /** @var string $resources */
        $resources = $input->getOption('resources');

        $buildGenerator = new BuildGenerator(
            $resources,
            $buildFolder,
            $logger,
            $writerCollection,
            $dataCollectionFactory
        );

        if (false !== $input->getOption('coverage')) {
            $buildGenerator->setCollectPatternIds(true);
        }

        $createZip = true;

        if (false !== $input->getOption('no-zip')) {
            $createZip = false;
        }

        $buildGenerator->run($version, $generationDate, $createZip);

        $logger->info('Build done.');

        return 0;
    }
}
