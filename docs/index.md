---
title:       PHPCSUtils
description: "PHPCSUtils: A suite of utility functions for use with PHP_CodeSniffer."
anchor:      home
permalink:   /
seo:
    type: WebSite
    publisher:
        type: Organisation
---

Features
-------------------------------------------

[PHPCSUtils](https://github.com/PHPCSStandards/PHPCSUtils) is a set of utilities to aid developers of sniffs for [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

This package offers the following features:

<div id="feature-list">

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

</div>

Minimum Requirements
-------------------------------------------

* PHP 5.4 or higher.
* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) 2.6.0+, 3.1.0+ (with the exception of PHPCS 3.5.3).


Integrating PHPCSUtils in your external PHPCS standard
-------------------------------------------

### Composer-based with a minimum PHPCS requirement of PHPCS 3.1.0

If your external PHP_CodeSniffer standard only supports Composer-based installs and has a minimum PHPCS requirement of PHP_CodeSniffer 3.1.0, integrating PHPCSUtils is pretty straight forward.

Run the following from the root of your external PHPCS standard's project:

<div class="language-bash highlighter-rouge"><div class="highlight"><pre class="highlight"><code>composer require <span class="s">{{ site.phpcsutils.packagist }}</span>:"<span class="mf">^1.0</span>"
</code></pre></div></div>

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



Frequently Asked Questions
-------

<div id="faq">

#### Q: How does this all work without an external standard needing to register an autoloader?

A: As PHPCSUtils is registered with PHPCS as an external standard and PHPCSUtils complies with the naming requirements of PHPCS, the PHPCS native autoloader will automatically take care of loading the classes you use from PHPCSUtils.

#### Q: What does the `PHPCS23Utils` standard do?

A: All the `PHPCS23Utils` standard does is load the `phpcsutils-autoload.php` file.

PHPCS 3.x uses namespaces, while PHPCS 2.x does not. The `phpcsutils-autoload.php` file creates `class_alias`-es for the most commonly used PHPCS classes, including all PHPCS classes used by PHPCSUtils. That way, both your external standard as well as PHPCSUtils can refer to the PHPCS 3.x class names and the code will still work in PHPCS 2.x.

#### Q: Why is PHP_CodeSniffer 3.5.3 not supported?

A: The backfill for PHP 7.4 numeric literals with underscores in PHP_CodeSniffer 3.5.3 is broken and there is no way to reliably provide support for anything to do with numbers or `T_STRING` tokens when using PHP_CodeSniffer 3.5.3 as the tokens returned by the tokenizer are unpredictable and unreliable.

The backfill was fixed in PHP_CodeSniffer 3.5.4.

</div>

Contributing
-------
Contributions to this project are welcome. Clone the repo, branch off from `develop`, make your changes, commit them and send in a pull request.

If you are unsure whether the changes you are proposing would be welcome, please open an issue first to discuss your proposal.

License
-------
This code is released under the [GNU Lesser General Public License (LGPLv3)](http://www.gnu.org/copyleft/lesser.html).
