<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * Autoloader for the PHPCSUtils files.
 * Also provides PHPCS cross-version class aliases.
 *
 * - If an external standard only supports PHPCS >= 3.1.0 and uses the PHPCS
 *   native unit test framework, this file does not need to be included.
 *
 * - When PHPCS 2.x support is desired, include the `"PHPCS23Utils"` standard
 *   in the ruleset of the external standard and this file will be included
 *   automatically.
 *   Including this file will allow PHPCSUtils to work in both PHPCS 2.x
 *   as well as PHPCS 3.x.
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
        if (stripos($fqClassName, 'PHPCSUtils') !== 0) {
            return;
        }

        $file = realpath(__DIR__) . DIRECTORY_SEPARATOR . strtr($fqClassName, '\\', DIRECTORY_SEPARATOR) . '.php';

        if (file_exists($file)) {
            include_once $file;
        }
    });

    define('PHPCSUTILS_AUTOLOAD', true);
}

if (defined('PHPCSUTILS_PHPCS_ALIASES_SET') === false) {
    /*
     * Alias a number of PHPCS 2.x classes to their PHPCS 3.x equivalents.
     *
     * {@internal The PHPCS file have been reorganized in PHPCS 3.x, quite
     * a few "old" classes have been split and spread out over several "new"
     * classes. In other words, this will only work for a limited number
     * of classes.}}
     *
     * {@internal The `class_exists` wrappers are needed to play nice with other
     * external PHPCS standards creating cross-version compatibility in a
     * similar manner.}}
     */
    if (interface_exists('\PHP_CodeSniffer_Sniff') === true
        && interface_exists('\PHP_CodeSniffer\Sniffs\Sniff') === false
    ) {
        class_alias('\PHP_CodeSniffer_Sniff', '\PHP_CodeSniffer\Sniffs\Sniff');
    }

    if (class_exists('\PHP_CodeSniffer_File') === true
        && class_exists('\PHP_CodeSniffer\Files\File') === false
    ) {
        class_alias('\PHP_CodeSniffer_File', '\PHP_CodeSniffer\Files\File');
    }

    if (class_exists('\PHP_CodeSniffer_Tokens') === true
        && class_exists('\PHP_CodeSniffer\Util\Tokens') === false
    ) {
        class_alias('\PHP_CodeSniffer_Tokens', '\PHP_CodeSniffer\Util\Tokens');
    }

    if (class_exists('\PHP_CodeSniffer_Exception') === true
        && class_exists('\PHP_CodeSniffer\Exceptions\RuntimeException') === false
    ) {
        class_alias('\PHP_CodeSniffer_Exception', '\PHP_CodeSniffer\Exceptions\RuntimeException');
    }

    if (class_exists('\PHP_CodeSniffer_Exception') === true
        && class_exists('\PHP_CodeSniffer\Exceptions\TokenizerException') === false
    ) {
        class_alias('\PHP_CodeSniffer_Exception', '\PHP_CodeSniffer\Exceptions\TokenizerException');
    }

    define('PHPCSUTILS_PHPCS_ALIASES_SET', true);
}

if (defined('PHPCSUTILS_PHPUNIT_ALIASES_SET') === false) {
    /*
     * Alias the PHPUnit 4/5 TestCase class to its PHPUnit 6+ name.
     *
     * This allows both the PHPCSUtils native unit tests as well as the
     * `UtilityMethodTestCase` class to work cross-version with PHPUnit
     * below 6.x and above.
     *
     * {@internal The `class_exists` wrappers are needed to play nice with
     * PHPUnit bootstrap files of external standards which may be creating
     * cross-version compatibility in a similar manner.}}
     */
    if (class_exists('PHPUnit_Framework_TestCase') === true
        && class_exists('PHPUnit\Framework\TestCase') === false
    ) {
        class_alias('PHPUnit_Framework_TestCase', 'PHPUnit\Framework\TestCase');
    }

    define('PHPCSUTILS_PHPUNIT_ALIASES_SET', true);
}
