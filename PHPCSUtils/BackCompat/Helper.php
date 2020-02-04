<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\BackCompat;

use PHP_CodeSniffer\Files\File;

/**
 * Utility methods to retrieve (configuration) information from PHP_CodeSniffer.
 *
 * PHP_CodeSniffer cross-version compatibility helper for PHPCS 2.x vs PHPCS 3.x.
 *
 * A number of PHPCS classes were split up into several classes in PHPCS 3.x
 * Those classes cannot be aliased as they don't represent the same object.
 * This class provides helper methods for functions which were contained in
 * one of these classes and which are commonly used by external standards.
 *
 * @since 1.0.0 The initial methods in this class have been ported over from
 *              the external PHPCompatibility & WPCS standards.
 */
class Helper
{

    /**
     * The default tab width used by PHP_CodeSniffer.
     *
     * @since 1.0.0
     *
     * @var int
     */
    const DEFAULT_TABWIDTH = 4;

    /**
     * Get the PHP_CodeSniffer version number.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public static function getVersion()
    {
        if (\defined('\PHP_CodeSniffer\Config::VERSION') === false) {
            // PHPCS 2.x.
            return \PHP_CodeSniffer::VERSION;
        }

        // PHPCS 3.x.
        return \PHP_CodeSniffer\Config::VERSION;
    }

    /**
     * Pass config data to PHP_CodeSniffer.
     *
     * @since 1.0.0
     *
     * @param string      $key   The name of the config value.
     * @param string|null $value The value to set. If null, the config entry
     *                           is deleted, reverting it to the default value.
     * @param bool        $temp  Set this config data temporarily for this script run.
     *                           This will not write the config data to the config file.
     *
     * @return bool Whether the setting of the data was successfull.
     */
    public static function setConfigData($key, $value, $temp = false)
    {
        if (\method_exists('\PHP_CodeSniffer\Config', 'setConfigData') === false) {
            // PHPCS 2.x.
            return \PHP_CodeSniffer::setConfigData($key, $value, $temp);
        }

        // PHPCS 3.x.
        return \PHP_CodeSniffer\Config::setConfigData($key, $value, $temp);
    }

    /**
     * Get the value of a single PHP_CodeSniffer config key.
     *
     * @since 1.0.0
     *
     * @param string $key The name of the config value.
     *
     * @return string|null
     */
    public static function getConfigData($key)
    {
        if (\method_exists('\PHP_CodeSniffer\Config', 'getConfigData') === false) {
            // PHPCS 2.x.
            return \PHP_CodeSniffer::getConfigData($key);
        }

        // PHPCS 3.x.
        return \PHP_CodeSniffer\Config::getConfigData($key);
    }

    /**
     * Get the value of a CLI overrulable single PHP_CodeSniffer config key.
     *
     * Use this for config keys which can be set in the `CodeSniffer.conf` file,
     * on the command-line or in a ruleset.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being processed.
     * @param string                      $key       The name of the config value.
     *
     * @return string|null
     */
    public static function getCommandLineData(File $phpcsFile, $key)
    {
        if (\class_exists('\PHP_CodeSniffer\Config') === false) {
            // PHPCS 2.x.
            $config = $phpcsFile->phpcs->cli->getCommandLineValues();
            if (isset($config[$key])) {
                return $config[$key];
            }
        } else {
            // PHPCS 3.x.
            $config = $phpcsFile->config;
            if (isset($config->{$key})) {
                return $config->{$key};
            }
        }

        return null;
    }

    /**
     * Get the applicable tab width as passed to PHP_CodeSniffer from the
     * command-line or the ruleset.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being processed.
     *
     * @return int Tab width. Defaults to the PHPCS native default of 4.
     */
    public static function getTabWidth(File $phpcsFile)
    {
        $tabWidth = self::getCommandLineData($phpcsFile, 'tabWidth');
        if ($tabWidth > 0) {
            return $tabWidth;
        }

        return self::DEFAULT_TABWIDTH;
    }

    /**
     * Check whether the `--ignore-annotations` option has been used.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile Optional. The current file
     *                                               being processed.
     *
     * @return bool True if annotations should be ignored, false otherwise.
     */
    public static function ignoreAnnotations(File $phpcsFile = null)
    {
        if (\class_exists('\PHP_CodeSniffer\Config') === false) {
            // PHPCS 2.x does not support `--ignore-annotations`.
            return false;
        }

        // PHPCS 3.x.
        if (isset($phpcsFile, $phpcsFile->config->annotations)) {
            return ! $phpcsFile->config->annotations;
        }

        $annotations = \PHP_CodeSniffer\Config::getConfigData('annotations');
        if (isset($annotations)) {
            return ! $annotations;
        }

        return false;
    }
}
