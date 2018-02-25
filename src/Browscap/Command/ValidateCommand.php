<?php
declare(strict_types = 1);
namespace Browscap\Command;

use Browscap\Helper\LoggerHelper;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Localheinz\Json\Normalizer;
use JsonSchema\Constraints;
use JsonSchema\Exception;
use JsonSchema\SchemaStorage;
use Localheinz\Json\Normalizer\Validator;
use Symfony\Component\Finder\Finder;

class ValidateCommand extends Command
{
    /**
     * @var string
     */
    private const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    protected function configure() : void
    {
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('validate')
            ->setDescription('Meta-Command to validate the resource and test files')
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null null or 0 if everything went fine, or an error code
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $loggerHelper = new LoggerHelper();
        $logger       = $loggerHelper->create($output);

        $failed = false;
        $command    = $this->getApplication()->find('validate-browsers');

        $input = new ArrayInput(
            [
                'command'     => 'validate-browsers',
                '--resources' => $input->getOption('resources'),
            ]
        );

        $returnCode = $command->run($input, $output);

        if (0 < $returnCode) {
            $logger->error('There was an error executing the "validate-browsers" command, cannot continue.');

            $failed = true;
        }
        $command    = $this->getApplication()->find('validate-devices');

        $input = new ArrayInput(
            [
                'command'     => 'validate-devices',
                '--resources' => $input->getOption('resources'),
            ]
        );

        $returnCode = $command->run($input, $output);

        if (0 < $returnCode) {
            $logger->error('There was an error executing the "validate-devices" command, cannot continue.');

            $failed = true;
        }

        $command    = $this->getApplication()->find('validate-engines');

        $input = new ArrayInput(
            [
                'command'     => 'validate-engines',
                '--resources' => $input->getOption('resources'),
            ]
        );

        $returnCode = $command->run($input, $output);

        if (0 < $returnCode) {
            $logger->error('There was an error executing the "validate-engines" command, cannot continue.');

            $failed = true;
        }

        $command    = $this->getApplication()->find('validate-platforms');

        $input = new ArrayInput(
            [
                'command'     => 'validate-platforms',
                '--resources' => $input->getOption('resources'),
            ]
        );

        $returnCode = $command->run($input, $output);

        if (0 < $returnCode) {
            $logger->error('There was an error executing the "validate-platforms" command, cannot continue.');

            $failed = true;
        }

        $command    = $this->getApplication()->find('validate-core-divisions');

        $input = new ArrayInput(
            [
                'command'     => 'validate-core-divisions',
                '--resources' => $input->getOption('resources'),
            ]
        );

        $returnCode = $command->run($input, $output);

        if (0 < $returnCode) {
            $logger->error('There was an error executing the "validate-core-divisions" command, cannot continue.');

            $failed = true;
        }

        $command    = $this->getApplication()->find('validate-divisions');

        $input = new ArrayInput(
            [
                'command'     => 'validate-divisions',
                '--resources' => $input->getOption('resources'),
            ]
        );

        $returnCode = $command->run($input, $output);

        if (0 < $returnCode) {
            $logger->error('There was an error executing the "validate-divisions" command, cannot continue.');

            $failed = true;
        }

        if (!$failed) {
            $output->writeln('the files are valid');
        }

        return (int) $failed;
    }
}
