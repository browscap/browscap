<?php
declare(strict_types = 1);
namespace UserAgentsTest\Native;

use Browscap\Coverage\Processor;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\FullFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Helper\IteratorHelper;
use Browscap\Writer\IniWriter;
use BrowscapPHP\Browscap;
use BrowscapPHP\BrowscapUpdater;
use BrowscapPHP\Formatter\LegacyFormatter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use WurflCache\Adapter\File;

class FullTest extends TestCase
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
     * @throws \BrowscapPHP\Exception
     */
    public static function setUpBeforeClass() : void
    {
        $objectIniPath = ini_get('browscap');

        echo 'using browscap.ini file: ', $objectIniPath, PHP_EOL;

        if (!is_file($objectIniPath)) {
            self::markTestSkipped('browscap not defined in php.ini');
        }

        $buildFolder = __DIR__ . '/../../../build/build/';
        $cacheFolder = __DIR__ . '/../../../build/cache/';

        // create build folder if it does not exist
        if (!file_exists($buildFolder)) {
            mkdir($buildFolder, 0777, true);
        }
        if (!file_exists($cacheFolder)) {
            mkdir($cacheFolder, 0777, true);
        }

        $logger = new NullLogger();

        self::$propertyHolder = new PropertyHolder();
        self::$filter         = new FullFilter(self::$propertyHolder);
        self::$writer         = new IniWriter($buildFolder . '/full_php_browscap.ini', $logger);
        $formatter            = new PhpFormatter(self::$propertyHolder);
        self::$writer->setFormatter($formatter);
        self::$writer->setFilter(self::$filter);

        $cache = new File([File::DIR => $cacheFolder]);
        $cache->flush();

        $resultFormatter = new LegacyFormatter(['lowercase' => true]);

        self::$browscap = new Browscap();
        self::$browscap
            ->setCache($cache)
            ->setLogger($logger)
            ->setFormatter($resultFormatter);

        self::$browscapUpdater = new BrowscapUpdater();
        self::$browscapUpdater
            ->setCache($cache)
            ->setLogger($logger)
            ->convertFile($objectIniPath);
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
            $coverageProcessor->write(__DIR__ . '/../../../coverage-full-native.json');
        }
    }

    /**
     * @return array[]
     */
    public function userAgentDataProvider() : array
    {
        [$data, $errors] = (new IteratorHelper())->getTestFiles(new NullLogger(), 'full');

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

        $bcResult  = self::$browscap->getBrowser($userAgent);
        $libResult = (object) get_browser($userAgent, false);

        if (isset($libResult->{'PatternId'})) {
            self::$coveredPatterns[] = $libResult->{'PatternId'};
        }

        foreach (array_keys($expectedProperties) as $propName) {
            if (!self::$filter->isOutputProperty($propName, self::$writer)) {
                continue;
            }

            self::assertFalse(
                self::$propertyHolder->isDeprecatedProperty($propName),
                'Actual result expects to test for deprecated property "' . $propName . '"'
            );

            $expectedValue = (string) $expectedProperties[$propName];
            $propName      = mb_strtolower($propName);

            self::assertObjectHasAttribute(
                $propName,
                $libResult,
                'Actual native result does not have "' . $propName . '" property'
            );

            self::assertObjectHasAttribute(
                $propName,
                $bcResult,
                'Actual browscap result does not have "' . $propName . '" property'
            );

            $libValue = (string) $libResult->{$propName};
            $message  = 'Expected actual "' . $propName . '" to be "' . $expectedValue . '" '
                . '(was "' . $libValue . '") [native]' . PHP_EOL
                . '(Full Result: "' . var_export($libResult, true) . '")' . PHP_EOL;

            if (null !== $bcResult->browser_name_pattern && null !== $libResult->browser_name_pattern) {
                $message .= 'expected pattern [\BrowscapPHP\Browscap::getBrowser()]:' . mb_strtolower($bcResult->browser_name_pattern) . PHP_EOL
                    . 'used pattern [get_browser]:                            ' . mb_strtolower($libResult->browser_name_pattern) . PHP_EOL;
            }

            self::assertSame(
                $expectedValue,
                $libValue,
                $message
            );
        }
    }
}
