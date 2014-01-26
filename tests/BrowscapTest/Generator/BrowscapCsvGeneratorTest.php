<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\BrowscapCsvGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Generator\DataCollection;

class BrowscapCsvGeneratorTest extends \PHPUnit_Framework_TestCase
{
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
        $dataCollection->addPlatformsFile($this->getPlatformsJsonFixture());

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
        $generator = new BrowscapCsvGenerator();

        $this->setExpectedException('\LogicException', 'Data collection has not been set yet');
        $generator->getCollectionData();
    }

    public function testSetCollectionData()
    {
        $dataCollection = new DataCollection('1234');

        $collectionParser = new CollectionParser();
        $collectionParser->setDataCollection($dataCollection);
        $collectionData = $collectionParser->parse();

        self::assertSame($dataCollection, $collectionParser->getDataCollection());

        $generator = new BrowscapCsvGenerator();
        $generator->setCollectionData($collectionData);

        self::assertAttributeSame($collectionData, 'collectionData', $generator);
    }

    public function testGetCollectionData()
    {
        $dataCollection = new DataCollection('1234');

        $collectionParser = new CollectionParser();
        $collectionParser->setDataCollection($dataCollection);
        $collectionData = $collectionParser->parse();

        self::assertSame($dataCollection, $collectionParser->getDataCollection());

        $generator = new BrowscapCsvGenerator();
        $generator->setCollectionData($collectionData);

        self::assertSame($collectionData, $generator->getCollectionData());
    }

    public function generateFormatsDataProvider()
    {
        return [
            'csv' => ['browscap.csv', false, true, false],
        ];
    }

    /**
     * @dataProvider generateFormatsDataProvider
     */
    public function testGenerateWithDifferentFormattingOptions($filename, $quoteStringProperties, $includeExtraProperties, $liteOnly)
    {
        $collectionParser = new CollectionParser();
        $collectionParser->setDataCollection($this->getCollectionData($this->getUserAgentFixtures()));
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

        $generator = new BrowscapCsvGenerator();
        $generator
            ->setCollectionData($collectionData)
            ->setOptions($quoteStringProperties, $includeExtraProperties, $liteOnly)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $ini = $generator->generate();

        $expectedFilename = __DIR__ . '/../../fixtures/csv/' . $filename;

        self::assertStringEqualsFile($expectedFilename, $ini);
    }

    public function generateFeaturesDataProvider()
    {
        $fixturesDir = __DIR__ . '/../../fixtures/';

        return [
            'bcv' => [$fixturesDir . 'ua/features-bcv.json', $fixturesDir . 'csv/features-bcv.csv'],
            'basic' => [$fixturesDir . 'ua/features-basic.json', $fixturesDir . 'csv/features-basic.csv'],
            'single-child' => [$fixturesDir . 'ua/features-single-child.json', $fixturesDir . 'csv/features-single-child.csv'],
            'multi-child' => [$fixturesDir . 'ua/features-multi-child.json', $fixturesDir . 'csv/features-multi-child.csv'],
            'versions' => [$fixturesDir . 'ua/features-versions.json', $fixturesDir . 'csv/features-versions.csv'],
            'platforms' => [$fixturesDir . 'ua/features-platforms.json', $fixturesDir . 'csv/features-platforms.csv'],
            'child-props' => [$fixturesDir . 'ua/features-child-props.json', $fixturesDir . 'csv/features-child-props.csv'],
            'platform-props' => [$fixturesDir . 'ua/features-platform-props.json', $fixturesDir . 'csv/features-platform-props.csv'],
            'skip-invalid-children' => [$fixturesDir . 'ua/features-skip-invalid-children.json', $fixturesDir . 'csv/features-skip-invalid-children.csv'],
        ];
    }

    /**
     * @dataProvider generateFeaturesDataProvider
     */
    public function testGenerateFeatures($jsonFile, $expectedCsv)
    {
        $fixturesDir = __DIR__ . '/../../fixtures/';

        $collectionParser = new CollectionParser();
        $collectionParser->setDataCollection(
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

        $generator = new BrowscapCsvGenerator();
        $generator
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $csv = $generator->generate();

        self::assertStringEqualsFile($expectedCsv, $csv);
    }
}
