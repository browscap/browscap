# Browser Capabilities Project

[![Continuous Integration](https://github.com/browscap/browscap/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/browscap/browscap/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/browscap/browscap/branch/master/graph/badge.svg)](https://codecov.io/gh/browscap/browscap)

This tool is used to build and maintain browscap files.

## Installation

```
$ git clone git://github.com/browscap/browscap.git
$ cd browscap
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install
```

## What's changed in version 6048
* https://github.com/browscap/browscap/pull/2535 Added recent new Apple platforms (Mac OS, iOS and iPadOS)

## What's changed in version 6028

### BC breaks listed

 * Interface changed for class \Browscap\Data\Factory\UserAgentFactory

## What's changed in version 6027

### BC breaks listed

 * Strict type hints have been added throughout. This may break some type assumptions made in earlier versions.
 * In many classes Setters and Getters have been removed, the parameters have been moved to the class constructor
 * Some classes are now `final` - use composition instead of inheritance

## What's changed in version 6025

### BC breaks listed

 * The `grep` command and the `diff` command were removed

### Changes

 * The tests for integration testing the source files are split from the other tests
 * Tests on travis use the build pipeline now

## Directory Structure

* `bin` - Contains executable files
* `build` - Contains various builds
* `resources` - Files needed to build the various files, also used to validate the capabilities
* `src` - The code of this project lives here
* `tests` - The testing code of this project lives here

## the CLI commands

There is actually only one cli command available.

### build

This command is used to build a set of defined browscap files.

```php
bin/browscap build [version]
```

#### options

- `version` (required) the name of the version that should be built
- `output` (optional) the directory where the files should be created
- `resources` (optional) the directory where the sources for the build are located
- `coverage` (optional) if this option is set, during the build information is added which can be used to generate a coverage report
- `no-zip` (optional) if this option is set, no zip file is generated during the build

For further documentation on the `build` command, [see here](https://github.com/browscap/browscap/wiki/Build-Command).

## CLI Examples

You can export a new set of browscap files:

```
$ bin/browscap build 5020-test
Resource folder: <your source dir>
Build folder: <your target dir>
Generating full_asp_browscap.ini [ASP/FULL]
Generating full_php_browscap.ini [PHP/FULL]
Generating browscap.ini [ASP]
Generating php_browscap.ini [PHP]
...
All done.
$
```

Now you if you look at `browscap/browscap.ini` you will see a new INI file has been generated.

## Usage Examples

### How to build a standard set of browscap files

This example assumes that you want to build all *php_browscap.ini files.

```php
$logger = new \Monolog\Logger('browscap'); // or maybe any other PSR-3 compatible Logger

$format = \Browscap\Formatter\FormatterInterface::TYPE_PHP; // you may choose the output format you want, the format must be already supported

$resourceFolder = 'resources/'; // please point to the resources directory inside the project
$buildFolder = ''; // choose the directory where the generated file should be written to

// If you are using one of the predefined WriterFactories, you may not choose the file names
$writerCollection = (new \Browscap\Writer\Factory\PhpWriterFactory())->createCollection($logger, $buildFolder);

$dataCollectionFactory = new \Browscap\Data\Factory\DataCollectionFactory($logger);

$buildGenerator = new BuildGenerator(
    $resourceFolder,
    $buildFolder,
    $logger,
    $writerCollection,
    $dataCollectionFactory
);

$version       = '';    // what you want to be written into the generated file
$createZipFile = false; // It is not possible yet to create a zipped version of a custom named browscap file

$buildGenerator->run($version, $createZipFile);
```

### How to build a custom set of browscap files

If you want to build a custom set of browscap files, you may not use the predefined WriterFactories.

```php
$logger = new \Monolog\Logger('browscap'); // or maybe any other PSR-3 compatible Logger

$format = \Browscap\Formatter\FormatterInterface::TYPE_PHP; // you may choose the output format you want, the format must be already supported

$resourceFolder = 'resources/'; // please point to the resources directory inside the project
$buildFolder = ''; // choose the directory where the generated file should be written to

$propertyHolder = new \Browscap\Data\PropertyHolder();

// build a standard version browscap.json file
$jsonFormatter = new \Browscap\Formatter\JsonFormatter($propertyHolder);
$jsonFilter    = new \Browscap\Filter\StandardFilter($propertyHolder);

$jsonWriter = new \Browscap\Writer\JsonWriter('relative path or name of the target file', $logger);
$jsonWriter->setFormatter($jsonFormatter);
$jsonWriter->setFilter($jsonFilter);

// build a lite version browscap.xml file
$xmlFormatter = new \Browscap\Formatter\XmlFormatter($propertyHolder);
$xmlFilter    = new \Browscap\Filter\LiteFilter($propertyHolder);

$xmlWriter = new \Browscap\Writer\XmlWriter('relative path or name of the target file', $logger);
$xmlWriter->setFormatter($xmlFormatter);
$xmlWriter->setFilter($xmlFilter);

$writerCollection = new \Browscap\Writer\WriterCollection();
$writerCollection->addWriter($jsonWriter);
$writerCollection->addWriter($xmlWriter);

$dataCollectionFactory = new \Browscap\Data\Factory\DataCollectionFactory($logger);

$buildGenerator = new BuildGenerator(
    $resourceFolder,
    $buildFolder,
    $logger,
    $writerCollection,
    $dataCollectionFactory
);

$version       = '';    // what you want to be written into the generated file
$createZipFile = false; // It is not possible yet to create a zipped version of a custom named browscap file

$buildGenerator->run($version, $createZipFile);
```

### How to build a custom browscap.ini

If you want to build a custom browscap file you may choose the file name and the fields which are included.

Note: It is not possible to build a custom browscap.ini file with the CLI command.

```php
$logger = new \Monolog\Logger('browscap'); // or maybe any other PSR-3 compatible Logger
// If using Monolog, you need specify a log handler, e.g. for STDOUT: $logger->pushHandler(new \Monolog\Handler\ErrorLogHandler());

$format = \Browscap\Formatter\FormatterInterface::TYPE_PHP; // you may choose the output format you want, the format must be already supported
$file   = null; // you may set a custom file name here
$fields = []; // choose the fields you want inside of your browscap file

$resourceFolder = 'resources/'; // please point to the resources directory inside the project
$buildFolder = ''; // choose the directory where the generated file should be written to

$writerCollection = (new \Browscap\Writer\Factory\CustomWriterFactory())->createCollection($logger, $buildFolder, $file, $fields, $format);

$dataCollectionFactory = new \Browscap\Data\Factory\DataCollectionFactory($logger);

$buildGenerator = new BuildGenerator(
    $resourceFolder,
    $buildFolder,
    $logger,
    $writerCollection,
    $dataCollectionFactory
);

$version       = '';                       // version what you want to be written into the generated file
$dateTime      = new \DateTimeImmutable(); // date what you want to be written into the generated file
$createZipFile = false;                    // It is not possible yet to create a zipped version of a custom named browscap file

$buildGenerator->run($version, $dateTime, $createZipFile);
```

## Issues and feature requests

Please report your issues and ask for new features on the GitHub Issue Tracker at https://github.com/browscap/browscap/issues

### Contributing

For instructions on how to contribute see the [CONTRIBUTE.md](https://github.com/browscap/browscap/blob/master/CONTRIBUTING.md) file.

### License

See the [LICENSE](https://github.com/browscap/browscap/blob/master/LICENSE) file.
