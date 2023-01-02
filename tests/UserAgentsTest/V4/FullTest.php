<?php

declare(strict_types=1);

namespace UserAgentsTest\V4;

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
use Exception;
use JsonException;
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;
use MatthiasMullie\Scrapbook\Psr16\SimpleCache;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Throwable;

use function assert;
use function count;
use function file_exists;
use function implode;
use function is_string;
use function mkdir;
use function sprintf;
use function time;

use const PHP_EOL;

class FullTest extends TestCase
{
    private static Browscap $browscap;

    private static PropertyHolder $propertyHolder;

    /** @var array<string> */
    private static array $coveredPatterns = [];

    private static FilterInterface $filter;

    private static WriterInterface $writer;

    /** @throws void */
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
            $logger = new class extends AbstractLogger {
                /**
                 * @param mixed   $level
                 * @param string  $message
                 * @param mixed[] $context
                 *
                 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
                 */
                public function log($level, $message, array $context = []): void
                {
                    assert(is_string($level));

                    echo '[', $level, '] ', $message, PHP_EOL;
                }

                /**
                 * @param string  $message
                 * @param mixed[] $context
                 *
                 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
                 */
                public function debug($message, array $context = []): void
                {
                    // do nothing here
                }

                /**
                 * @param string  $message
                 * @param mixed[] $context
                 *
                 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
                 */
                public function info($message, array $context = []): void
                {
                    // do nothing here
                }
            };

            self::$propertyHolder = new PropertyHolder();
            self::$filter         = new FullFilter(self::$propertyHolder);
            self::$writer         = new IniWriter($buildFolder . '/full_php_browscap.ini', $logger);

            $formatter = new PhpFormatter(self::$propertyHolder);
            self::$writer->setFormatter($formatter);
            self::$writer->setFilter(self::$filter);

            $writerCollection = new WriterCollection();
            $writerCollection->addWriter(self::$writer);

            $dataCollectionFactory = new DataCollectionFactory($logger);

            $buildGenerator = new BuildGenerator(
                $resourceFolder,
                $buildFolder,
                $logger,
                $writerCollection,
                $dataCollectionFactory,
            );

            $buildGenerator->setCollectPatternIds(true);
            $buildGenerator->run($version, new DateTimeImmutable(), false);

            $cache = new SimpleCache(
                new MemoryStore(),
            );
            $cache->clear();

            $resultFormatter = new LegacyFormatter();

            self::$browscap = new Browscap($cache, $logger);
            self::$browscap->setFormatter($resultFormatter);

            $updater = new BrowscapUpdater($cache, $logger);
            $updater->convertFile($buildFolder . '/full_php_browscap.ini');
        } catch (Throwable $e) {
            echo sprintf(
                'Browscap ini file could not be built in %s test class, there was an uncaught exception: %s (%s)' . PHP_EOL,
                self::class,
                $e::class,
                $e->getMessage(),
            );

            exit(1);
        }
    }

    /**
     * Runs after the entire test suite is run.  Generates a coverage report for JSON resource files if
     * the $coveredPatterns array isn't empty
     *
     * @throws JsonException
     * @throws RuntimeException
     * @throws DirectoryNotFoundException
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
     * @return array<string, array<string, bool|int|string>|bool|string>
     * @phpstan-return array<string, array{ua: string, properties: array<string, string|int|bool>, lite: bool, standard: bool, full: bool}>
     *
     * @throws RuntimeException
     */
    public function userAgentDataProvider(): array
    {
        [$data, $errors] = (new IteratorHelper())->getTestFiles(new NullLogger(), 'full');

        if (! empty($errors)) {
            throw new RuntimeException(
                'Errors occured while collecting test files' . PHP_EOL . implode(PHP_EOL, $errors),
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
                . '; used pattern: "' . $actualProps['browser_name_pattern'] . '")',
            );

            static::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual result does not have "' . $propName . '" property'
                . '; used pattern: "' . $actualProps['browser_name_pattern'] . '")',
            );

            static::assertSame(
                $propValue,
                $actualProps[$propName],
                'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName]
                . '"; used pattern: "' . $actualProps['browser_name_pattern'] . '")',
            );
        }
    }
}
