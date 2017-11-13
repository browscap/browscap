<?php
declare(strict_types = 1);
namespace UserAgentsTest;

use Browscap\Coverage\Processor;
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
use Psr\Log\NullLogger;
use WurflCache\Adapter\File;

class FullTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \BrowscapPHP\Browscap
     */
    private static $browscap;

    /**
     * @var \BrowscapPHP\BrowscapUpdater
     */
    private static $browscapUpdater;

    /**
     * @var string
     */
    private static $buildFolder;

    /**
     * @var \Browscap\Data\PropertyHolder
     */
    private static $propertyHolder;

    /**
     * @var string[]
     */
    private static $coveredPatterns = [];

    /**
     * @var \Browscap\Filter\FilterInterface
     */
    private static $filter;

    /**
     * @var \Browscap\Writer\WriterInterface
     */
    private static $writer;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public static function setUpBeforeClass() : void
    {
        // First, generate the INI files
        $buildNumber    = time();
        $resourceFolder = __DIR__ . '/../../resources/';

        self::$buildFolder = __DIR__ . '/../../build/browscap-ua-test-full-' . $buildNumber . '/build/';
        $cacheFolder       = __DIR__ . '/../../build/browscap-ua-test-full-' . $buildNumber . '/cache/';

        // create build folder if it does not exist
        if (!file_exists(self::$buildFolder)) {
            mkdir(self::$buildFolder, 0777, true);
        }
        if (!file_exists($cacheFolder)) {
            mkdir($cacheFolder, 0777, true);
        }

        $logger = new NullLogger();

        $version = (string) $buildNumber;

        $writerCollection = new WriterCollection();

        self::$propertyHolder = new PropertyHolder();
        self::$filter         = new FullFilter(self::$propertyHolder);
        self::$writer         = new IniWriter(self::$buildFolder . '/full_php_browscap.ini', $logger);
        $formatter            = new PhpFormatter(self::$propertyHolder);
        self::$writer->setFormatter($formatter);
        self::$writer->setFilter(self::$filter);
        $writerCollection->addWriter(self::$writer);

        $dataCollectionFactory = new DataCollectionFactory($logger);

        $buildGenerator = new BuildGenerator(
            $resourceFolder,
            self::$buildFolder,
            $logger,
            $writerCollection,
            $dataCollectionFactory
        );

        $buildGenerator->setCollectPatternIds(true);
        $buildGenerator->run($version, false);

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
            ->convertFile(self::$buildFolder . '/full_php_browscap.ini');
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
            $coverageProcessor->write(__DIR__ . '/../../coverage-full.json');
        }
    }

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

            $tests = require $file->getPathname();

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

        if (isset($actualProps['PatternId'])) {
            self::$coveredPatterns[] = $actualProps['PatternId'];
        }

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
