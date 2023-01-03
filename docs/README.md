# README

This directory does **not** contain any documentation, but is used to _generate_ the [documentation website]
on each release.

The documentation is generated as follows:
* The project root `README` file is converted to the website homepage using Jekyll.
* The project root `CHANGELOG` file is converted to a changelog webpage using Jekyll.
* The docblocks in the source code is used to generate the API documentation using phpDocumentor.

This is handled automatically whenever a new release is published via GH Actions, with the help
of a few scripts, which can be found in the `.github/GHPages` directory.

:point_right: If you've discovered an error or typo on the website, please submit a pull request
updating the underlying source files.

[documentation website]: https://phpcsutils.com/
