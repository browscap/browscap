<?php

namespace BrowscapTest\Generator;

use Browscap\Generator\BrowscapIniGenerator;
use Browscap\Generator\BuildGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Generator\DataCollection;

/**
 * Class BrowscapIniGeneratorTest
 *
 * @package BrowscapTest\Generator
 */
class BrowscapIniGeneratorTest extends \PHPUnit_Framework_TestCase
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

        foreach ($files as $file) {
            $dataCollection->addSourceFile($file);
        }

        return $dataCollection;
    }

    public function testgetCollectionDataThrowsExceptionIfDataCollectionNotSet()
    {
        $generator = new BrowscapIniGenerator();

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

        $generator = new BrowscapIniGenerator();
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

        $generator = new BrowscapIniGenerator();
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
        ;

        self::assertSame($collectionData, $generator->getCollectionData());
    }

    public function generateFormatsDataProvider()
    {
        return [
            'asp_full' => ['full_asp_browscap.ini', BuildGenerator::OUTPUT_FORMAT_ASP, BuildGenerator::OUTPUT_TYPE_FULL],
            'php_full' => ['full_php_browscap.ini', BuildGenerator::OUTPUT_FORMAT_PHP, BuildGenerator::OUTPUT_TYPE_FULL],
            'asp_std' => ['browscap.ini', BuildGenerator::OUTPUT_FORMAT_ASP, BuildGenerator::OUTPUT_TYPE_DEFAULT],
            'php_std' => ['php_browscap.ini', BuildGenerator::OUTPUT_FORMAT_PHP, BuildGenerator::OUTPUT_TYPE_DEFAULT],
            'asp_lite' => ['lite_asp_browscap.ini', BuildGenerator::OUTPUT_FORMAT_ASP, BuildGenerator::OUTPUT_TYPE_LITE],
            'php_lite' => ['lite_php_browscap.ini', BuildGenerator::OUTPUT_FORMAT_PHP, BuildGenerator::OUTPUT_TYPE_LITE],
        ];
    }

    /**
     * @dataProvider generateFormatsDataProvider
     */
    public function testGenerateWithDifferentFormattingOptions($filename, $format, $type)
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

        $generator = new BrowscapIniGenerator();
        $generator
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $ini = $generator->generate($format, $type);

        $expectedFilename = __DIR__ . '/../../fixtures/ini/' . $filename;

        self::assertStringEqualsFile($expectedFilename, $ini);
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

        $generator = new BrowscapIniGenerator();
        $generator
            ->setLogger($this->logger)
            ->setCollectionData($collectionData)
            ->setComments($comments)
            ->setVersionData(array('version' => '1234', 'released' => 'Fri, 31 Dec 2010 12:34:56 +0000'))
        ;

        $ini = $generator->generate(BuildGenerator::OUTPUT_FORMAT_ASP, BuildGenerator::OUTPUT_TYPE_FULL);

        self::assertStringEqualsFile($expectedIni, $ini);
    }
}
