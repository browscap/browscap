<?php

namespace BrowscapTest;

use Browscap\Generator\BrowscapIniGenerator;
use Browscap\Generator\CollectionParser;
use Browscap\Generator\BuildGenerator;
use Browscap\Helper\CollectionCreator;
use Browscap\Helper\Generator;
use Browscap\Helper\LoggerHelper;
use phpbrowscap\Browscap;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Class UserAgentsTest
 *
 * @package BrowscapTest
 */
class UserAgentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \phpbrowscap\Browscap
     */
    protected static $browscap;

    public static function setUpBeforeClass()
    {
        // First, generate the INI files
        $buildNumber = time();

        $resourceFolder = __DIR__ . '/../../resources/';

        $buildFolder = sys_get_temp_dir() . '/browscap-ua-test-' . $buildNumber;
        mkdir($buildFolder, 0777, true);

        $logger = new Logger('browscap');
        $logger->pushHandler(new NullHandler(Logger::DEBUG));

        $collectionCreator = new CollectionCreator();
        $collectionParser = new CollectionParser();
        $generatorHelper = new Generator();

        $collectionCreator = new CollectionCreator();
        $collectionParser = new CollectionParser();
        $iniGenerator = new BrowscapIniGenerator();
        
        $iniFile = $buildFolder . '/full_php_browscap.ini';

        $generatorHelper = new Generator();
        $generatorHelper
            ->setVersion('temporary-version')
            ->setResourceFolder($resourceFolder)
            ->setCollectionCreator($collectionCreator)
            ->setCollectionParser($collectionParser)
            ->createCollection()
            ->parseCollection()
            ->setGenerator($iniGenerator)
        ;

        file_put_contents($iniFile, $generatorHelper->create(BuildGenerator::OUTPUT_FORMAT_PHP, BuildGenerator::OUTPUT_TYPE_FULL));

        // Now, load an INI file into phpbrowscap\Browscap for testing the UAs
        $browscap = new Browscap($buildFolder);
        $browscap->localFile = $iniFile;

        self::$browscap = $browscap;
    }

    public function userAgentDataProvider()
    {
        $data = require_once __DIR__ . '/../fixtures/TestUserAgents.php';

        return $data;
    }

    /**
     * @dataProvider userAgentDataProvider
     * @coversNothing
     */
    public function testUserAgents($ua, $props)
    {
        if (!is_array($props) || !count($props)) {
            $this->markTestSkipped('Could not run test - no properties were defined to test');
        }

        $actualProps = self::$browscap->getBrowser($ua, true);

        foreach ($props as $propName => $propValue) {
            self::assertArrayHasKey(
                $propName,
                $actualProps,
                'Actual property did not have "' . $propName . '" property [' . serialize($actualProps) . ']'
            );

            self::assertSame(
                $propValue,
                $actualProps[$propName],
                'Expected actual "' . $propName . '" to be "' . $propValue . '" (was "' . $actualProps[$propName] . '")'
            );
        }
    }
}
