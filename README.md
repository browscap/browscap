Browser Capabilities Project
============================

[![Build Status](https://travis-ci.org/browscap/browscap.png?branch=master)](https://travis-ci.org/browscap/browscap) [![Code Coverage](https://scrutinizer-ci.com/g/browscap/browscap/badges/coverage.png?s=82d775d431d7e22060cf06be0115aa2da2aa6546)](https://scrutinizer-ci.com/g/browscap/browscap/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/browscap/browscap/badges/quality-score.png?s=2df900495a8b7951066cec5b5ded3a69279240d9)](https://scrutinizer-ci.com/g/browscap/browscap/)

This tool is used to build and maintain browscap files.

## Install

```
$ git clone git://github.com/browscap/browscap.git
$ cd browscap
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install
```

## Usage

```
bin/browscap build [version]
```

For further documentation on the `build` command, [see here](https://github.com/browscap/browscap/wiki/Build-Command).

## Demonstrating Functionality

You can export a new set of browscap.ini from the JSON files:

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

## How to build a custom browscap.ini

It is not possible to build a custom browscap.ini file with the CLI command.

```php
$logger = new \Monolog\Logger('browscap'); // or maybe any other PSR-3 compatible Logger

$format = \Browscap\Formatter\FormatterInterface::TYPE_PHP; // you may choose the output format you want, the format must be already supported
$file   = null; // you may set a custom file name here
$fields = []; // choose the fields you want inside of your browscap file

$resourceFolder = 'resources/'; // please point to the resources directory inside the project
$buildFolder = ''; // choose the directory where the generated file should be written to

$writerCollection = (new \Browscap\Writer\Factory\CustomWriterFactory())->createCollection($logger, $buildFolder, $file, $fields);

$buildGenerator = new BuildGenerator(
    $resourceFolder,
    $buildFolder,
    $logger,
    $writerCollection
);

$version       = '';    // what you want to be written into the generated file
$createZipFile = false; // It is not possible yet to create a zipped version of a custom named browscap file

$buildGenerator->run($version, $createZipFile);
```

## Directory Structure

* `bin` - Contains executable files
* `build` - Contains various builds
* `resources` - Files needed to build the various files, also used to validate the capabilities
* `src` - The code of this project lives here

## Contributing

For instructions on how to contribute see the [CONTRIBUTE.md](https://github.com/browscap/browscap/blob/master/CONTRIBUTING.md) file.

## License

See the [LICENSE](https://github.com/browscap/browscap/blob/master/LICENSE) file.
