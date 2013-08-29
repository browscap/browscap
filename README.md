Browser Capabilities Project
============================

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

In order to show what the tool can do after you have cloned and installed dependencies, first obtain an original browscap INI, recommended to be the `full_asp_browscap.ini`, then do something like this:

```
$ cd resources
$ rm -R user-agents
$ bin/browscap import build/full_asp_browscap_5020.ini
Written 1 user agents to JSON: defaultproperties.json
Written 3 user agents to JSON: ask.json
Written 12 user agents to JSON: baidu.json
...
Written 11 user agents to JSON: ie-9-0.json
Written 1 user agents to JSON: default-browser.json
$
```

Now if you examine the resources/user-agents folder, you will see many hundreds of JSON files containing the UAs.

Then you can export a new browscap.ini from the JSON files:

```
$ bin/browscap build 5020-test
Processing file ./resources/user-agents/palm-web.json ...
Processing file ./resources/user-agents/chromium-30-0.json ...
Processing file ./resources/user-agents/mozilla-1-5.json ...
...
Processing file ./resources/user-agents/iron-10-0.json ...
Processing file ./resources/user-agents/opera-12-12.json ...
Generating browscap.ini
All done.
$
```

Now you if you look at `browscap/browscapTest.ini` you will see a new INI file has been generated.

Then you can compare to your original `full_asp_browscap.ini` to check for differences:

```
$ bin/browscap diff build/browscapTest.ini build/full_asp_browscap_5020.ini
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

Copyright (c) 2013 Browser Capabilities Project

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
