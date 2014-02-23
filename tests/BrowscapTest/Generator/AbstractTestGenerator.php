<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\DataCollection;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class AbstractTestGenerator
 *
 * @package BrowscapTest\Generator
 */
abstract class AbstractTestGenerator extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger = null;

    public function setUp()
    {
        $this->logger = new Logger('browscapTest', array(new NullHandler()));
    }

    protected function getPlatformsJsonFixture()
    {
        return __DIR__ . '/../../fixtures/platforms/platforms.json';
    }

    protected function getUserAgentFixtures()
    {
        $dir = __DIR__ . '/../../fixtures/ua';

        return [
            $dir . '/default-properties.json',
            $dir . '/test1.json',
            $dir . '/default-browser.json',
        ];
    }

    /**
     * @param array $files
     *
     * @return \Browscap\Generator\DataCollection
     */
    protected function getCollectionData(array $files)
    {
        $dataCollection = new DataCollection('1234');
        $dataCollection
            ->setLogger($this->logger)
            ->addPlatformsFile($this->getPlatformsJsonFixture())
        ;

        $dateProperty = new \ReflectionProperty(get_class($dataCollection), 'generationDate');
        $dateProperty->setAccessible(true);
        $dateProperty->setValue($dataCollection, new \DateTime('2010-12-31 12:34:56'));

        foreach ($files as $file) {
            $dataCollection->addSourceFile($file);
        }

        return $dataCollection;
    }
}
