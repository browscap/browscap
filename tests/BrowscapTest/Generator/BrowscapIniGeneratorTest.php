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
            $dir . '/test4.json',
            $dir . '/default-browser.json',
        ];
    }

    /**
     * @return \Browscap\Generator\DataCollection
     */
    private function getDataCollection()
    {
        $dataCollection = new DataCollection('1234');
        $dataCollection->addPlatformsFile($this->getPlatformsJsonFixture());

        $dateProperty = new \ReflectionProperty(get_class($dataCollection), 'generationDate');
        $dateProperty->setAccessible(true);
        $dateProperty->setValue($dataCollection, new \DateTime('2010-12-31 12:34:56'));

        $files = $this->getUserAgentFixtures();
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

    public function testIsExtraProperty()
    {
    	$this->markTestIncomplete();
    }

    public function testGetPropertyType()
    {
        $this->markTestIncomplete();
    }

    public function generateDataProvider()
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
     * @dataProvider generateDataProvider
     */
    public function testGenerate($filename, $quoteStringProperties, $includeExtraProperties, $liteOnly)
    {
        $generator = new BrowscapIniGenerator();
        $generator->setDataCollection($this->getDataCollection());
        $generator->setOptions($quoteStringProperties, $includeExtraProperties, $liteOnly);

        $ini = $generator->generate();

        $expectedFilename = __DIR__ . '/../../fixtures/ini/' . $filename;

        #file_put_contents($expectedFilename, $ini);
        $this->assertStringEqualsFile($expectedFilename, $ini);
    }
}
