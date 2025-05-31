<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * Autoloader for the PHPCSUtils files.
 *
 * - If an external standard uses the PHPCS native unit test framework,
 *   this file does not need to be included.
 *
 * - If an external standard uses its own unit test setup, this file should
 *   be included from the unit test bootstrap file.
 *
 * - If an external standard uses the PHPCSUtils {@see PHPCSUtils\TestUtils\UtilityMethodTestCase}
 *   class to test their own utility methods, this file should be included from
 *   the unit test bootstrap file.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 *
 * @since 1.0.0
 */

if (defined('PHPCSUTILS_AUTOLOAD') === false) {
    /*
     * Register an autoloader.
     *
     * External PHPCS standards which have their own unit test suite
     * should include this file in their test runner bootstrap.
     */
    spl_autoload_register(function ($fqClassName) {
        // Only try & load our own classes.
        if (stripos($fqClassName, 'PHPCSUtils\\') !== 0) {
            return;
        }

        $file = realpath(__DIR__) . DIRECTORY_SEPARATOR . strtr($fqClassName, '\\', DIRECTORY_SEPARATOR) . '.php';

        if (file_exists($file)) {
            include_once $file;
        }
    });

    define('PHPCSUTILS_AUTOLOAD', true);
}
