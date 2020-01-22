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
     * Determine whether a `T_OPEN/CLOSE_SHORT_ARRAY` token is a short list() construct.
     *
     * This method also accepts `T_OPEN/CLOSE_SQUARE_BRACKET` tokens to allow it to be
     * PHPCS cross-version compatible as the short array tokenizing has been plagued by
     * a number of bugs over time, which affects the short list determination.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the short array bracket token.
     *
     * @return bool True if the token passed is the open/close bracket of a short list.
     *              False if the token is a short array bracket or plain square bracket
     *              or not one of the accepted tokens.
     */
    public static function isShortList(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Is this one of the tokens this function handles ?
        if (isset($tokens[$stackPtr]) === false
            || isset(Collections::$shortListTokensBC[$tokens[$stackPtr]['code']]) === false
        ) {
            return false;
        }

        /*
         * BC: Work around a bug in the tokenizer of PHPCS 2.8.0 - 3.2.3 where a `[` would be
         * tokenized as T_OPEN_SQUARE_BRACKET instead of T_OPEN_SHORT_ARRAY if it was
         * preceded by a PHP open tag at the very start of the file.
         *
         * In that case, we also know for sure that it is a short list as long as the close
         * bracket is followed by an `=` sign.
         *
         * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/1971
         *
         * Also work around a bug in the tokenizer of PHPCS < 2.8.0 where a `[` would be
         * tokenized as T_OPEN_SQUARE_BRACKET instead of T_OPEN_SHORT_ARRAY if it was
         * preceded by a closing curly belonging to a control structure.
         *
         * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/1284
         */
        if ($tokens[$stackPtr]['code'] === \T_OPEN_SQUARE_BRACKET
            || $tokens[$stackPtr]['code'] === \T_CLOSE_SQUARE_BRACKET
        ) {
            $opener = $stackPtr;
            if ($tokens[$stackPtr]['code'] === \T_CLOSE_SQUARE_BRACKET) {
                $opener = $tokens[$stackPtr]['bracket_opener'];
            }

            if (isset($tokens[$opener]['bracket_closer']) === false) {
                // Definitely not a short list.
                return false;
            }

            $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($opener - 1), null, true);
            if ((($prevNonEmpty === 0 && $tokens[$prevNonEmpty]['code'] === \T_OPEN_TAG) // Bug #1971.
                || ($tokens[$prevNonEmpty]['code'] === \T_CLOSE_CURLY_BRACKET
                    && isset($tokens[$prevNonEmpty]['scope_condition']))) // Bug #1284.
            ) {
                $closer       = $tokens[$opener]['bracket_closer'];
                $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($closer + 1), null, true);
                if ($nextNonEmpty !== false && $tokens[$nextNonEmpty]['code'] === \T_EQUAL) {
                    return true;
                }
            }

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
                [\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET], // BC: PHPCS#1971.
                ($parentOpen - 1),
                null,
                false,
                null,
                true
            );

            if ($parentOpen === false) {
                return false;
            }
        } while (isset($tokens[$parentOpen]['bracket_closer']) === true
            && $tokens[$parentOpen]['bracket_closer'] < $opener
        );

        return self::isShortList($phpcsFile, $parentOpen);
    }
}
