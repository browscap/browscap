<?php

declare(strict_types=1);

namespace Browscap\Command;

use Browscap\Command\Helper\LoggerHelper;
use Browscap\Command\Helper\RewriteHelper;
use Ergebnis\Json\Normalizer\Exception\InvalidIndentSize;
use Ergebnis\Json\Normalizer\Exception\InvalidIndentStyle;
use Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodeOptions;
use Ergebnis\Json\Normalizer\Exception\InvalidNewLineString;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function is_string;
use function realpath;
use function sprintf;

class RewriteEnginesCommand extends Command
{
    private const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    /** @throws InvalidArgumentException */
    protected function configure(): void
    {
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('rewrite-engines')
            ->setDescription('rewrites the resource files for the engines')
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder);
    }

    /**
     * @return int 0 if everything went fine, or an error code
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws InvalidNewLineString
     * @throws InvalidIndentStyle
     * @throws InvalidIndentSize
     * @throws InvalidJsonEncodeOptions
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $loggerHelper = $this->getHelper('logger');
        assert($loggerHelper instanceof LoggerHelper);
        $logger = $loggerHelper->create($output);

        $resources = $input->getOption('resources');
        assert(is_string($resources));

        $enginesResourcePath = $resources . '/engines';

        $logger->info(sprintf('Resource folder: %s', $resources));

        $schema = 'file://' . realpath(__DIR__ . '/../../../schema/engines.json');

        $rewriteHelper = $this->getHelper('rewrite');
        assert($rewriteHelper instanceof RewriteHelper);

        $rewriteHelper->rewrite($logger, $enginesResourcePath, $schema, true);

        $output->writeln('Done');

        return self::SUCCESS;
    }
}
