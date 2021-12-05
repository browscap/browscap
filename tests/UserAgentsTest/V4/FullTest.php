<?php

declare(strict_types=1);

namespace UserAgentsTest\V4;

use Assert\AssertionFailedException;
use Browscap\Coverage\Processor;
use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\FilterInterface;
use Browscap\Filter\FullFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\IteratorHelper;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use Browscap\Writer\WriterInterface;
use BrowscapPHP\Browscap;
use BrowscapPHP\BrowscapUpdater;
use BrowscapPHP\Formatter\LegacyFormatter;
use DateTimeImmutable;
use Doctrine\Common\Cache\ArrayCache;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\NullLogger;
use Roave\DoctrineSimpleCache\SimpleCacheAdapter;
use RuntimeException;
use Throwable;

use function count;
use function file_exists;
use function get_class;
use function implode;
use function mkdir;
use function sprintf;
use function time;

use const PHP_EOL;

class FullTest extends TestCase
{
    /** @var Browscap */
    private static $browscap;

    /** @var PropertyHolder */
    private static $propertyHolder;

    /** @var array<string> */
    private static $coveredPatterns = [];

    /** @var FilterInterface */
    private static $filter;

    /** @var WriterInterface */
    private static $writer;

    /**
     * @throws Exception
     * @throws AssertionFailedException
     */
    public static function setUpBeforeClass(): void
    {
        // First, generate the INI files
        $buildNumber    = time();
        $resourceFolder = __DIR__ . '/../../../resources/';
        $buildFolder    = __DIR__ . '/../../../build/browscap-ua-test-full4-' . $buildNumber . '/build/';

        // create folders if it does not exist
        if (! file_exists($buildFolder)) {
            mkdir($buildFolder, 0777, true);
        }

        $version = (string) $buildNumber;

        try {
            $logger               = new class extends AbstractLogger {

                public function log($level, $message, array $context = array())
                {
                    echo '[', $level, '] ', $message, PHP_EOL;
                }

                public function info($message, array $context = array())
                {
                    // do nothing
                }

                public function debug($message, array $context = array())
                {
                    // do nothing
                }
            };
            $writerCollection     = new WriterCollection();
            self::$propertyHolder = new PropertyHolder();
            self::$filter         = new FullFilter(self::$propertyHolder);
            self::$writer         = new IniWriter($buildFolder . '/full_php_browscap.ini', $logger);
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
            $buildGenerator->run($version, new DateTimeImmutable(), false);

            $memoryCache = new ArrayCache();
            $cache       = new SimpleCacheAdapter($memoryCache);
            $cache->clear();

            $resultFormatter = new LegacyFormatter();

            self::$browscap = new Browscap($cache, $logger);
            self::$browscap->setFormatter($resultFormatter);

            $updater = new BrowscapUpdater($cache, $logger);
            $updater->convertFile($buildFolder . '/full_php_browscap.ini');
        } catch (Throwable $e) {
            exit(sprintf(
                'Browscap ini file could not be built in %s test class, there was an uncaught exception: %s (%s)' . PHP_EOL,
                self::class,
                get_class($e),
                $e->getMessage()
            ));
        }
    }

    /**
     * Runs after the entire test suite is run.  Generates a coverage report for JSON resource files if
     * the $coveredPatterns array isn't empty
     */
    public static function tearDownAfterClass(): void
    {
        if (empty(self::$coveredPatterns)) {
            return;
        }

        $coverageProcessor = new Processor(__DIR__ . '/../../../resources/user-agents/');
        $coverageProcessor->process(self::$coveredPatterns);
        $coverageProcessor->write(__DIR__ . '/../../../coverage-full4.json');
    }

    /**
     * @return array<string>
     *
     * @throws RuntimeException
     */
    public function userAgentDataProvider(): array
    {
        [$data, $errors] = (new IteratorHelper())->getTestFiles(new NullLogger(), 'full');

        if (! empty($errors)) {
            throw new RuntimeException(
                'Errors occured while collecting test files' . PHP_EOL . implode(PHP_EOL, $errors)
            );
        }

        return $data;
    }

    /**
     * @param array<string> $expectedProperties
     *
     * @throws Exception
     * @throws \BrowscapPHP\Exception
     *
     * @dataProvider userAgentDataProvider
     * @coversNothing
     */
    public function testUserAgents(string $userAgent, array $expectedProperties): void
    {
        if (! count($expectedProperties)) {
            static::markTestSkipped('Could not run test - no properties were defined to test');
        }

        $actualProps = (array) self::$browscap->getBrowser($userAgent);

        if (isset($actualProps['PatternId'])) {
            self::$coveredPatterns[] = $actualProps['PatternId'];
        }

        foreach ($expectedProperties as $propName => $propValue) {
            if (! self::$filter->isOutputProperty($propName, self::$writer)) {
                continue;
            }

            static::assertFalse(
                self::$propertyHolder->isDeprecatedProperty($propName),
                'Actual result expects to test for deprecated property "' . $propName . '"'
                . '; used pattern: "' . $actualProps['browser_name_pattern'] . '")'
            );

            static::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual result does not have "' . $propName . '" property'
                . '; used pattern: "' . $actualProps['browser_name_pattern'] . '")'
            );

            static::assertSame(
                $propValue,
                $actualProps[$propName],
                'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName]
                . '"; used pattern: "' . $actualProps['browser_name_pattern'] . '")'
            );
        }
    }
}
