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

Then you can compare to an original 5020 version `full_asp_browscap.ini` to check for differences:

```
$ bin/browscap diff build/full_asp_browscap.ini build/full_asp_browscap_5020.ini
The following differences have been found:

[GJK_Browscap_Version]
"Released" differs (L / R): Thu, 29 Aug 2013 22:54:50 +0100 / Mon, 29 Jul 2013 22:22:31 -0000
"Version" differs (L / R): 5020-test / 5020

There were 2 differences found in the comparison.
$
```

You can see here the only differences were the release date and version number, which is normal - so it works! :)

## Directory Structure

* `bin` - Contains executable files
* `build` - Contains various builds
* `resources` - Files needed to build the various files, also used to validate the capabilities
* `src` - The code of this project lives here

## Contributing

For instructions on how to contribute see the [CONTRIBUTE.md](https://github.com/browscap/browscap/blob/master/CONTRIBUTE.md) file.

## License

See the [LICENSE](https://github.com/browscap/browscap/blob/master/LICENSE) file.
