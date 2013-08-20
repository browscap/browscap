<?php

namespace Browscap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Browscap\Parser\JsonParser;
use Browscap\Adapters\ClassPropertiesAdapter;
use Browscap\Adapters\UserAgentAdapter;
use Browscap\Entity\Device;
use Browscap\Entity\Platform;
use Browscap\Entity\RenderingEngine;
use Browscap\Entity\UserAgent;
use Browscap\Generator\BrowscapIniGenerator;

/**
 * @author James Titcumb <james@asgrim.com>
 */
class BuildCommand extends Command
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $resourceFolder;

    /**
     * @var \Browscap\Adapters\ClassPropertiesAdapter
     */
    protected $classPropertiesAdapter;

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('The JSON source files and builds the INI files')
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->resourceFolder = __DIR__ . '/../../../resources';

        $this->classPropertiesAdapter = new ClassPropertiesAdapter();

        $userAgents = $this->populateUserAgentEntities(
            $this->loadUserAgents(),
            $this->loadDevices(),
            $this->loadPlatforms(),
            $this->loadRenderingEngines()
        );

        $msg = sprintf("%d user agents loaded.", count($userAgents));
        $this->output->writeln($msg);

        $buildFolder = __DIR__ . '/../../../build';

        if (!file_exists($buildFolder)) {
            mkdir($buildFolder);
        }

        $generator = new BrowscapIniGenerator();

        file_put_contents($buildFolder . '/browscap.ini', $generator->generate($userAgents));
        $this->output->writeln('<info>Generated build/browscap.ini</info>');

        file_put_contents($buildFolder . '/browscap2.ini', $generator->generate($userAgents));
        $this->output->writeln('<info>Generated build/browscap2.ini</info>');

        file_put_contents($buildFolder . '/php_browscap.ini', $generator->generate($userAgents, true));
        $this->output->writeln('<info>Generated build/php_browscap.ini</info>');
    }

    /**
     * @return Browscap\Entity\Device[]
     */
    public function loadDevices()
    {
        $devices = $this->classPropertiesAdapter
            ->setParser(new JsonParser($this->resourceFolder . '/devices.json'))
            ->setEntityPrototype(new Device())
            ->setSourceProperty('devices')
            ->setPrimaryKey('deviceId')
            ->populateEntities();

        $msg = sprintf("%d devices loaded.", count($devices));
        $this->output->writeln($msg);

        return $devices;
    }

    /**
     * @return Browscap\Entity\Platform[]
     */
    public function loadPlatforms()
    {
        $platforms = $this->classPropertiesAdapter
            ->setParser(new JsonParser($this->resourceFolder . '/platforms.json'))
            ->setEntityPrototype(new Platform())
            ->setSourceProperty('platforms')
            ->setPrimaryKey('platformId')
            ->populateEntities();

        $msg = sprintf("%d platforms loaded.", count($platforms));
        $this->output->writeln($msg);

        return $platforms;
    }

    /**
     * @return Browscap\Entity\RenderingEngine[]
     */
    public function loadRenderingEngines()
    {
        $renderingEngines = $this->classPropertiesAdapter
            ->setParser(new JsonParser($this->resourceFolder . '/rendering-engines.json'))
            ->setEntityPrototype(new RenderingEngine())
            ->setSourceProperty('renderingEngines')
            ->setPrimaryKey('renderingEngineId')
            ->populateEntities();

        $msg = sprintf("%d rendering engines loaded.", count($renderingEngines));
        $this->output->writeln($msg);

        return $renderingEngines;
    }

    /**
     * @return \Browscap\Entity\UserAgent[]
     */
    public function loadUserAgents()
    {
        $uaSourceDirectory = $this->resourceFolder . '/user-agents';

        $iterator = new \RecursiveDirectoryIterator($uaSourceDirectory);

        $userAgentAdapter = new UserAgentAdapter();

        $userAgents = array();

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {

            if (!$file->isFile() || $file->getExtension() != 'json') continue;

            $userAgent = $userAgentAdapter
                ->setParser(new JsonParser($file->getPathname()))
                ->generateEntity();

            $userAgents[$userAgent->name] = $userAgent;
        }

        return $userAgents;
    }

    /**
     *
     * @param \Browscap\Entity\UserAgent[] $userAgents
     * @param \Browscap\Entity\Device[] $devices
     * @param \Browscap\Entity\Platform[] $platforms
     * @param \Browscap\Entity\RenderingEngine[] $renderingEngines
     * @return \Browscap\Entity\UserAgent[]
     */
    public function populateUserAgentEntities($userAgents, $devices, $platforms, $renderingEngines)
    {
        foreach ($userAgents as $userAgent) {

            /* @var $userAgent \Browscap\Entity\UserAgent */

            $this->populateForeignEntitiesById($userAgent, 'devices', $devices);
            $this->populateForeignEntitiesById($userAgent, 'platforms', $platforms);
            $this->populateForeignEntitiesById($userAgent, 'renderingEngines', $renderingEngines);

            // Not sure if we need to populate the parent entity...
            //if (!empty($userAgent->parent)) {
            //    $parentName = $userAgent->parent;
            //    $userAgent->parent = $userAgents[$parentName];
            //}
        }

        return $userAgents;
    }

    /**
     *
     * @param UserAgent $userAgent
     * @param string $type
     * @param array $resourcePool
     */
    public function populateForeignEntitiesById(UserAgent $userAgent, $type, $resourcePool)
    {
        if (is_array($userAgent->{$type})) {
            $populated = array();

            foreach ($userAgent->{$type} as $key => $id) {
                $populated[$id] = $resourcePool[$id];
            }

            $userAgent->{$type} = $populated;
        }
    }
}
