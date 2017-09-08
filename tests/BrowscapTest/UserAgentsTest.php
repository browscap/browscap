<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   BrowscapTest
 * @copyright  1998-2017 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest;

use Browscap\Coverage\Processor;
use Browscap\Data\PropertyHolder;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer\Factory\PhpWriterFactory;
use BrowscapPHP\Browscap;
use BrowscapPHP\BrowscapUpdater;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use WurflCache\Adapter\File;

/**
 * Class UserAgentsTest
 *
 * @category   BrowscapTest
 * @author     James Titcumb <james@asgrim.com>
 * @group      useragenttest
 */
class UserAgentsTest extends \PHPUnit\Framework\TestCase
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
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
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
            self::$buildFolder
        );

        $writerCollectionFactory = new PhpWriterFactory();
        $writerCollection        = $writerCollectionFactory->createCollection($logger, self::$buildFolder);

        $buildGenerator
            ->setLogger($logger)
            ->setCollectionCreator(new CollectionCreator())
            ->setWriterCollection($writerCollection);

        $buildGenerator->setCollectPatternIds(true);

        $buildGenerator->run($buildNumber, false);

        $cache = new File([File::DIR => $cacheFolder]);

        self::$browscap = new Browscap();
        self::$browscap
            ->setCache($cache)
            ->setLogger($logger);

        self::$browscapUpdater = new BrowscapUpdater();
        self::$browscapUpdater
            ->setCache($cache)
            ->setLogger($logger);

        self::$propertyHolder = new PropertyHolder();
    }

    /**
     * Runs after the entire test suite is run.  Generates a coverage report for JSON resource files if
     * the $coveredPatterns array isn't empty
     */
    public static function tearDownAfterClass()
    {
        if (!empty(self::$coveredPatterns)) {
            $coverageProcessor = new Processor(__DIR__ . '/../../resources/user-agents/');
            $coverageProcessor->process(self::$coveredPatterns);
            $coverageProcessor->write(__DIR__ . '/../../coverage.json');
        }
    }

    /**
     * @return array[]
     */
    private function userAgentDataProvider()
    {
        static $data = [];

        if (count($data)) {
            return $data;
        }

        $checks          = [];
        $sourceDirectory = __DIR__ . '/../issues/';
        $iterator        = new \RecursiveDirectoryIterator($sourceDirectory);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $tests = require_once $file->getPathname();

            foreach ($tests as $key => $test) {
                if (isset($data[$key])) {
                    throw new \RuntimeException('Test data is duplicated for key "' . $key . '"');
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
     * @return array[]
     */
    public function userAgentDataProviderFull()
    {
        return $this->userAgentDataProvider();
    }

    /**
     * @return array[]
     */
    public function userAgentDataProviderStandard()
    {
        return array_filter(
            $this->userAgentDataProvider(),
            function ($test) {
                return (isset($test['standard']) && $test['standard']);
            }
        );
    }

    /**
     * @return array[]
     */
    public function userAgentDataProviderLite()
    {
        return array_filter(
            $this->userAgentDataProvider(),
            function ($test) {
                return (isset($test['lite']) && $test['lite'] && isset($test['standard']) && $test['standard']);
            }
        );
    }

    /**
     * @dataProvider userAgentDataProviderFull
     * @coversNothing
     *
     * @param string $userAgent
     * @param array  $expectedProperties
     *
     * @throws \Exception
     * @throws \BrowscapPHP\Exception
     * @group  integration
     * @group  useragenttest
     * @group  full
     */
    public function testUserAgentsFull($userAgent, $expectedProperties)
    {
        if (!is_array($expectedProperties) || !count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        static $updatedFullCache = false;

        if (!$updatedFullCache) {
            self::$browscapUpdater->getCache()->flush();
            self::$browscapUpdater->convertFile(self::$buildFolder . '/full_php_browscap.ini');
            $updatedFullCache = true;
        }

        $actualProps = (array) self::$browscap->getBrowser($userAgent);

        self::$coveredPatterns[] = $actualProps['patternid'];

        foreach ($expectedProperties as $propName => $propValue) {
            if (!self::$propertyHolder->isOutputProperty($propName)) {
                continue;
            }

            $propName = strtolower($propName);

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

    /**
     * @dataProvider userAgentDataProviderStandard
     * @coversNothing
     *
     * @param string $userAgent
     * @param array  $expectedProperties
     *
     * @throws \Exception
     * @throws \BrowscapPHP\Exception
     * @group  integration
     * @group  useragenttest
     * @group  standard
     */
    public function testUserAgentsStandard($userAgent, $expectedProperties)
    {
        if (!is_array($expectedProperties) || !count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        static $updatedStandardCache = false;

        if (!$updatedStandardCache) {
            self::$browscapUpdater->getCache()->flush();
            self::$browscapUpdater->convertFile(self::$buildFolder . '/php_browscap.ini');
            $updatedStandardCache = true;
        }

        $actualProps = (array) self::$browscap->getBrowser($userAgent);

        foreach ($expectedProperties as $propName => $propValue) {
            if (!self::$propertyHolder->isOutputProperty($propName)) {
                continue;
            }

            if (!self::$propertyHolder->isStandardModeProperty($propName)) {
                continue;
            }

            $propName = strtolower($propName);

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

    /**
     * @dataProvider userAgentDataProviderLite
     * @coversNothing
     *
     * @param string $userAgent
     * @param array  $expectedProperties
     *
     * @throws \Exception
     * @throws \BrowscapPHP\Exception
     *
     * @group intergration
     * @group useragenttest
     * @group lite
     */
    public function testUserAgentsLite($userAgent, $expectedProperties)
    {
        if (!is_array($expectedProperties) || !count($expectedProperties)) {
            self::markTestSkipped('Could not run test - no properties were defined to test');
        }

        static $updatedLiteCache = false;

        if (!$updatedLiteCache) {
            self::$browscapUpdater->getCache()->flush();
            self::$browscapUpdater->convertFile(self::$buildFolder . '/lite_php_browscap.ini');
            $updatedLiteCache = true;
        }

        $actualProps = (array) self::$browscap->getBrowser($userAgent);

        foreach ($expectedProperties as $propName => $propValue) {
            if (!self::$propertyHolder->isOutputProperty($propName)) {
                continue;
            }

            if (!self::$propertyHolder->isLiteModeProperty($propName)) {
                continue;
            }

            $propName = strtolower($propName);

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
