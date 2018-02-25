<?php
declare(strict_types = 1);
namespace Browscap\Command;

use Browscap\Helper\LoggerHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Localheinz\Json\Normalizer;
use Symfony\Component\Finder\Finder;

class RewriteCoreDivisionsCommand extends Command
{
    /**
     * @var string
     */
    private const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    protected function configure() : void
    {
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('rewrite-core-divisions')
            ->setDescription('rewrites the resource files for the core divisions')
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

        $browserResourcePath = $input->getOption('resources') . '/core';

        $logger->info('Resource folder: ' . $input->getOption('resources'));

        $schema = 'file://' . realpath(__DIR__ . '/../../../schema/core-divisions.json');

        $normalizer = new Normalizer\ChainNormalizer(
            new Normalizer\SchemaNormalizer($schema),
            new Normalizer\JsonEncodeNormalizer(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            new Normalizer\IndentNormalizer('  '),
            new Normalizer\FinalNewLineNormalizer()
        );

        $finder = new Finder();
        $finder->files();
        $finder->name('*.json');
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);
        $finder->sortByName();
        $finder->ignoreUnreadableDirs();
        $finder->in($browserResourcePath);

        foreach ($finder as $file) {
            $logger->info('read source file ' . $file->getPathname());

            $json = file_get_contents($file->getPathname());

            try {
                $normalized = $normalizer->normalize($json);
            } catch (\Throwable $e) {
                $logger->critical(new \Exception(sprintf('file "%s" is not valid', $file->getPathname()), 0, $e));
                continue;
            }

            file_put_contents($file->getPathname(), $normalized);
        }

        $output->writeln('Done');

        return 0;
    }
}
