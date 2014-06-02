<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\BrowscapJsonGenerator;
use Browscap\Generator\BuildGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Generator\DataCollection;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class BrowscapJsonGeneratorTest
 *
 * @package BrowscapTest\Generator
 */
class BrowscapJsonGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    public function setUp()
    {
        $this->logger = new Logger('browscapTest', array(new NullHandler()));
    }

    private function getPlatformsJsonFixture()
    {
        return __DIR__ . '/../../fixtures/platforms/platforms.json';
    }

    private function getUserAgentFixtures()
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
    private function getCollectionData(array $files)
    {
        $dataCollection = new DataCollection('1234');
        $dataCollection
            ->setLogger($this->logger)
            ->addPlatformsFile($this->getPlatformsJsonFixture())
            ->addEnginesFile(__DIR__ . '/../../fixtures/engines/engines.json')
        ;

        $dateProperty = new \ReflectionProperty(get_class($dataCollection), 'generationDate');
        $dateProperty->setAccessible(true);
        $dateProperty->setValue($dataCollection, new \DateTime('2010-12-31 12:34:56'));

        foreach ($files as $file) {
            $dataCollection->addSourceFile($file);
        }

        return $dataCollection;
    }

    public function testgetCollectionDataThrowsExceptionIfDataCollectionNotSet()
    {
        $generator = new BrowscapJsonGenerator();

        $this->setExpectedException('\LogicException', 'Data collection has not been set yet');
        $generator->getCollectionData();
    }

    public function testSetCollectionData()
    {
        $dataCollection = new DataCollection('1234');

        $collectionParser = new CollectionParser();
        $collectionParser
            ->setLogger($this->logger)
            ->setDataCollection($dataCollection)
        ;
        $collectionData = $collectionParser->parse();

        self::assertSame($dataCollection, $collectionParser->getDataCollection());

        $generator = new BrowscapJsonGenerator();
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
        ;

        self::assertAttributeSame($collectionData, 'collectionData', $generator);
    }

    public function testGetCollectionData()
    {
        $dataCollection = new DataCollection('1234');

        $collectionParser = new CollectionParser();
        $collectionParser
            ->setLogger($this->logger)
            ->setDataCollection($dataCollection)
        ;
        $collectionData = $collectionParser->parse();

        self::assertSame($dataCollection, $collectionParser->getDataCollection());

        $generator = new BrowscapJsonGenerator();
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
        ;

        self::assertSame($collectionData, $generator->getCollectionData());
    }

    public function generateFormatsDataProvider()
    {
        return [
            'json' => ['browscap.json'],
        ];
    }

    /**
     * @dataProvider generateFormatsDataProvider
     */
    public function testGenerateWithDifferentFormattingOptions($filename)
    {
        $collectionParser = new CollectionParser();
        $collectionParser
            ->setLogger($this->logger)
            ->setDataCollection($this->getCollectionData($this->getUserAgentFixtures()))
        ;
        $collectionData = $collectionParser->parse();

        $comments = array(
            'Provided courtesy of http://tempdownloads.browserscap.com/',
            'Created on Friday, December 31, 2010 at 12:34 PM UTC',
            'Keep up with the latest goings-on with the project:',
            'Follow us on Twitter <https://twitter.com/browscap>, or...',
            'Like us on Facebook <https://facebook.com/browscap>, or...',
            'Collaborate on GitHub <https://github.com/GaryKeith/browscap>, or...',
            'Discuss on Google Groups <https://groups.google.com/d/forum/browscap>.'
        );

        $generator = new BrowscapJsonGenerator();
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $json = $generator->generate();

        $expectedFilename = __DIR__ . '/../../fixtures/json/' . $filename;

        self::assertStringEqualsFile($expectedFilename, $json);
    }

    public function generateFeaturesDataProvider()
    {
        $fixturesDir = __DIR__ . '/../../fixtures/';

        return [
            'bcv' => [$fixturesDir . 'ua/features-bcv.json', $fixturesDir . 'json/features-bcv.json'],
            'basic' => [$fixturesDir . 'ua/features-basic.json', $fixturesDir . 'json/features-basic.json'],
            'single-child' => [$fixturesDir . 'ua/features-single-child.json', $fixturesDir . 'json/features-single-child.json'],
            'multi-child' => [$fixturesDir . 'ua/features-multi-child.json', $fixturesDir . 'json/features-multi-child.json'],
            'versions' => [$fixturesDir . 'ua/features-versions.json', $fixturesDir . 'json/features-versions.json'],
            'platforms' => [$fixturesDir . 'ua/features-platforms.json', $fixturesDir . 'json/features-platforms.json'],
            'child-props' => [$fixturesDir . 'ua/features-child-props.json', $fixturesDir . 'json/features-child-props.json'],
            'platform-props' => [$fixturesDir . 'ua/features-platform-props.json', $fixturesDir . 'json/features-platform-props.json'],
        ];
    }

    /**
     * @dataProvider generateFeaturesDataProvider
     */
    public function testGenerateFeatures($jsonFile, $expectedJson)
    {
        $fixturesDir = __DIR__ . '/../../fixtures/';

        $collectionParser = new CollectionParser();
        $collectionParser
            ->setLogger($this->logger)
            ->setDataCollection(
            $this->getCollectionData([$fixturesDir . 'ua/default-properties.json', $jsonFile])
        );
        $collectionData = $collectionParser->parse();

        $comments = array(
            'Provided courtesy of http://tempdownloads.browserscap.com/',
            'Created on Friday, December 31, 2010 at 12:34 PM UTC',
            'Keep up with the latest goings-on with the project:',
            'Follow us on Twitter <https://twitter.com/browscap>, or...',
            'Like us on Facebook <https://facebook.com/browscap>, or...',
            'Collaborate on GitHub <https://github.com/GaryKeith/browscap>, or...',
            'Discuss on Google Groups <https://groups.google.com/d/forum/browscap>.'
        );

        $generator = new BrowscapJsonGenerator();
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $json = $generator->generate();

        self::assertStringEqualsFile($expectedJson, $json);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGenerateInvalidFeatures()
    {
        $fixturesDir = __DIR__ . '/../../fixtures/';

        $collectionParser = new CollectionParser();
        $collectionParser
            ->setLogger($this->logger)
            ->setDataCollection(
                $this->getCollectionData(
                    [
                        $fixturesDir . 'ua/default-properties.json',
                        $fixturesDir . 'ua/features-skip-invalid-children.json'
                    ]
                )
            );
        $collectionData = $collectionParser->parse();

        $comments = array(
            'Provided courtesy of http://tempdownloads.browserscap.com/',
            'Created on Friday, December 31, 2010 at 12:34 PM UTC',
            'Keep up with the latest goings-on with the project:',
            'Follow us on Twitter <https://twitter.com/browscap>, or...',
            'Like us on Facebook <https://facebook.com/browscap>, or...',
            'Collaborate on GitHub <https://github.com/GaryKeith/browscap>, or...',
            'Discuss on Google Groups <https://groups.google.com/d/forum/browscap>.'
        );

        $generator = new BrowscapJsonGenerator();
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $generator->generate();
    }
}
