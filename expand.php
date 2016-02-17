<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(__DIR__);

$autoloadPaths = array(
    'vendor/autoload.php',
    '../../autoload.php',
);

$foundVendorAutoload = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        $foundVendorAutoload = true;
        break;
    }
}

ini_set('memory_limit', '-1');
date_default_timezone_set(date_default_timezone_get());

$buildNumber    = time();
$resourceFolder = __DIR__ . '/resources/';

$buildFolder = __DIR__ . '/build/browscap-ua-test-' . $buildNumber . '/build/';
$cacheFolder = __DIR__ . '/build/browscap-ua-test-' . $buildNumber . '/cache/';

// create build folder if it does not exist
if (!file_exists($buildFolder)) {
    mkdir($buildFolder, 0777, true);
}
if (!file_exists($cacheFolder)) {
    mkdir($cacheFolder, 0777, true);
}

$logger = new \Monolog\Logger('browscap');
$logger->pushHandler(new \Monolog\Handler\NullHandler(\Monolog\Logger::DEBUG));

$buildGenerator = new \Browscap\Generator\BuildGenerator(
    $resourceFolder,
    $buildFolder
);

$writerCollectionFactory = new \Browscap\Writer\Factory\PhpWriterFactory();
$writerCollection        = $writerCollectionFactory->createCollection($logger, $buildFolder);

$buildGenerator
    ->setLogger($logger)
    ->setCollectionCreator(new \Browscap\Helper\CollectionCreator())
    ->setWriterCollection($writerCollection)
;

$buildGenerator->run($buildNumber, false);

$cache = new \WurflCache\Adapter\File(array(\WurflCache\Adapter\File::DIR => $cacheFolder));
// Now, load an INI file into BrowscapPHP\Browscap for testing the UAs
$browscap = new \BrowscapPHP\Browscap();
$browscap
    ->setCache($cache)
    ->setLogger($logger)
;

$browscap->getCache()->flush();
$browscap->convertFile($buildFolder . '/full_php_browscap.ini');

$propertyHolder = new \Browscap\Data\PropertyHolder();

$data = array();
$checks          = array();
$sourceDirectory = __DIR__ . '/tests/fixtures/issues/';

$iterator = new \RecursiveDirectoryIterator($sourceDirectory);

$fileContent = file_get_contents(__DIR__ . '/resources/core/default-browser.json');
$json        = json_decode($fileContent, true);
$properties  = $json['userAgents'][0]['properties'];

unset($properties['RenderingEngine_Description']);

foreach (new \RecursiveIteratorIterator($iterator) as $file) {
    /** @var $file \SplFileInfo */
    if (!$file->isFile() || $file->getExtension() != 'php') {
        continue;
    }

    $tests = require_once $file->getPathname();

    foreach ($tests as $key => $test) {
        if (isset($data[$key])) {
            continue;
        }

        if (isset($checks[$test[0]])) {
            continue;
        }

        $data[$key]       = $test;
        $checks[$test[0]] = $key;

        $newTest = array(
            'ua'         => $test[0],
            'properties' => $properties,
            'lite'       => (array_key_exists('lite', $test) ? $test['lite'] : true),
            'standard'   => (array_key_exists('standard', $test) ? $test['standard'] : true),
        );

        $actualProps = (array) $browscap->getBrowser($test[0]);

        foreach ($properties as $property => $value) {
            if (array_key_exists($property, $test[1])) {
                $newTest['properties'][$property] = $test[1][$property];
            } elseif (array_key_exists(strtolower($property), $actualProps)) {
                $newTest['properties'][$property] = $actualProps[strtolower($property)];
            } else {
                $newTest['properties'][$property] = $value;
            }
        }

        $tests[$key] = $newTest;
    }

    $content = "<?php\n\nreturn " . var_export($tests, true) . ";\n";
    $content = str_replace("=> \n    array (", '=> array(', $content);
    $content = str_replace("=> \n  array (", '=> array(', $content);
    $content = str_replace("\n      '", "\n            '", $content);
    $content = str_replace("\n    '", "\n        '", $content);
    $content = str_replace("\n  '", "\n    '", $content);
    $content = str_replace("\n    )", "\n        )", $content);
    $content = str_replace("\n  )", "\n    )", $content);
    $content = str_replace("array (", 'array(', $content);
    file_put_contents($file->getPathname(), $content);
}
