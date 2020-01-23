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
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Lists;

/**
 * Utility functions for use when examining arrays.
 *
 * @since 1.0.0
 */
class Arrays
{

    /**
     * Determine whether a `T_OPEN/CLOSE_SHORT_ARRAY` token is a short array() construct
     * and not a short list.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the short array bracket token.
     *
     * @return bool True if the token passed is the open/close bracket of a short array.
     *              False if the token is a short list bracket or not one of the accepted tokens.
     */
    public static function isShortArray(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Is this one of the tokens this function handles ?
        if (isset($tokens[$stackPtr]) === false
            || isset(Collections::$shortArrayTokens[$tokens[$stackPtr]['code']]) === false
        ) {
            return false;
        }

        return (Lists::isShortList($phpcsFile, $stackPtr) === false);
    }
}
