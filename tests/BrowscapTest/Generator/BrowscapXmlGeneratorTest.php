<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\BrowscapXmlGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Generator\DataCollection;

/**
 * Class BrowscapXmlGeneratorTest
 *
 * @package BrowscapTest\Generator
 */
class BrowscapXmlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    public function setUp()
    {
        $this->logger = new \Monolog\Logger('browscapTest', array(new \Monolog\Handler\NullHandler()));
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
        ;

        $dateProperty = new \ReflectionProperty(get_class($dataCollection), 'generationDate');
        $dateProperty->setAccessible(true);
        $dateProperty->setValue($dataCollection, new \DateTime('2010-12-31 12:34:56'));

        foreach ($files as $file)
        {
            $dataCollection->addSourceFile($file);
        }

        return $dataCollection;
    }

    public function testgetCollectionDataThrowsExceptionIfDataCollectionNotSet()
    {
        $generator = new BrowscapXmlGenerator();

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

        $generator = new BrowscapXmlGenerator();
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

        $generator = new BrowscapXmlGenerator();
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
        ;

        self::assertSame($collectionData, $generator->getCollectionData());
    }

    public function generateFormatsDataProvider()
    {
        return [
            'xml' => ['browscap.xml'],
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

        $generator = new BrowscapXmlGenerator();
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $ini = $generator->generate();

        $expectedFilename = __DIR__ . '/../../fixtures/xml/' . $filename;

        self::assertStringEqualsFile($expectedFilename, $ini);
    }

    public function generateFeaturesDataProvider()
    {
        $fixturesDir = __DIR__ . '/../../fixtures/';

        return [
            'bcv' => [$fixturesDir . 'ua/features-bcv.json', $fixturesDir . 'xml/features-bcv.xml'],
            'basic' => [$fixturesDir . 'ua/features-basic.json', $fixturesDir . 'xml/features-basic.xml'],
            'single-child' => [$fixturesDir . 'ua/features-single-child.json', $fixturesDir . 'xml/features-single-child.xml'],
            'multi-child' => [$fixturesDir . 'ua/features-multi-child.json', $fixturesDir . 'xml/features-multi-child.xml'],
            'versions' => [$fixturesDir . 'ua/features-versions.json', $fixturesDir . 'xml/features-versions.xml'],
            'platforms' => [$fixturesDir . 'ua/features-platforms.json', $fixturesDir . 'xml/features-platforms.xml'],
            'child-props' => [$fixturesDir . 'ua/features-child-props.json', $fixturesDir . 'xml/features-child-props.xml'],
            'platform-props' => [$fixturesDir . 'ua/features-platform-props.json', $fixturesDir . 'xml/features-platform-props.xml'],
            'skip-invalid-children' => [$fixturesDir . 'ua/features-skip-invalid-children.json', $fixturesDir . 'xml/features-skip-invalid-children.xml'],
        ];
    }

    /**
     * @dataProvider generateFeaturesDataProvider
     */
    public function testGenerateFeatures($jsonFile, $expectedXml)
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

        $generator = new BrowscapXmlGenerator();
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $xml = $generator->generate();

        self::assertStringEqualsFile($expectedXml, $xml);
    }
}
