<?php
declare(strict_types = 1);
namespace UserAgentsTest\V4;

use Browscap\Coverage\Processor;
use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\LiteFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\IteratorHelper;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use BrowscapPHP\Browscap;
use BrowscapPHP\BrowscapUpdater;
use BrowscapPHP\Formatter\LegacyFormatter;
use Doctrine\Common\Cache\ArrayCache;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Roave\DoctrineSimpleCache\SimpleCacheAdapter;

class LiteTest extends TestCase
{
    /**
     * @var \BrowscapPHP\Browscap
     */
    private static $browscap;

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
     * @throws \Exception
     * @throws \Assert\AssertionFailedException
     */
    public static function setUpBeforeClass() : void
    {
        // First, generate the INI files
        $buildNumber    = time();
        $resourceFolder = __DIR__ . '/../../../resources/';
        $buildFolder    = __DIR__ . '/../../../build/browscap-ua-test-lite4-' . $buildNumber . '/build/';

        // create folders if it does not exist
        if (!file_exists($buildFolder)) {
            mkdir($buildFolder, 0777, true);
        }

        $version = (string) $buildNumber;

        try {
            $logger               = new NullLogger();
            $writerCollection     = new WriterCollection();
            self::$propertyHolder = new PropertyHolder();
            self::$filter         = new LiteFilter(self::$propertyHolder);
            self::$writer         = new IniWriter($buildFolder . '/lite_php_browscap.ini', $logger);
            $formatter            = new PhpFormatter(self::$propertyHolder);
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
            $updater->convertFile($buildFolder . '/lite_php_browscap.ini');
        } catch (\Exception $e) {
            die(sprintf(
                'Browscap ini file could not be built in %s test class, there was an uncaught exception: %s (%s)' . PHP_EOL,
                __CLASS__,
                get_class($e),
                $e->getMessage()
            ));
        }
    }

    /**
     * Runs after the entire test suite is run.  Generates a coverage report for JSON resource files if
     * the $coveredPatterns array isn't empty
     */
    public static function tearDownAfterClass() : void
    {
        if (!empty(self::$coveredPatterns)) {
            $coverageProcessor = new Processor(__DIR__ . '/../../../resources/user-agents/');
            $coverageProcessor->process(self::$coveredPatterns);
            $coverageProcessor->write(__DIR__ . '/../../../coverage-lite4.json');
        }
    }

    /**
     * @throws \RuntimeException
     *
     * @return array
     */
    public function userAgentDataProvider() : array
    {
        [$data, $errors] = (new IteratorHelper())->getTestFiles(new NullLogger(), 'lite');

        if (!empty($errors)) {
            throw new \RuntimeException(
                'Errors occured while collecting test files' . PHP_EOL . implode(PHP_EOL, $errors)
            );
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
