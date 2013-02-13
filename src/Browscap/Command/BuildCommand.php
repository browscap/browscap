<?php

namespace Browscap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Joshua Estes <Joshua.Estes@ScenicCityLabs.com>
 */
class BuildCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Takes an XML file and builds into various ini files and formats for use on other systems.')
            ->setDefinition(array(
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file     = __DIR__ . '/../../../resources/browscap.xml';
        $contents = file_get_contents($file);
        $xml      = new \SimpleXMLElement($contents);
        $browsers = array();

        foreach ($xml->browsercapitems->children() as $item) {
            $properties = $item->children();
            $browser    = array();
            foreach ($properties as $property) {
                $browser[(string) $property['name']] = (string) $property['value'];
            }
            $b = new \Browscap\Browser();
            $b->setData($browser);
            $browsers[] = $b;
        }

        $buildDir = __DIR__ . '/../../../build';
        $file     = $buildDir . '/browscap.ini';
        $lines    = array();
        foreach ($browsers as $browser) {
            $lines[] = $browser->toIni();
        }
        file_put_contents($file, implode($lines, "\n"));
    }

}
