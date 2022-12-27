<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * Bootstrap file for the unit tests.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 *
 * @since 1.0
 */

namespace PHPCSUtils\Tests;

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Internal\NoFileCache;

if (\defined('PHP_CODESNIFFER_IN_TESTS') === false) {
    \define('PHP_CODESNIFFER_IN_TESTS', true);
}

// The below two defines are needed for PHPCS 3.x.
if (\defined('PHP_CODESNIFFER_CBF') === false) {
    \define('PHP_CODESNIFFER_CBF', false);
}

if (\defined('PHP_CODESNIFFER_VERBOSITY') === false) {
    \define('PHP_CODESNIFFER_VERBOSITY', 0);
}

if (\is_dir(\dirname(__DIR__) . '/vendor') && \file_exists(\dirname(__DIR__) . '/vendor/autoload.php')) {
    $vendorDir = \dirname(__DIR__) . '/vendor';
} else {
    echo 'Please run `composer install` before attempting to run the unit tests.
You can still run the tests using a PHPUnit phar file, but some test dependencies need to be available.
';
    die(1);
}

// Get the PHPCS dir from an environment variable.
$phpcsDir = \getenv('PHPCS_DIR');

// This may be a Composer install.
if ($phpcsDir === false && \is_dir($vendorDir . '/squizlabs/php_codesniffer')) {
    $phpcsDir = $vendorDir . '/squizlabs/php_codesniffer';
} elseif ($phpcsDir !== false) {
    $phpcsDir = \realpath($phpcsDir);
}

// Try and load the PHPCS autoloader.
if ($phpcsDir !== false && \file_exists($phpcsDir . '/autoload.php')) {
    require_once $phpcsDir . '/autoload.php';

    // Pre-load the token back-fills to prevent undefined constant notices.
    require_once $phpcsDir . '/src/Util/Tokens.php';
} else {
    echo 'Uh oh... can\'t find PHPCS.

If you use Composer, please run `composer install`.
Otherwise, make sure you set a `PHPCS_DIR` environment variable in your phpunit.xml file
pointing to the PHPCS directory.
';
    die(1);
}

if (\defined('__PHPUNIT_PHAR__')) {
    // Testing via a PHPUnit phar.

    // Load the PHPUnit Polyfills autoloader.
    require_once $vendorDir . '/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';

    /*
     * Autoloader specifically for the test files.
     * Fixes issues with PHPUnit not being able to find test classes being extended when running
     * in a non-Composer context.
     */
    \spl_autoload_register(function ($fqClassName) {
        // Only try & load our own classes.
        if (\stripos($fqClassName, 'PHPCSUtils\Tests\\') !== 0) {
            return;
        }

        // Strip namespace prefix 'PHPCSUtils\Tests\'.
        $relativeClass = \substr($fqClassName, 17);
        $file          = \realpath(__DIR__) . \DIRECTORY_SEPARATOR
            . \strtr($relativeClass, '\\', \DIRECTORY_SEPARATOR) . '.php';

        if (\file_exists($file)) {
            include_once $file;
        }
    });
} else {
    // Testing via a Composer setup.
    require_once $vendorDir . '/autoload.php';
}

/*
 * Alias the non-namespaced PHPUnit 4.x/5.x test case class to the
 * namespaced PHPUnit 6+ version.
 */
require_once \dirname(__DIR__) . '/phpcsutils-autoload.php';

/*
 * Determine whether to run the test suite with caching enabled or disabled.
 *
 * Use `<php><env name="PHPCSUTILS_USE_CACHE" value="On|Off"/></php>` in a `phpunit.xml` file
 * or set the ENV variable on an OS-level.
 *
 * If the ENV variable has not been set, the tests will run with caching turned OFF.
 */
if (\defined('PHPCSUTILS_USE_CACHE') === false) {
    $useCache = \getenv('PHPCSUTILS_USE_CACHE');
    if ($useCache === false) {
        Cache::$enabled       = false;
        NoFileCache::$enabled = false;
    } else {
        $useCache             = \filter_var($useCache, \FILTER_VALIDATE_BOOLEAN);
        Cache::$enabled       = $useCache;
        NoFileCache::$enabled = $useCache;
    }
}

unset($phpcsDir, $vendorDir, $useCache);
