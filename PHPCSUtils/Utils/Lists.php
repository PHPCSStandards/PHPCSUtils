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
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;

/**
 * Utility functions to retrieve information when working with lists.
 *
 * @since 1.0.0
 */
class Lists
{

    /**
     * Determine whether a T_OPEN/CLOSE_SHORT_ARRAY token is a short list() construct.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the short array bracket token.
     *
     * @return bool True if the token passed is the open/close bracket of a short list.
     *              False if the token is a short array bracket or not
     *              a T_OPEN/CLOSE_SHORT_ARRAY token.
     */
    public static function isShortList(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Is this one of the tokens this function handles ?
        if (isset($tokens[$stackPtr]) === false
            || isset(Collections::$shortListTokens[$tokens[$stackPtr]['code']]) === false
        ) {
            return false;
        }

        switch ($tokens[$stackPtr]['code']) {
            case \T_OPEN_SHORT_ARRAY:
                $opener = $stackPtr;
                $closer = $tokens[$stackPtr]['bracket_closer'];
                break;

            case \T_CLOSE_SHORT_ARRAY:
                $opener = $tokens[$stackPtr]['bracket_opener'];
                $closer = $stackPtr;
                break;
        }

        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($closer + 1), null, true);
        if ($nextNonEmpty !== false && $tokens[$nextNonEmpty]['code'] === \T_EQUAL) {
            return true;
        }

        // Check for short list in foreach, i.e. `foreach($array as [$a, $b])`.
        $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($opener - 1), null, true);
        if ($prevNonEmpty !== false
            && ($tokens[$prevNonEmpty]['code'] === \T_AS
                || $tokens[$prevNonEmpty]['code'] === \T_DOUBLE_ARROW)
            && Parentheses::lastOwnerIn($phpcsFile, $prevNonEmpty, \T_FOREACH) !== false
        ) {
            return true;
        }

        // Maybe this is a short list syntax nested inside another short list syntax ?
        $parentOpen = $opener;
        do {
            $parentOpen = $phpcsFile->findPrevious(
                \T_OPEN_SHORT_ARRAY,
                ($parentOpen - 1),
                null,
                false,
                null,
                true
            );

            if ($parentOpen === false) {
                return false;
            }
        } while ($tokens[$parentOpen]['bracket_closer'] < $opener);

        return self::isShortList($phpcsFile, $parentOpen);
    }
}
