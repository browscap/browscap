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

use Browscap\Coverage\Processor;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\LiteFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use BrowscapPHP\Browscap;
use BrowscapPHP\BrowscapUpdater;
use BrowscapPHP\Formatter\LegacyFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use WurflCache\Adapter\File;

/**
 * @group      useragenttest
 * @group      lite
 */
class LiteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \BrowscapPHP\Browscap
     */
    private static $browscap = null;

    /**
     * @var \BrowscapPHP\BrowscapUpdater
     */
    private static $browscapUpdater = null;

    /**
     * @var string
     */
    private static $buildFolder = null;

    /**
     * @var \Browscap\Data\PropertyHolder
     */
    private static $propertyHolder = null;

    /**
     * @var string[]
     */
    private static $coveredPatterns = [];

    /**
     * @var \Browscap\Filter\LiteFilter
     */
    private static $filter = null;

    public static function setUpBeforeClass() : void
    {
        // First, generate the INI files
        $buildNumber    = time();
        $resourceFolder = __DIR__ . '/../../resources/';

        self::$buildFolder = __DIR__ . '/../../build/browscap-ua-test-' . $buildNumber . '/build/';
        $cacheFolder       = __DIR__ . '/../../build/browscap-ua-test-' . $buildNumber . '/cache/';

        // create build folder if it does not exist
        if (!file_exists(self::$buildFolder)) {
            mkdir(self::$buildFolder, 0777, true);
        }
        if (!file_exists($cacheFolder)) {
            mkdir($cacheFolder, 0777, true);
        }

        $logger = new Logger('browscap');
        $logger->pushHandler(new NullHandler(Logger::DEBUG));

        $buildGenerator = new BuildGenerator(
            $resourceFolder,
            self::$buildFolder,
            $logger
        );

        $writerCollection = new WriterCollection();

        self::$propertyHolder = new PropertyHolder();
        self::$filter         = new LiteFilter(self::$propertyHolder);

        $litePhpWriter = new IniWriter(self::$buildFolder . '/lite_php_browscap.ini', $logger);
        $formatter     = new PhpFormatter();
        $formatter->setFilter(self::$filter);
        $litePhpWriter->setFormatter($formatter);
        $litePhpWriter->setFilter(self::$filter);
        $writerCollection->addWriter($litePhpWriter);

        $buildGenerator->setCollectionCreator(new CollectionCreator($logger));
        $buildGenerator->setWriterCollection($writerCollection);
        $buildGenerator->setCollectPatternIds(true);

        $buildGenerator->run((string) $buildNumber, false);

        $cache = new File([File::DIR => $cacheFolder]);
        $cache->flush();

        $resultFormatter = new LegacyFormatter();

        self::$browscap = new Browscap();
        self::$browscap
            ->setCache($cache)
            ->setLogger($logger)
            ->setFormatter($resultFormatter);

        self::$browscapUpdater = new BrowscapUpdater();
        self::$browscapUpdater
            ->setCache($cache)
            ->setLogger($logger)
            ->convertFile(self::$buildFolder . '/lite_php_browscap.ini');
    }

    /**
     * Runs after the entire test suite is run.  Generates a coverage report for JSON resource files if
     * the $coveredPatterns array isn't empty
     */
    public static function tearDownAfterClass() : void
    {
        if (!empty(self::$coveredPatterns)) {
            $coverageProcessor = new Processor(__DIR__ . '/../../resources/user-agents/');
            $coverageProcessor->process(self::$coveredPatterns);
            $coverageProcessor->write(__DIR__ . '/../../coverage-lite.json');
        }
    }

    /**
     * @return array[]
     */
    public function userAgentDataProvider()
    {
        static $data = [];

        if (count($data)) {
            return $data;
        }

        $sourceDirectory = __DIR__ . '/../issues/';
        $iterator        = new \RecursiveDirectoryIterator($sourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || 'php' !== $file->getExtension()) {
                continue;
            }

            $tests = require_once $file->getPathname();

            foreach ($tests as $key => $test) {
                if (!array_key_exists('lite', $test)) {
                    throw new \RuntimeException(
                        '"lite" keyword is missing for  key "' . $key . '"'
                    );
                }

                if (!$test['lite']) {
                    continue;
                }

                $data[$key] = $test;
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
    public function testUserAgents($userAgent, $expectedProperties) : void
    {
        if (!is_array($expectedProperties) || !count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        $actualProps = (array) self::$browscap->getBrowser($userAgent);

        if (isset($actualProps['PatternId'])) {
            self::$coveredPatterns[] = $actualProps['PatternId'];
        }

        foreach ($expectedProperties as $propName => $propValue) {
            if (!self::$filter->isOutputProperty($propName)) {
                continue;
            }

            self::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual result does not have "' . $propName . '" property'
            );

            self::assertSame(
                $propValue,
                $actualProps[$propName],
                'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName]
                . '"; used pattern: ' . $actualProps['browser_name_pattern'] . ')'
            );
        }
    }
}
