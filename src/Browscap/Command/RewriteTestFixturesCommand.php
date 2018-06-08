<?php
declare(strict_types = 1);
namespace Browscap\Command;

use Browscap\Helper\LoggerHelper;
use Localheinz\Json\Normalizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class RewriteTestFixturesCommand extends Command
{
    protected function configure() : void
    {
        $this
            ->setName('rewrite-test-fixtures')
            ->setDescription('rewrites the test files in the fixtures folder');
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

        $resourcePath = __DIR__ . '/../../../tests/fixtures';

        $normalizer = new Normalizer\ChainNormalizer(
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
        $finder->in($resourcePath);

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
