<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\BrowscapXmlGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Generator\DataCollection;

class BrowscapXmlGeneratorTest extends \PHPUnit_Framework_TestCase
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
     * @param $files
     *
     * @return \Browscap\Generator\DataCollection
     */
    private function getCollectionData($files)
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
        $generator = new BrowscapXmlGenerator();

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

        $generator = new BrowscapXmlGenerator();
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

        $generator = new BrowscapXmlGenerator();
        $generator->setCollectionData($collectionData);

        self::assertSame($collectionData, $generator->getCollectionData());
    }

    public function generateFormatsDataProvider()
    {
        return [
            'xml' => ['browscap.xml', false, true, false],
        ];
    }

    /**
     * @dataProvider generateFormatsDataProvider
     */
    public function testGenerateWithDifferentFormattingOptions($filename, $quoteStringProperties, $includeExtraProperties, $liteOnly)
    {
        $this->markTestSkipped();

        $collectionParser = new CollectionParser();
        $collectionParser->setDataCollection($this->getCollectionData($this->getUserAgentFixtures()));
        $collectionData = $collectionParser->parse();

        $generator = new BrowscapXmlGenerator();
        $generator->setCollectionData($collectionData);
        $generator->setOptions($quoteStringProperties, $includeExtraProperties, $liteOnly);

        $ini = $generator->generate();

        $expectedFilename = __DIR__ . '/../../fixtures/xml/' . $filename;

        self::assertStringEqualsFile($expectedFilename, $ini);
    }

    public function generateFeaturesDataProvider()
    {
        $fixturesDir = __DIR__ . '/../../fixtures/';

        return [
            'bcv' => [$fixturesDir . 'ua/features-bcv.json', $fixturesDir . 'xml/browscap.xml'],
        ];
    }

    /**
     * @dataProvider generateFeaturesDataProvider
     */
    public function testGenerateFeatures($jsonFile, $expectedIni)
    {
        $this->markTestSkipped();

        $collectionParser = new CollectionParser();
        $collectionParser->setDataCollection($this->getCollectionData([$jsonFile]));
        $collectionData = $collectionParser->parse();

        $generator = new BrowscapXmlGenerator();
        $generator->setCollectionData($collectionData);

        $xml = $generator->generate();

        self::assertStringEqualsFile($expectedIni, $xml);
    }

    public function propertyNameTypeDataProvider()
    {
        return [
            ['Comment', 'string'],
            ['Browser', 'string'],
            ['Platform', 'string'],
            ['Platform_Description', 'string'],
            ['Device_Name', 'string'],
            ['Device_Maker', 'string'],
            ['RenderingEngine_Name', 'string'],
            ['RenderingEngine_Description', 'string'],
            ['Parent', 'generic'],
            ['Platform_Version', 'generic'],
            ['RenderingEngine_Version', 'generic'],
            ['Version', 'number'],
            ['MajorVer', 'number'],
            ['MinorVer', 'number'],
            ['CssVersion', 'number'],
            ['AolVersion', 'number'],
            ['Alpha', 'boolean'],
            ['Beta', 'boolean'],
            ['Win16', 'boolean'],
            ['Win32', 'boolean'],
            ['Win64', 'boolean'],
            ['Frames', 'boolean'],
            ['IFrames', 'boolean'],
            ['Tables', 'boolean'],
            ['Cookies', 'boolean'],
            ['BackgroundSounds', 'boolean'],
            ['JavaScript', 'boolean'],
            ['VBScript', 'boolean'],
            ['JavaApplets', 'boolean'],
            ['ActiveXControls', 'boolean'],
            ['isMobileDevice', 'boolean'],
            ['isSyndicationReader', 'boolean'],
            ['Crawler', 'boolean'],
        ];
    }

    /**
     * @dataProvider propertyNameTypeDataProvider
     */
    public function testGetPropertyType($propertyName, $expectedType)
    {
        $generator = new BrowscapXmlGenerator();
        $actualType = $generator->getPropertyType($propertyName);
        self::assertSame($expectedType, $actualType, "Property {$propertyName} should be {$expectedType} (was {$actualType})");
    }

    public function testGetPropertyTypeThrowsExceptionIfPropertyNameNotMapped()
    {
        $generator = new BrowscapXmlGenerator();

        $this->setExpectedException('\InvalidArgumentException', 'Property Foobar did not have a defined property type');
        $generator->getPropertyType('Foobar');
    }

    public function extraPropertiesDataProvider()
    {
        return [
            ['Comment', false],
            ['Browser', false],
            ['Platform', false],
            ['Platform_Description', true],
            ['Device_Name', true],
            ['Device_Maker', true],
            ['RenderingEngine_Name', true],
            ['RenderingEngine_Description', true],
            ['Parent', false],
            ['Platform_Version', false],
            ['RenderingEngine_Version', true],
            ['Version', false],
            ['MajorVer', false],
            ['MinorVer', false],
            ['CssVersion', false],
            ['AolVersion', false],
            ['Alpha', false],
            ['Beta', false],
            ['Win16', false],
            ['Win32', false],
            ['Win64', false],
            ['Frames', false],
            ['IFrames', false],
            ['Tables', false],
            ['Cookies', false],
            ['BackgroundSounds', false],
            ['JavaScript', false],
            ['VBScript', false],
            ['JavaApplets', false],
            ['ActiveXControls', false],
            ['isMobileDevice', false],
            ['isSyndicationReader', false],
            ['Crawler', false],
        ];
    }

    /**
     * @dataProvider extraPropertiesDataProvider
     */
    public function testIsExtraProperty($propertyName, $isExtra)
    {
        $generator = new BrowscapXmlGenerator();
        $actualValue = $generator->isExtraProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }
}
