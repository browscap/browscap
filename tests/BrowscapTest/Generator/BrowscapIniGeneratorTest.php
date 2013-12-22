<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\BrowscapIniGenerator;
use Browscap\Generator\DataCollection;

class BrowscapIniGeneratorTest extends \PHPUnit_Framework_TestCase
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
     * @return \Browscap\Generator\DataCollection
     */
    private function getDataCollection($files)
    {
        $dataCollection = new DataCollection('1234');
        $dataCollection->addPlatformsFile($this->getPlatformsJsonFixture());

        $dateProperty = new \ReflectionProperty(get_class($dataCollection), 'generationDate');
        $dateProperty->setAccessible(true);
        $dateProperty->setValue($dataCollection, new \DateTime('2010-12-31 12:34:56'));

        $files = $files;
        foreach ($files as $file)
        {
        	$dataCollection->addSourceFile($file);
        }

        return $dataCollection;
    }

    public function testGetDataCollectionThrowsExceptionIfDataCollectionNotSet()
    {
        $generator = new BrowscapIniGenerator();

        $this->setExpectedException('\LogicException', 'Data collection has not been set yet');
        $generator->getDataCollection();
    }

    public function testSetDataCollection()
    {
        $this->markTestIncomplete();
    }

    public function testGetDataCollection()
    {
        $this->markTestIncomplete();
    }

    public function testSetOptions()
    {
        $this->markTestIncomplete();
    }

    public function generateFormatsDataProvider()
    {
        return [
            'asp_full' => ['full_asp_browscap.ini', false, true, false],
            'php_full' => ['full_php_browscap.ini', true, true, false],
            'asp_std' => ['browscap.ini', false, false, false],
            'php_std' => ['php_browscap.ini', true, false, false],
            'asp_lite' => ['lite_asp_browscap.ini', false, false, true],
            'php_lite' => ['lite_php_browscap.ini', true, false, true],
        ];
    }

    /**
     * @dataProvider generateFormatsDataProvider
     */
    public function testGenerateWithDifferentFormattingOptions($filename, $quoteStringProperties, $includeExtraProperties, $liteOnly)
    {
        $generator = new BrowscapIniGenerator();
        $generator->setDataCollection($this->getDataCollection($this->getUserAgentFixtures()));
        $generator->setOptions($quoteStringProperties, $includeExtraProperties, $liteOnly);

        $ini = $generator->generate();

        $expectedFilename = __DIR__ . '/../../fixtures/ini/' . $filename;

        $this->assertStringEqualsFile($expectedFilename, $ini);
    }

    public function generateFeaturesDataProvider()
    {
        $fixturesDir = __DIR__ . '/../../fixtures/';

        return [
            'bcv' => [$fixturesDir . 'ua/features-bcv.json', $fixturesDir . 'ini/features-bcv.ini'],
            'basic' => [$fixturesDir . 'ua/features-basic.json', $fixturesDir . 'ini/features-basic.ini'],
            'single-child' => [$fixturesDir . 'ua/features-single-child.json', $fixturesDir . 'ini/features-single-child.ini'],
            'multi-child' => [$fixturesDir . 'ua/features-multi-child.json', $fixturesDir . 'ini/features-multi-child.ini'],
            'versions' => [$fixturesDir . 'ua/features-versions.json', $fixturesDir . 'ini/features-versions.ini'],
            'platforms' => [$fixturesDir . 'ua/features-platforms.json', $fixturesDir . 'ini/features-platforms.ini'],
            'child-props' => [$fixturesDir . 'ua/features-child-props.json', $fixturesDir . 'ini/features-child-props.ini'],
            'platform-props' => [$fixturesDir . 'ua/features-platform-props.json', $fixturesDir . 'ini/features-platform-props.ini'],
            'skip-invalid-children' => [$fixturesDir . 'ua/features-skip-invalid-children.json', $fixturesDir . 'ini/features-skip-invalid-children.ini'],
        ];
    }

    /**
     * @dataProvider generateFeaturesDataProvider
     */
    public function testGenerateFeatures($jsonFile, $expectedIni)
    {
        $generator = new BrowscapIniGenerator();
        $generator->setDataCollection($this->getDataCollection([$jsonFile]));

        $ini = $generator->generate();

        $this->assertStringEqualsFile($expectedIni, $ini);
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
        $generator = new BrowscapIniGenerator();
        $actualType = $generator->getPropertyType($propertyName);
        $this->assertSame($expectedType, $actualType, "Property {$propertyName} should be {$expectedType} (was {$actualType})");
    }

    public function testGetPropertyTypeThrowsExceptionIfPropertyNameNotMapped()
    {
        $generator = new BrowscapIniGenerator();

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
        $generator = new BrowscapIniGenerator();
        $actualValue = $generator->isExtraProperty($propertyName);
        $this->assertSame($isExtra, $actualValue);
    }
}
