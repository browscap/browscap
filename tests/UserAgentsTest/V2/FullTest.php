<?php
declare(strict_types = 1);
namespace UserAgentsTest\V2;

use Browscap\Coverage\Processor;
use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\FullFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\IteratorHelper;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterCollection;
use phpbrowscap\Browscap;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class FullTest extends TestCase
{
    /**
     * @var \phpbrowscap\Browscap
     */
    private static $browscap = null;

    /**
     * @var string
     */
    private static $buildFolder = null;

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

    public static function setUpBeforeClass() : void
    {
        // First, generate the INI files
        $buildNumber    = time();
        $resourceFolder = __DIR__ . '/../../../resources/';
        $buildFolder    = __DIR__ . '/../../../build/browscap-ua-test-full2-';
        $cacheFolder    = __DIR__ . '/../../../build/browscap-ua-test-full2-' . $buildNumber . '/cache/';

        // create build folder if it does not exist
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
        $buildGenerator->run($version, false);

        // Now, load an INI file into phpbrowscap\Browscap for testing the UAs
        self::$browscap            = new Browscap($cacheFolder);
        self::$browscap->lowercase = true;
        self::$browscap->localFile = $buildFolder . '/full_php_browscap.ini';
        self::$browscap->updateCache();

        self::$propertyHolder = new PropertyHolder();
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
            $coverageProcessor->write(__DIR__ . '/../../../coverage-full2.json');
        }
    }

    /**
     * @return array
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
     */
    public function testUserAgents(string $userAgent, array $expectedProperties) : void
    {
        if (!count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        $actualProps = self::$browscap->getBrowser($userAgent, true);

        if (isset($actualProps['PatternId'])) {
            self::$coveredPatterns[] = $actualProps['PatternId'];
        }

        foreach (array_keys($expectedProperties) as $propName) {
            if (!self::$filter->isOutputProperty($propName, self::$writer)) {
                continue;
            }

            self::assertFalse(
                self::$propertyHolder->isDeprecatedProperty($propName),
                'Actual result expects to test for deprecated property "' . $propName . '"'
            );

            $expectedValue = $expectedProperties[$propName];
            $propName      = mb_strtolower($propName);

            self::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual result does not have "' . $propName . '" property'
            );

            $libValue = $actualProps[$propName];
            $message  = 'Expected actual "' . $propName . '" to be "' . $expectedValue . '" '
                . '(was "' . $libValue . '")' . PHP_EOL
                . '(Full Result: "' . var_export($actualProps, true) . '")' . PHP_EOL;

            self::assertSame(
                $expectedValue,
                $libValue,
                $message
            );
        }
    }
}
