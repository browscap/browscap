## Reporting Issues & Missing User Agents

If you find the Browscap INI is missing a User Agent, you can report this by
opening an issue on the [Issue Tracker](https://github.com/browscap/browscap/issues).

All bug reports should include:

* The version and type of browscap.ini you are using (e.g. 5029, Standard)
* The method you are using to download and/or parse the file (e.g. manually, using browscap/browscap-php, using GaretJax/phpbrowscap, using Classic ASP, using PHP built in etc.)
* The full missing User Agent(s)
* Any other information you feel may be relevant
* If you are reporting something other than a missing user agents, it would be useful if you could provide steps to reproduce your issue

## Adding User Agents Yourself

In order to make updates to the Source Files (in the `resources`) folder, you
may submit Pull Requests on GitHub to help update the files.

The format of these files is documented [here](https://github.com/browscap/browscap/wiki/Resource:-User-Agents-Database).

You must also add a UA test in the [tests/fixtures/issues](https://github.com/browscap/browscap/tree/master/tests/fixtures/issues) folder that verifies that your changes work correctly. See more information in the [Testing](https://github.com/browscap/browscap/wiki/Testing) section on the wiki.
