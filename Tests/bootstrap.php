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

/*
 * PHPUnit 9.3 is the first version which supports Xdebug 3, but we're using PHPUnit 9.2
 * for code coverage due to PHP_Parser interfering with our tests.
 *
 * For now, until a fix is pulled to allow us to use PHPUnit 9.3, this will allow
 * PHPUnit 9.2 to run with Xdebug 3 for code coverage.
 */
if (\extension_loaded('xdebug') && \version_compare(\phpversion('xdebug'), '3', '>=')) {
    if (\defined('XDEBUG_CC_UNUSED') === false) {
        \define('XDEBUG_CC_UNUSED', null);
    }
    if (\defined('XDEBUG_CC_DEAD_CODE') === false) {
        \define('XDEBUG_CC_DEAD_CODE', null);
    }
}

// Get the PHPCS dir from an environment variable.
$phpcsDir = \getenv('PHPCS_DIR');

// This may be a Composer install.
if ($phpcsDir === false && \is_dir(\dirname(__DIR__) . '/vendor/squizlabs/php_codesniffer')) {
    $vendorDir = \dirname(__DIR__) . '/vendor';
    $phpcsDir  = $vendorDir . '/squizlabs/php_codesniffer';
} elseif ($phpcsDir !== false) {
    $phpcsDir = \realpath($phpcsDir);
}

// Try and load the PHPCS autoloader.
if ($phpcsDir !== false && \file_exists($phpcsDir . '/autoload.php')) {
    // PHPCS 3.x.
    require_once $phpcsDir . '/autoload.php';

    // Pre-load the token back-fills to prevent undefined constant notices.
    require_once $phpcsDir . '/src/Util/Tokens.php';
} elseif ($phpcsDir !== false && \file_exists($phpcsDir . '/CodeSniffer.php')) {
    // PHPCS 2.x.
    require_once $phpcsDir . '/CodeSniffer.php';

    // Pre-load the token back-fills to prevent undefined constant notices.
    require_once $phpcsDir . '/CodeSniffer/Tokens.php';
} else {
// @todo: change URL!!!!
    echo 'Uh oh... can\'t find PHPCS.

If you use Composer, please run `composer install`.
Otherwise, make sure you set a `PHPCS_DIR` environment variable in your phpunit.xml file
pointing to the PHPCS directory.
';
    die(1);
}

// Load the composer autoload if available.
if (isset($vendorDir) && \file_exists($vendorDir . '/autoload.php')) {
    require_once $vendorDir . '/autoload.php';
} else {
    /*
     * Autoloader specifically for the test files.
     * Fixes issues with PHPUnit not being able to find test classes being extended when running
     * in a non-Composer context.
     */
    \spl_autoload_register(function ($class) {
        // Only try & load our own classes.
        if (\stripos($class, 'PHPCSUtils\Tests\\') !== 0) {
            return;
        }

        // Strip namespace prefix 'PHPCSUtils\Tests\'.
        $class = \substr($class, 17);
        $file  = \realpath(__DIR__) . \DIRECTORY_SEPARATOR . \strtr($class, '\\', \DIRECTORY_SEPARATOR) . '.php';
        if (\file_exists($file)) {
            include_once $file;
        }
    });
}

/*
 * Alias the PHPCS 2.x classes to their PHPCS 3.x equivalent if necessary.
 *
 * Also alias the non-namespaced PHPUnit 4.x/5.x test case class to the
 * namespaced PHPUnit 6+ version.
 */
require_once \dirname(__DIR__) . '/phpcsutils-autoload.php';

unset($phpcsDir, $vendorDir);
