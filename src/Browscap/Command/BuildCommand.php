<?php

namespace Browscap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Browscap\Adapters\DeviceAdapter;
use Browscap\Parser\JsonParser;
use Browscap\Adapters\ClassPropertiesAdapter;
use Browscap\Entity\Device;
use Browscap\Entity\Platform;
use Browscap\Entity\RenderingEngine;

/**
 * @author James Titcumb <james@asgrim.com
 */
class BuildCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('The JSON source files and builds the INI files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resourceFolder = __DIR__ . '/../../../resources';

        $adapter = new ClassPropertiesAdapter();

        $devices = $adapter
            ->setParser(new JsonParser($resourceFolder . '/devices.json'))
            ->setEntityPrototype(new Device())
            ->setSourceProperty('devices')
            ->setPrimaryKey('deviceId')
            ->populateEntities();

        $msg = sprintf("%d devices loaded.", count($devices));
        $output->writeln($msg);

        $platforms = $adapter
            ->setParser(new JsonParser($resourceFolder . '/platforms.json'))
            ->setEntityPrototype(new Platform())
            ->setSourceProperty('platforms')
            ->setPrimaryKey('platformId')
            ->populateEntities();

        $msg = sprintf("%d platforms loaded.", count($devices));
        $output->writeln($msg);

        $renderingEngines = $adapter
            ->setParser(new JsonParser($resourceFolder . '/rendering-engines.json'))
            ->setEntityPrototype(new RenderingEngine())
            ->setSourceProperty('renderingEngines')
            ->setPrimaryKey('renderingEngineId')
            ->populateEntities();

        $msg = sprintf("%d rendering engines loaded.", count($devices));
        $output->writeln($msg);
    }

}
