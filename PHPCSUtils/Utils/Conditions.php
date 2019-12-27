<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Utils;

use PHP_CodeSniffer\Files\File;

/**
 * Utility functions for use when examining token conditions.
 *
 * @since 1.0.0 The `getCondition()` and `hasCondition()` methods are based
 *              on and inspired by the methods of the same name in the
 *              PHPCS native `File` class.
 *              Also see {@see \PHPCSUtils\BackCompat\BCFile}.
 */
class Conditions
{

    /**
     * Retrieve the position of the condition for the passed token.
     *
     * @see \PHP_CodeSniffer\Files\File::getCondition()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::getCondition() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the token we are checking.
     * @param int|string                  $type      The type of token to search for.
     *
     * @return int|false Integer stack pointer to the condition or FALSE if the token
     *                   does not have the condition.
     */
    public static function getCondition(File $phpcsFile, $stackPtr, $type)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (isset($tokens[$stackPtr]['conditions']) === false) {
            return false;
        }

        $conditions = $tokens[$stackPtr]['conditions'];
        foreach ($conditions as $token => $condition) {
            if ($condition === $type) {
                return $token;
            }
        }

        return false;
    }

    /**
     * Determine if the passed token has a condition of one of the passed types.
     *
     * @see \PHP_CodeSniffer\Files\File::hasCondition()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::hasCondition() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the token we are checking.
     * @param int|string|array            $types     The type(s) of tokens to search for.
     *
     * @return bool
     */
    public static function hasCondition(File $phpcsFile, $stackPtr, $types)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (isset($tokens[$stackPtr]['conditions']) === false) {
            return false;
        }

        $types      = (array) $types;
        $conditions = $tokens[$stackPtr]['conditions'];

        foreach ($types as $type) {
            if (\in_array($type, $conditions, true) === true) {
                // We found a token with the required type.
                return true;
            }
        }

        return false;
    }
}
