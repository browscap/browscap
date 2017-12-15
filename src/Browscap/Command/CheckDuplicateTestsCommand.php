<?php
declare(strict_types = 1);
namespace Browscap\Command;

use Browscap\Helper\IteratorHelper;
use Browscap\Helper\LoggerHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDuplicateTestsCommand extends Command
{
    protected function configure() : void
    {
        $this
            ->setName('check-duplicate-tests')
            ->setDescription('checks the test cases for duplicates');
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

        [, $errors] = (new IteratorHelper())->getTestFiles($logger);

        if (!empty($errors)) {
            return 1;
        }

        return 0;
    }
}
