<?php

namespace Browscap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Joshua Estes <Joshua.Estes@ScenicCityLabs.com>
 */
class PropertyCommand extends Command
{

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('property')
            ->setDescription('Display information about various properties about a browser.')
            ->setDefinition(array(
                new InputArgument('property', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Display information on a specific property'),
            ))
            ->setHelp(<<<EOF
EOF
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file     = __DIR__ . '/../../../resources/property-name-docs.xml';
        $contents = file_get_contents($file);
        $crawler  = new Crawler();
        $crawler->addXmlContent($contents);

        $property = $input->getArgument('property');
        if (!empty($property)) {
            foreach ($property as $v) {
                $node = $crawler->filterXPath(sprintf('BrowscapPropertiesDocumentation/BrowscapProperty//[PropertyName="%s"]/*', strtoupper($v)));
                $this->displayProperty($node, $output);
            }

            return 0;
        }

        // Might be a better way to do this?
        $nodes = $crawler->filterXPath('BrowscapPropertiesDocumentation')->children();
        foreach ($nodes as $node) {
            foreach ($node->childNodes as $i) {
                if ($i->nodeName !== "#text") {
                    $output->writeln(array(
                        sprintf('%12s: %s',$i->nodeName,$i->nodeValue),
                    ));
                }
            }
            $output->writeln('');
        }
    }

    private function displayProperty($node, OutputInterface $output)
    {
        if ($node->count() <= 0) {
            return;
        }
        foreach ($node as $v) {
            $output->writeln(array(
                sprintf('%12s: %s',$v->nodeName,$v->nodeValue),
            ));
        }
        $output->writeln('--');
    }

}
