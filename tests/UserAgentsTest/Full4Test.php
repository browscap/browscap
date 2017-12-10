<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace UserAgentsTest;

use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\FullFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Generator\BuildGenerator;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use BrowscapPHP\Browscap;
use BrowscapPHP\BrowscapUpdater;
use BrowscapPHP\Formatter\LegacyFormatter;
use Doctrine\Common\Cache\ArrayCache;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Roave\DoctrineSimpleCache\SimpleCacheAdapter;

/**
 * @group      useragenttest
 * @group      full
 */
class Full4Test extends TestCase
{
    /**
     * @var \BrowscapPHP\Browscap
     */
    private static $browscap = null;

    /**
     * @var \Browscap\Data\PropertyHolder
     */
    private static $propertyHolder = null;

    /**
     * @var \Browscap\Filter\FullFilter
     */
    private static $filter = null;

    /**
     * @var \Browscap\Writer\WriterInterface
     */
    private static $writer;

    public static function setUpBeforeClass() : void
    {
        // First, generate the INI files
        $buildNumber    = time();
        $resourceFolder = __DIR__ . '/../../resources/';
        $buildFolder    = __DIR__ . '/../../build/browscap-ua-test-full4-' . $buildNumber . '/build/';
        $cacheFolder    = __DIR__ . '/../../build/browscap-ua-test-full4-' . $buildNumber . '/cache/';

        // create folders if it does not exist
        if (!file_exists($buildFolder)) {
            mkdir($buildFolder, 0777, true);
        }
        if (!file_exists($cacheFolder)) {
            mkdir($cacheFolder, 0777, true);
        }

        $version = (string) $buildNumber;

        $logger               = new NullLogger();
        $writerCollection     = new WriterCollection();
        self::$propertyHolder = new PropertyHolder();
        self::$filter         = new FullFilter(self::$propertyHolder);
        $formatter            = new PhpFormatter(self::$propertyHolder);
        self::$writer         = new IniWriter($buildFolder . '/full_php_browscap.ini', $logger);
        self::$writer->setFormatter($formatter);
        self::$writer->setFilter(self::$filter);
        $writerCollection->addWriter(self::$writer);

        $dataCollectionFactory = new DataCollectionFactory($logger);

        $buildGenerator = new BuildGenerator(
            $resourceFolder,
            $buildFolder,
            $logger,
            $writerCollection,
            $dataCollectionFactory
        );

        $buildGenerator->setCollectPatternIds(true);
        $buildGenerator->run($version, false);

        $memoryCache = new ArrayCache();
        $cache       = new SimpleCacheAdapter($memoryCache);
        $cache->clear();

        $resultFormatter = new LegacyFormatter();

        self::$browscap = new Browscap($cache, $logger);
        self::$browscap->setFormatter($resultFormatter);

        $updater = new BrowscapUpdater($cache, $logger);
        $updater->convertFile($buildFolder . '/full_php_browscap.ini');
    }

    /**
     * @return array[]
     */
    public function userAgentDataProvider() : array
    {
        $data = [];

        $checks          = [];
        $sourceDirectory = __DIR__ . '/../issues/';
        $iterator        = new \RecursiveDirectoryIterator($sourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || 'php' !== $file->getExtension()) {
                continue;
            }

            $tests = require_once $file->getPathname();

            foreach ($tests as $key => $test) {
                if (isset($data[$key])) {
                    throw new \RuntimeException('Test data is duplicated for key "' . $key . '"');
                }

                if (!array_key_exists('full', $test)) {
                    throw new \RuntimeException(
                        '"full" keyword is missing for  key "' . $key . '"'
                    );
                }

                if (!$test['full']) {
                    continue;
                }

                if (isset($checks[$test['ua']])) {
                    throw new \RuntimeException(
                        'UA "' . $test['ua'] . '" added more than once, now for key "' . $key . '", before for key "'
                        . $checks[$test['ua']] . '"'
                    );
                }

                $data[$key]          = $test;
                $checks[$test['ua']] = $key;
            }
        }

        return $data;
    }

    /**
     * @dataProvider userAgentDataProvider
     * @coversNothing
     *
     * @param string $userAgent
     * @param array  $expectedProperties
     *
     * @throws \Exception
     * @throws \BrowscapPHP\Exception
     */
    public function testUserAgents(string $userAgent, array $expectedProperties) : void
    {
        if (!count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        $actualProps = (array) self::$browscap->getBrowser($userAgent);

        foreach ($expectedProperties as $propName => $propValue) {
            if (!self::$filter->isOutputProperty($propName, self::$writer)) {
                continue;
            }

            self::assertFalse(
                self::$propertyHolder->isDeprecatedProperty($propName),
                'Actual result expects to test for deprecated property "' . $propName . '"'
            );

            self::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual result does not have "' . $propName . '" property'
            );

            self::assertSame(
                $propValue,
                $actualProps[$propName],
                'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName]
                . '"; used pattern: "' . $actualProps['browser_name_pattern'] . '")'
            );
        }
    }
}
