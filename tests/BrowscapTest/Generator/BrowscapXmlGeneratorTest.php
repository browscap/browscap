<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\BrowscapXmlGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Generator\DataCollection;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

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

    /**
     * @dataProvider generateFormatsDataProvider
     *
     * @param string $filename
     */
    public function testgetCollectionDataThrowsExceptionIfDataCollectionNotSet($filename)
    {
        $generator = new BrowscapXmlGenerator($filename);

        $this->setExpectedException('\LogicException', 'Data collection has not been set yet');
        $generator->getCollectionData();
    }

    /**
     * @dataProvider generateFormatsDataProvider
     *
     * @param string $filename
     */
    public function testSetCollectionData($filename)
    {
        $dataCollection = new DataCollection('1234');

        $collectionParser = new CollectionParser();
        $collectionParser
            ->setLogger($this->logger)
            ->setDataCollection($dataCollection)
        ;
        $collectionData = $collectionParser->parse();

        self::assertSame($dataCollection, $collectionParser->getDataCollection());

        $generator = new BrowscapXmlGenerator($filename);
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
        ;

        self::assertAttributeSame($collectionData, 'collectionData', $generator);
    }

    /**
     * @dataProvider generateFormatsDataProvider
     *
     * @param string $filename
     */
    public function testGetCollectionData($filename)
    {
        $dataCollection = new DataCollection('1234');

        $collectionParser = new CollectionParser();
        $collectionParser
            ->setLogger($this->logger)
            ->setDataCollection($dataCollection)
        ;
        $collectionData = $collectionParser->parse();

        self::assertSame($dataCollection, $collectionParser->getDataCollection());

        $generator = new BrowscapXmlGenerator($filename);
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
     *
     * @param string $filename
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

        $expectedFilename = __DIR__ . '/../../fixtures/xml/' . $filename;

        $outputfile = __DIR__ . '/../../fixtures/xml/temp_' . $filename;
        $generator  = new BrowscapXmlGenerator($outputfile);
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $generator->generate();

        self::assertStringEqualsFile(
            $expectedFilename,
            file_get_contents($outputfile)
        );
    }

    public function generateFeaturesDataProvider()
    {
        return [
            'bcv' => ['ua/features-bcv.json', 'xml/features-bcv.xml'],
            'basic' => ['ua/features-basic.json', 'xml/features-basic.xml'],
            'single-child' => ['ua/features-single-child.json', 'xml/features-single-child.xml'],
            'multi-child' => ['ua/features-multi-child.json', 'xml/features-multi-child.xml'],
            'versions' => ['ua/features-versions.json', 'xml/features-versions.xml'],
            'platforms' => ['ua/features-platforms.json', 'xml/features-platforms.xml'],
            'child-props' => ['ua/features-child-props.json', 'xml/features-child-props.xml'],
            'platform-props' => ['ua/features-platform-props.json', 'xml/features-platform-props.xml'],
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
            $this->getCollectionData([$fixturesDir . 'ua/default-properties.json', $fixturesDir . $jsonFile])
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

        $outputfile = $fixturesDir . str_replace('xml/', 'xml/temp_', $expectedXml);
        $generator  = new BrowscapXmlGenerator($outputfile);
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $generator->generate();

        self::assertStringEqualsFile($fixturesDir . $expectedXml, file_get_contents($outputfile));
    }

    /**
     * @dataProvider generateFormatsDataProvider
     *
     * @param string $filename
     *
     * @expectedException \LogicException
     */
    public function testGenerateInvalidFeatures($filename)
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

        $generator = new BrowscapXmlGenerator($filename);
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $generator->generate();
    }
}
