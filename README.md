PHPCSUtils: A suite of utility functions for use with PHP_CodeSniffer
=====================================================

[![Latest Stable Version](https://poser.pugx.org/phpcsstandards/phpcsutils/v/stable)](https://packagist.org/packages/phpcsstandards/phpcsutils)
[![Travis Build Status](https://travis-ci.com/PHPCSStandards/PHPCSUtils.svg?branch=master)](https://travis-ci.com/PHPCSStandards/PHPCSUtils/branches)
[![Release Date of the Latest Version](https://img.shields.io/github/release-date/PHPCSStandards/PHPCSUtils.svg?maxAge=1800)](https://github.com/PHPCSStandards/PHPCSUtils/releases)
:construction:
[![Latest Unstable Version](https://img.shields.io/badge/unstable-dev--develop-e68718.svg?maxAge=2419200)](https://packagist.org/packages/phpcsstandards/phpcsutils#dev-develop)
[![Travis Build Status](https://travis-ci.com/PHPCSStandards/PHPCSUtils.svg?branch=develop)](https://travis-ci.com/PHPCSStandards/PHPCSUtils/branches)
[![Last Commit to Unstable](https://img.shields.io/github/last-commit/PHPCSStandards/PHPCSUtils/develop.svg)](https://github.com/PHPCSStandards/PHPCSUtils/commits/develop)

[![Minimum PHP Version](https://img.shields.io/packagist/php-v/phpcsstandards/phpcsutils.svg?maxAge=3600)](https://packagist.org/packages/phpcsstandards/phpcsutils)
[![Tested on PHP 5.4 to 7.4](https://img.shields.io/badge/tested%20on-PHP%205.4%20|%205.5%20|%205.6%20|%207.0%20|%207.1%20|%207.2%20|%207.3%20|%207.4-brightgreen.svg?maxAge=2419200)](https://travis-ci.com/PHPCSStandards/PHPCSUtils)
[![Coverage Status](https://coveralls.io/repos/github/PHPCSStandards/PHPCSUtils/badge.svg)](https://coveralls.io/github/PHPCSStandards/PHPCSUtils)

[![License: LGPLv3](https://poser.pugx.org/phpcsstandards/phpcsutils/license)](https://github.com/PHPCSStandards/PHPCSUtils/blob/master/LICENSE)
![Awesome](https://img.shields.io/badge/awesome%3F-yes!-brightgreen.svg)


* [Features](#features)
* [Minimum Requirements](#minimum-requirements)
* [Integrating PHPCSUtils in your external PHPCS standard](#integrating-phpcsutils-in-your-external-phpcs-standard)
    + [Composer-based with a minimum PHPCS requirement of PHPCS 3.1.0](#composer-based-with-a-minimum-phpcs-requirement-of-phpcs-310)
    + [Composer-based with a minimum PHPCS requirement of PHPCS 2.6.0](#composer-based-with-a-minimum-phpcs-requirement-of-phpcs-260)
    + [Non-Composer based integration](#non-composer-based-integration)
* [Frequently Asked Questions](#frequently-asked-questions)
* [Contributing](#contributing)
* [License](#license)


Features
-------------------------------------------

This is a set of utilities to aid developers of sniffs for [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

This package offers the following features:

### Use the latest version of PHP_CodeSniffer native utility functions.

Normally to use the latest version of PHP_CodeSniffer native utility functions, you would have to raise the minimum requirements of your external PHPCS standard.

Now you won't have to anymore. This package allows you to use the latest version of those utility functions in all PHP_CodeSniffer versions from PHPCS 2.6.0 and up.

### Several abstract sniff classes which your sniffs can extend.

These classes take most of the heavy lifting away for some frequently occurring sniff types.

### A collection of static properties for often-used token groups.

Collections of related tokens often-used and needed for sniffs.
These are additional "token groups" to compliment the ones available through the PHPCS native `PHP_CodeSniffer\Util\Tokens` class.

### An ever-growing number of utility functions for use with PHP_CodeSniffer.

Whether you need to split an `array` into the individual items, are trying to determine which variables are being assigned to in a `list()` or are figuring out whether a function has a DocBlock, PHPCSUtils has you covered!

Includes improved versions of the PHPCS native utility functions and plenty of new utility functions.

These functions are, of course, compatible with PHPCS 2.6.0 up to PHPCS `master`.

### Test utilities

An abstract `UtilityMethodTestCase` class to support testing of your utility methods written for PHP_CodeSniffer.
Compatible with both PHPCS 2.x as well as 3.x. Supports PHPUnit 4.x up to 9.x.

### Backward compatibility layer

A `PHPCS23Utils` standard which allows sniffs to work in both PHPCS 2.x and 3.x, as well as a few helper functions for external standards which still want to support both PHP_CodeSniffer 2.x as well as 3.x.

### Fully documented

To see detailed information about all the available abstract sniffs, utility functions and PHPCS helper functions, have a read through the [extensive documentation](https://phpcsutils.com/).


Minimum Requirements
-------------------------------------------

* PHP 5.4 or higher.
* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) 2.6.0+, 3.1.0+ (with the exception of PHPCS 3.5.3).


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

> :information_source: The `PHPCS23Utils` "standard" does not add any real sniffs, it just makes sure that the Utility functions will work in PHPCS 2.x as well.

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

#### Q: How does this all work without an external standard needing to register an autoloader?

A: As PHPCSUtils is registered with PHPCS as an external standard and PHPCSUtils complies with the naming requirements of PHPCS, the PHPCS native autoloader will automatically take care of loading the classes you use from PHPCSUtils.

#### Q: What does the `PHPCS23Utils` standard do?

A: All the `PHPCS23Utils` standard does is load the `phpcsutils-autoload.php` file.

PHPCS 3.x uses namespaces, while PHPCS 2.x does not. The `phpcsutils-autoload.php` file creates `class_alias`-es for the most commonly used PHPCS classes, including all PHPCS classes used by PHPCSUtils. That way, both your external standard as well as PHPCSUtils can refer to the PHPCS 3.x class names and the code will still work in PHPCS 2.x.

#### Q: Why is PHP_CodeSniffer 3.5.3 not supported?

A: The backfill for PHP 7.4 numeric literals with underscores in PHP_CodeSniffer 3.5.3 is broken and there is no way to reliably provide support for anything to do with numbers or `T_STRING` tokens when using PHP_CodeSniffer 3.5.3 as the tokens returned by the tokenizer are unpredictable and unreliable.

The backfill was fixed in PHP_CodeSniffer 3.5.4.


Contributing
-------
Contributions to this project are welcome. Clone the repo, branch off from `develop`, make your changes, commit them and send in a pull request.

If you are unsure whether the changes you are proposing would be welcome, please open an issue first to discuss your proposal.

License
-------
This code is released under the GNU Lesser General Public License (LGPLv3). For more information, visit http://www.gnu.org/copyleft/lesser.html
