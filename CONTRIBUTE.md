## Reporting Missing User Agents

If you find the Browscap INI is missing a User Agent, you can report this by
opening an issue on the [Issue Tracker](https://github.com/browscap/browscap/issues).

Your report should include:

* The version and type of browscap.ini you are using (e.g. 5020, Standard)
* The full missing User Agent(s)

## Contributing User Agents

In order to make updates to the Source Files (in the `resources`) folder, you
may submit Pull Requests on GitHub to help update the files.

The format of these files is documented [here](https://github.com/browscap/browscap/wiki/Resource:-User-Agents-Database).

You should also add a UA test in [tests/fixtures/TestUserAgents.php](https://github.com/browscap/browscap/blob/master/tests/fixtures/TestUserAgents.php) that verifies that your changes work correctly. See more information in the [Testing](https://github.com/browscap/browscap/wiki/Testing) section on the wiki.
