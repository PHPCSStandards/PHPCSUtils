<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Utils;

use PHP_CodeSniffer\Files\File;
use PHPCSUtils\BackCompat\Helper;

/**
 * Helper functions for creating PHPCS error/warning messages.
 *
 * @since 1.0.0
 */
class MessageHelper
{

    /**
     * Add a PHPCS message to the output stack as either a warning or an error.
     *
     * @since 1.0.0-alpha4
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param string                      $message   The message.
     * @param int                         $stackPtr  The position of the token
     *                                               the message relates to.
     * @param bool                        $isError   Whether to report the message as an
     *                                               'error' or 'warning'.
     *                                               Defaults to true (error).
     * @param string                      $code      The error code for the message.
     *                                               Defaults to 'Found'.
     * @param array                       $data      Optional input for the data replacements.
     * @param int                         $severity  Optional. Severity level. Defaults to 0 which will
     *                                               translate to the PHPCS default severity level.
     *
     * @return bool
     */
    public static function addMessage(
        File $phpcsFile,
        $message,
        $stackPtr,
        $isError = true,
        $code = 'Found',
        $data = [],
        $severity = 0
    ) {
        if ($isError === true) {
            return $phpcsFile->addError($message, $stackPtr, $code, $data, $severity);
        }

        return $phpcsFile->addWarning($message, $stackPtr, $code, $data, $severity);
    }

    /**
     * Add a PHPCS message to the output stack as either a fixable warning or a fixable error.
     *
     * @since 1.0.0-alpha4
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param string                      $message   The message.
     * @param int                         $stackPtr  The position of the token
     *                                               the message relates to.
     * @param bool                        $isError   Whether to report the message as an
     *                                               'error' or 'warning'.
     *                                               Defaults to true (error).
     * @param string                      $code      The error code for the message.
     *                                               Defaults to 'Found'.
     * @param array                       $data      Optional input for the data replacements.
     * @param int                         $severity  Optional. Severity level. Defaults to 0 which will
     *                                               translate to the PHPCS default severity level.
     *
     * @return bool
     */
    public static function addFixableMessage(
        File $phpcsFile,
        $message,
        $stackPtr,
        $isError = true,
        $code = 'Found',
        $data = [],
        $severity = 0
    ) {
        if ($isError === true) {
            return $phpcsFile->addFixableError($message, $stackPtr, $code, $data, $severity);
        }

        return $phpcsFile->addFixableWarning($message, $stackPtr, $code, $data, $severity);
    }

    /**
     * Convert an arbitrary text string to an alphanumeric string with underscores.
     *
     * Pre-empt issues in XML and PHP when arbitrary strings are being used as error codes.
     *
     * @since 1.0.0-alpha4
     *
     * @param string $text Arbitrary text string intended to be used in an error code.
     *
     * @return string
     */
    public static function stringToErrorcode($text)
    {
        return \preg_replace('`[^a-z0-9_]`i', '_', $text);
    }

    /**
     * Check whether PHPCS can properly handle new lines in violation messages.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/pull/2093
     *
     * @since 1.0.0-alpha4
     *
     * @return bool
     */
    public static function hasNewLineSupport()
    {
        static $supported;
        if (isset($supported) === false) {
            $supported = \version_compare(Helper::getVersion(), '3.3.1', '>=');
        }

        return $supported;
    }

    /**
     * Make the whitespace escape codes used in an arbitrary text string visible.
     *
     * At times, it is useful to show a code snippet in an error message.
     * If such a code snippet contains new lines and/or tab or space characters, those would be
     * displayed as-is in the command-line report, often breaking the layout of the report
     * or making the report harder to read.
     *
     * This method will convert these characters to their escape codes, making them visible in the
     * display string without impacting the report layout.
     *
     * @see \PHPCSUtils\Utils\GetTokensToString             Methods to retrieve a multi-token code snippet.
     * @see \PHP_CodeSniffer\Util\Common\prepareForOutput() Similar PHPCS native method.
     *
     * @since 1.0.0-alpha4
     *
     * @param string $text Arbitrary text string.
     *
     * @return string
     */
    public static function showEscapeChars($text)
    {
        $search  = ["\n", "\r", "\t"];
        $replace = ['\n', '\r', '\t'];

        return \str_replace($search, $replace, $text);
    }
}
