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
                new InputOption('ini', null, InputOption::VALUE_NONE, 'Build an ini file'),
                new InputOption('php', null, InputOption::VALUE_NONE, 'Builds a file for use with PHP'),
                new InputOption('asp', null, InputOption::VALUE_NONE, 'Builds a file for use with ASP'),
                new InputOption('lite', null, InputOption::VALUE_NONE, 'Leave off some features'),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file     = __DIR__ . '/../../../resources/browscap.xml';
        $contents = file_get_contents($file);
        $crawler  = new Crawler();
        $crawler->addXmlContent($contents);

        $nodes = $crawler->filterXPath('browsercaps')->children()->filterXPath('browsercapitems')->children();
        foreach ($nodes as $node) {
            $browsecapItem = $node->getAttributeNode('name')->nodeValue;
            $output->writeln(sprintf('Item: %s',$browsecapItem));
            foreach ($node->childNodes as $i) {
                if ($i->nodeName !== "#text") {
                    $name = $i->getAttributeNode('name')->nodeValue;
                    $value = $i->getAttributeNode('value')->nodeValue;
                    $output->writeln(array(
                        sprintf('%30s: %s',$name,$value),
                    ));
                }
            }
            die();
            $output->writeln('');
        }
    }

}
