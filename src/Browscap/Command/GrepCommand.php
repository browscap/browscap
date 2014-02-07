<?php

namespace Browscap\Command;

use Browscap\Generator\BrowscapIniGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Helper\CollectionCreator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use phpbrowscap\Browscap;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class GrepCommand extends Command
{
    const MODE_MATCHED = 'matched';
    const MODE_UNMATCHED = 'unmatched';

    /**
     * @var \phpbrowscap\Browscap
     */
    protected $browscap;

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('grep')
            ->setDescription('')
            ->addArgument('inputFile', InputArgument::REQUIRED, 'The input file to test')
            ->addArgument('iniFile', InputArgument::OPTIONAL, 'The INI file to test against')
            ->addOption('mode', null, InputOption::VALUE_REQUIRED, 'What mode (matched/unmatched)', self::MODE_UNMATCHED);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cache_dir = sys_get_temp_dir() . '/browscap-grep/' . microtime(true) . '/';

        if (!file_exists($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }

        $iniFile = $input->getArgument('iniFile');

        if (!$iniFile || !file_exists($iniFile)) {
            $output->writeln('<info>iniFile Argument not set or invalid - creating iniFile from resources</info>');
            $resourceFolder = __DIR__ . BuildCommand::DEFAULT_RESOURCES_FOLDER;

            $collection = CollectionCreator::createDataCollection('temporary-version', $resourceFolder);

            $version = $collection->getVersion();
            $dateUtc = $collection->getGenerationDate()->format('l, F j, Y \a\t h:i A T');
            $date    = $collection->getGenerationDate()->format('r');

            $comments = array(
                'Provided courtesy of http://browscap.org/',
                'Created on ' . $dateUtc,
                'Keep up with the latest goings-on with the project:',
                'Follow us on Twitter <https://twitter.com/browscap>, or...',
                'Like us on Facebook <https://facebook.com/browscap>, or...',
                'Collaborate on GitHub <https://github.com/browscap>, or...',
                'Discuss on Google Groups <https://groups.google.com/forum/#!forum/browscap>.'
            );

            $collectionParser = new CollectionParser();
            $collectionParser->setDataCollection($collection);
            $collectionData = $collectionParser->parse();

            $iniGenerator = new BrowscapIniGenerator();
            $iniGenerator->setCollectionData($collectionData);

            $iniFile = $cache_dir . 'full_php_browscap.ini';

            $iniGenerator
                ->setOptions(true, true, false)
                ->setComments($comments)
                ->setVersionData(array('version' => $version, 'released' => $date))
            ;

            file_put_contents($iniFile, $iniGenerator->generate());
        }

        $this->browscap = new Browscap($cache_dir);
        $this->browscap->localFile = $iniFile;

        $inputFile = $input->getArgument('inputFile');
        $mode = $input->getOption('mode');

        if (!in_array($mode, array(self::MODE_MATCHED, self::MODE_UNMATCHED))) {
            throw new \Exception('Mode must be "matched" or "unmatched"');
        }

        if (!file_exists($inputFile)) {
            throw new \Exception('Input File "' . $inputFile . '" does not exist, or cannot access');
        }

        $fileContents = file_get_contents($inputFile);

        $uas = explode("\n", $fileContents);

        foreach ($uas as $ua) {
            if ($ua == '') {
                continue;
            }

            $this->testUA($ua, $mode);
        }

        $output->writeln('<info>All done.</info>');
    }

    protected function testUA($ua, $mode)
    {
        $data = $this->browscap->getBrowser($ua, true);

        if ($mode == 'unmatched' && $data['Browser'] == 'Default Browser') {
            echo $ua . "\n";
        } else if ($mode == 'matched' && $data['Browser'] != 'Default Browser') {
            echo $ua . "\n";
        }
    }
}
