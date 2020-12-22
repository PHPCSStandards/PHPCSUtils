PHPCSUtils: A suite of utility functions for use with PHP_CodeSniffer
=====================================================

[![Latest Stable Version](https://poser.pugx.org/phpcsstandards/phpcsutils/v/stable)](https://packagist.org/packages/phpcsstandards/phpcsutils)
[![Release Date of the Latest Version](https://img.shields.io/github/release-date/PHPCSStandards/PHPCSUtils.svg?maxAge=1800)](https://github.com/PHPCSStandards/PHPCSUtils/releases)
:construction:
[![Latest Unstable Version](https://img.shields.io/badge/unstable-dev--develop-e68718.svg?maxAge=2419200)](https://packagist.org/packages/phpcsstandards/phpcsutils#dev-develop)
[![Last Commit to Unstable](https://img.shields.io/github/last-commit/PHPCSStandards/PHPCSUtils/develop.svg)](https://github.com/PHPCSStandards/PHPCSUtils/commits/develop)

[![Minimum PHP Version](https://img.shields.io/packagist/php-v/phpcsstandards/phpcsutils.svg?maxAge=3600)](https://packagist.org/packages/phpcsstandards/phpcsutils)
[![CS Build Status](https://github.com/PHPCSStandards/PHPCSUtils/workflows/CS/badge.svg?branch=develop)](https://github.com/PHPCSStandards/PHPCSUtils/actions?query=workflow%3ACS)
[![Test Build Status](https://github.com/PHPCSStandards/PHPCSUtils/workflows/Test/badge.svg?branch=develop)](https://github.com/PHPCSStandards/PHPCSUtils/actions?query=workflow%3ATest)
[![Tested on PHP 5.4 to 8.0](https://img.shields.io/badge/tested%20on-PHP%205.4%20|%205.5%20|%205.6%20|%207.0%20|%207.1%20|%207.2%20|%207.3%20|%207.4%20|%208.0-brightgreen.svg?maxAge=2419200)](https://github.com/PHPCSStandards/PHPCSUtils/actions?query=workflow%3ATest)
[![Coverage Status](https://coveralls.io/repos/github/PHPCSStandards/PHPCSUtils/badge.svg)](https://coveralls.io/github/PHPCSStandards/PHPCSUtils)

[![Docs Build Status](https://github.com/PHPCSStandards/PHPCSUtils/workflows/Docs/badge.svg?branch=develop)](https://phpcsutils.com/)
[![License: LGPLv3](https://poser.pugx.org/phpcsstandards/phpcsutils/license)](https://github.com/PHPCSStandards/PHPCSUtils/blob/master/LICENSE)
![Awesome](https://img.shields.io/badge/awesome%3F-yes!-brightgreen.svg)


* [Features](#features)
* [Minimum Requirements](#minimum-requirements)
* [Integrating PHPCSUtils in your external PHPCS standard](#integrating-phpcsutils-in-your-external-phpcs-standard)
    + [Composer-based with a minimum PHPCS requirement of PHPCS 3.1.0](#composer-based-with-a-minimum-phpcs-requirement-of-phpcs-310)
    + [Composer-based with a minimum PHPCS requirement of PHPCS 2.6.0](#composer-based-with-a-minimum-phpcs-requirement-of-phpcs-260)
    + [Non-Composer based integration](#non-composer-based-integration)
* [Frequently Asked Questions](#frequently-asked-questions)
* [Potential Support Questions from your End-Users](#potential-support-questions-from-your-end-users)
* [Contributing](#contributing)
* [License](#license)


Features
-------------------------------------------

[PHPCSUtils](https://github.com/PHPCSStandards/PHPCSUtils) is a set of utilities to aid developers of sniffs for [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) (or "PHPCS" for short).

This package offers the following features:

<div id="feature-list">

### Use the latest version of PHP_CodeSniffer native utility functions.

Normally to use the latest version of PHP_CodeSniffer native utility functions, you would have to raise the minimum requirements of your external PHPCS standard.

Now you won't have to anymore. This package allows you to use the latest version of those utility functions in all PHP_CodeSniffer versions from PHPCS 2.6.0 and up.

### Several abstract sniff classes which your sniffs can extend.

These classes take most of the heavy lifting away for some frequently occurring sniff types.

### A collection of static properties and methods for often-used token groups.

Collections of related tokens often-used and needed for sniffs.
These are additional "token groups" to compliment the ones available through the PHPCS native `PHP_CodeSniffer\Util\Tokens` class.

### An ever-growing number of utility functions for use with PHP_CodeSniffer.

Whether you need to split an `array` into the individual items, are trying to determine which variables are being assigned to in a `list()` or are figuring out whether a function has a DocBlock, PHPCSUtils has got you covered!

Includes improved versions of the PHPCS native utility functions and plenty of new utility functions.

These functions are, of course, compatible with PHPCS 2.6.0 up to PHPCS `master`.

### Test utilities

An abstract `UtilityMethodTestCase` class to support testing of your utility methods written for PHP_CodeSniffer.
Compatible with both PHPCS 2.x as well as 3.x. Supports PHPUnit 4.x up to 9.x.

### Backward compatibility layer

A `PHPCS23Utils` standard which allows sniffs to work in both PHPCS 2.x and 3.x, as well as a few helper functions for external standards which still want to support both PHP_CodeSniffer 2.x as well as 3.x.

### Fully documented

To see detailed information about all the available abstract sniffs, utility functions and PHPCS helper functions, have a read through the [extensive documentation](https://phpcsutils.com/).

</div>

Minimum Requirements
-------------------------------------------

* PHP 5.4 or higher.
* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) 2.6.0+, 3.1.0+ (with the exception of PHPCS 3.5.3).
* Recommended PHP extensions for optimal functionality:
    - PCRE with Unicode support (normally enabled by default)


Integrating PHPCSUtils in your external PHPCS standard
-------------------------------------------

### Composer-based with a minimum PHPCS requirement of PHPCS 3.1.0

If your external PHP_CodeSniffer standard only supports Composer-based installs and has a minimum PHPCS requirement of PHP_CodeSniffer 3.1.0, integrating PHPCSUtils is pretty straight forward.

Run the following from the root of your external PHPCS standard's project:
```bash
composer require phpcsstandards/phpcsutils:^1.0
```

No further action needed. You can start using all the utility functions, abstract sniff classes and other features of PHPCSUtils straight away.

> :information_source: The PHPCSUtils package includes the [DealerDirect Composer PHPCS plugin](https://github.com/Dealerdirect/phpcodesniffer-composer-installer).
>
> This plugin will automatically register PHPCSUtils (and your own external standard) with PHP_CodeSniffer, so you and your users don't have to worry about this anymore.
>
> :warning: Note: if your end-user installation instructions include instructions on adding a Composer PHPCS plugin or on manually registering your standard with PHPCS using the `--config-set installed_paths` command, you can remove those instructions as they are no longer needed.

#### Running your unit tests

If your unit tests use the PHP_CodeSniffer native unit test suite, all is good.

If you have your own unit test suite to test your sniffs, make sure to load the Composer `vendor/autoload.php` file in your PHPUnit bootstrap file or _as_ the PHPUnit bootstrap file.

If you intend to use the test utilities provided in the `PHPCSUtils/TestUtils` directory, make sure you also load the `vendor/phpcsstandards/phpcsutils/phpcsutils-autoload.php` file in your PHPUnit bootstrap file.


### Composer-based with a minimum PHPCS requirement of PHPCS 2.6.0

Follow the above instructions for use with PHPCS 3.x.

In addition to that, add the following to the `ruleset.xml` file of your standard(s):
```xml
    <!-- Make the utility functions available in PHPCS 2.x -->
    <rule ref="PHPCS23Utils"/>
```

> :information_source: The `PHPCS23Utils` "standard" does not add any real sniffs, it only makes sure that the Utility functions will work in PHPCS 2.x as well.

#### Running your unit tests

If your standard supports both PHPCS 2.x as well as 3.x, you are bound to already have a PHPUnit `bootstrap.php` file in place.

To allow the unit tests to find the relevant files for PHPCSUtils, make sure that the bootstrap loads both the Composer `vendor/autoload.php` file, as well as the `vendor/phpcsstandards/phpcsutils/phpcsutils-autoload.php` file.


### Non-Composer based integration

In this case, more than anything, you will need to update the non-Composer installation instructions for your end-users.

To use a non-Composer based installation for your sniff development environment, the same instructions would apply.

Your installation instructions for a non-Composer based installation will probably look similar to this:

> * Install [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) via [your preferred method](https://github.com/squizlabs/PHP_CodeSniffer#installation).
> * Register the path to PHPCS in your system `$PATH` environment variable to make the `phpcs` command available from anywhere in your file system.
> * Download the [latest _YourStandardName_ release] and unzip/untar it into an arbitrary directory.
>     You can also choose to clone the repository using git.
> * Add the path to the directory in which you placed your copy of the _YourStandardName_ repo to the PHP_CodeSniffer configuration using the below command:
>    ```bash
>    phpcs --config-set installed_paths /path/to/YourStandardName
>    ```
>    **Warning**: :warning: The `installed_paths` command overwrites any previously set `installed_paths`. If you have previously set `installed_paths` for other external standards, run `phpcs --config-show` first and then run the `installed_paths` command with all the paths you need separated by comma's, i.e.:
>    ```bash
>    phpcs --config-set installed_paths /path/1,/path/2,/path/3
>    ```

For things to continue working when you add PHPCSUtils to your standard, you need to replace the last bullet with this:

> * **Next, download the [latest PHPCSUtils release](https://github.com/PHPCSStandards/PHPCSUtils/releases) and unzip/untar it into an arbitrary directory.**
>     You can also choose to clone the repository using git.
> * Add the path to the **directories** in which you placed your copy of the _YourStandardName_ repo **and the PHPCSUtils repo** to the PHP_CodeSniffer configuration using the below command:
>    ```bash
>    phpcs --config-set installed_paths /path/to/YourStandardName,/path/to/PHPCSUtils
>    ```
>    **Warning**: :warning: The `installed_paths` command overwrites any previously set `installed_paths`. If you have previously set `installed_paths` for other external standards, run `phpcs --config-show` first and then run the `installed_paths` command with all the paths you need separated by comma's, i.e.:
>    ```bash
>    phpcs --config-set installed_paths /path/1,/path/2,/path/3
>    ```

#### Running your unit tests

To support non-Composer based installs for running your sniff unit tests, you will need to adjust the PHPUnit `bootstrap.php` file to allow for passing an environment variable pointing to your PHPCSUtils installation.

<details>
  <summary><b>Example bootstrap code using a <code>PHPCSUTILS_DIR</code> environment variable</b></summary>

```php
// Get the PHPCS dir from an environment variable.
$phpcsUtilDir = getenv('PHPCSUTILS_DIR');

// This may be a Composer install.
if ($phpcsUtilDir === false && file_exists(__DIR__ . '/vendor/autoload.php')) {
    $vendorDir    = __DIR__ . '/vendor';
    $phpcsUtilDir = $vendorDir . '/phpcsstandards/phpcsutils';

    // Load the Composer autoload file.
    require_once $vendorDir . '/autoload.php';

    // This snippet is only needed when you use the PHPCSUtils TestUtils or if your standard still supports PHPCS 2.x.
    if (file_exists($phpcsUtilDir . '/phpcsutils-autoload.php')) {
        require_once $phpcsUtilDir . '/phpcsutils-autoload.php';
    }

} elseif ($phpcsUtilDir !== false) {
    $phpcsUtilDir = realpath($phpcsUtilDir);

    require_once $phpcsUtilDir . '/phpcsutils-autoload.php';
} else {
    echo 'Uh oh... can\'t find PHPCSUtils.

If you use Composer, please run `composer install`.
Otherwise, make sure you set a `PHPCSUTILS_DIR` environment variable in your phpunit.xml file
pointing to the PHPCS directory.
';

    die(1);
}
```

</details>

Once that's done, you will need to make a small tweak to your own dev environment to get the unit tests runnning for a non-Composer based install:
* Copy your project's `phpunit.xml.dist` file to `phpunit.xml`.
* Add the following to the `phpunit.xml` file within the `<phpunit>` tags, replacing `/path/to/PHPCSUtils` with the path in which you installed PHPCSUtils on your local system:
    ```xml
    <php>
        <env name="PHPCSUTILS_DIR" value="/path/to/PHPCSUtils"/>
    </php>
    ```


Frequently Asked Questions
-------

<div id="faq">

#### Q: How does this all work without an external standard needing to register an autoloader?

A: As PHPCSUtils is registered with PHPCS as an external standard and PHPCSUtils complies with the naming requirements of PHPCS, the PHPCS native autoloader will automatically take care of loading the classes used from PHPCSUtils.

#### Q: What does the `PHPCS23Utils` standard do?

A: All the `PHPCS23Utils` standard does is load the `phpcsutils-autoload.php` file.

PHPCS 3.x uses namespaces, while PHPCS 2.x does not. The `phpcsutils-autoload.php` file creates `class_alias`-es for the most commonly used PHPCS classes, including all PHPCS classes used by PHPCSUtils. That way, both your external standard as well as PHPCSUtils can refer to the PHPCS 3.x class names and the code will still work in PHPCS 2.x.

#### Q: Why is PHP_CodeSniffer 3.5.3 not supported?

A: The backfill for PHP 7.4 numeric literals with underscores in PHP_CodeSniffer 3.5.3 is broken and there is no way to reliably provide support for anything to do with numbers or `T_STRING` tokens when using PHP_CodeSniffer 3.5.3 as the tokens returned by the tokenizer are unpredictable and unreliable.

The backfill was fixed in PHP_CodeSniffer 3.5.4.

#### Q: Any other problematic PHPCS versions?

A: Well, the arrow function backfill which was added in PHPCS 3.5.3 is still causing problems. In a very limited set of circumstances, it will even hang the Tokenizer. A fix for [one particular such problem](https://github.com/squizlabs/php_codesniffer/issues/2926) has been committed to `master`, but is not (yet) in a released version. It is expected to be released in PHP_CodeSniffer 3.5.6.

As the Tokenizer hanging is a problem unrelated to PHPCSUtils and not something which can be mitigated from within PHPCSUtils in any conceivable way, PHPCSUtils won't block installation in combination with PHPCS 3.5.4 and 3.5.5.

There are several other issues which can not be worked around, like scope closers being set incorrectly and throwing the scope setting off for the rest of the file, so not withstanding that PHPCSUtils can work around a lot of issues, it is still highly recommended to advise your end-users to always use the latest version of PHPCS for the most reliable results.

#### Q: Does using PHPCSUtils have any effect on the PHPCS native sniffs?

A: No. PHPCSUtils will only work for those sniffs which explicitly use the PHPCSUtils functionality.

If your standard includes both PHPCS native sniffs as well as your own sniffs, your own sniffs can benefit from the back-compat layer offered by PHPCSUtils, as well as from the additional utility functions. However, the PHPCS native sniffs will not receive those benefits, as PHPCS itself does not use PHPCSUtils.

#### Q: Do the utilities work with javascript/CSS files?

A: JS/CSS support will be removed from PHP_CodeSniffer in PHPCS 4.x.
While at this time, some of the utilies _may_ work with JS/CSS files, PHPCSUtils does not offer formal support for JS/CSS sniffing with PHP_CodeSniffer and will stop any existing support once PHPCS 4.x has been released.

#### Q: Are all file encodings supported?

A: No. The UTF-8 file encoding is the only officially supported encoding. Support for other encodings may incidentally work, but is not officially supported.

> **It is recommended to advise your users to save their files as UTF-8 encoded for the best results.**

PHP_CodeSniffer 3.x will default to UTF-8 as the expected file encoding.
If your standard supports PHP_CodeSniffer 2.x, it is recommended to set the expected encoding in the ruleset to `utf-8`, like so: `<arg name="encoding" value="utf-8"/>` or to advise users to use the CLI option `--encoding=utf-8`.


Potential Support Questions from your End-Users
-------

#### Q: A user reports a fatal "class not found" error for a class from PHPCSUtils.

1. Check that the version of PHPCSUtils the user has installed complies with the minimum version of PHPCSUtils your standard requires. If not, they will need to upgrade.
2. If the version is correct, this indicates that the end-user does not have PHPCSUtils installed and/or registered with PHP_CodeSniffer.
    - Please review your standard's installation instructions to make sure that PHPCSUtils will be installed when those are followed.
    - Inform the user to install PHPCSUtils and register it with PHP_CodeSniffer.

> :bulb: **Pro-tip**: if you want to prevent the fatal error and show a _friendlier_ error message instead, add `<rule ref="PHPCSUtils"/>` to your standard's `ruleset.xml` file.
>
> With that in place, PHP_CodeSniffer will show a _"ERROR: the "PHPCSUtils" coding standard is not installed."_ message if PHPCSUtils is missing as soon as the ruleset loading is finished.

> :bulb: **Pro-tip**: provide upgrade instructions for your end-users. For Composer-based installs, those should look like this:
> ```bash
> composer update your/cs-package --with-dependencies
> ```
> That way, when the user updates your coding standards package, they will automatically also update PHPCSUtils.

</div>


Contributing
-------
Contributions to this project are welcome. Clone the repo, branch off from `develop`, make your changes, commit them and send in a pull request.

If you are unsure whether the changes you are proposing would be welcome, please open an issue first to discuss your proposal.

License
-------
This code is released under the [GNU Lesser General Public License (LGPLv3)](http://www.gnu.org/copyleft/lesser.html).
