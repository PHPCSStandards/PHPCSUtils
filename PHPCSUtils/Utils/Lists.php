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

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\GetTokensAsString;

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

    /**
     * Find the list opener & closer based on a T_LIST or T_OPEN_SHORT_ARRAY token.
     *
     * This method also accepts `T_OPEN_SQUARE_BRACKET` tokens to allow it to be
     * PHPCS cross-version compatible as the short array tokenizing has been plagued by
     * a number of bugs over time, which affects the short list determination.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile   The file being scanned.
     * @param int                   $stackPtr    The position of the T_LIST or T_OPEN_SHORT_ARRAY
     *                                           token in the stack.
     * @param true|null             $isShortList Short-circuit the short list check for T_OPEN_SHORT_ARRAY
     *                                           tokens if it isn't necessary.
     *                                           Efficiency tweak for when this has already been established,
     *                                           i.e. when encountering a nested list while walking the
     *                                           tokens in a list.
     *                                           Use with care.
     *
     * @return array|false Array with two keys `opener`, `closer` or false if
     *                     not a (short) list token or if the opener/closer
     *                     could not be determined.
     */
    public static function getOpenClose(File $phpcsFile, $stackPtr, $isShortList = null)
    {
        $tokens = $phpcsFile->getTokens();

        // Is this one of the tokens this function handles ?
        if (isset($tokens[$stackPtr]) === false
            || isset(Collections::$listTokensBC[$tokens[$stackPtr]['code']]) === false
        ) {
            return false;
        }

        switch ($tokens[ $stackPtr ]['code']) {
            case \T_LIST:
                if (isset($tokens[$stackPtr]['parenthesis_opener'])) {
                    // PHPCS 3.5.0.
                    $opener = $tokens[$stackPtr]['parenthesis_opener'];
                } else {
                    // PHPCS < 3.5.0.
                    $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
                    if ($nextNonEmpty !== false
                        && $tokens[$nextNonEmpty]['code'] === \T_OPEN_PARENTHESIS
                    ) {
                        $opener = $nextNonEmpty;
                    }
                }

                if (isset($opener, $tokens[$opener]['parenthesis_closer'])) {
                    $closer = $tokens[$opener]['parenthesis_closer'];
                }
                break;

            case \T_OPEN_SHORT_ARRAY:
            case \T_OPEN_SQUARE_BRACKET:
                if ($isShortList === true || self::isShortList($phpcsFile, $stackPtr) === true) {
                    $opener = $stackPtr;
                    $closer = $tokens[$stackPtr]['bracket_closer'];
                }
                break;
        }

        if (isset($opener, $closer)) {
            return [
                'opener' => $opener,
                'closer' => $closer,
            ];
        }

        return false;
    }

    /**
     * Retrieves information on the assignments made in the specified (long/short) list.
     *
     * This method also accepts `T_OPEN_SQUARE_BRACKET` tokens to allow it to be
     * PHPCS cross-version compatible as the short array tokenizing has been plagued by
     * a number of bugs over time, which affects the short list determination.
     *
     * The returned array will contain the following basic information for each assignment:
     *
     * <code>
     *   0 => array(
     *         'raw'      => string, // The full content of the variable definition, including
     *                               // whitespace and comments.
     *                               // This may be an empty string when an item is being skipped.
     *         'is_empty' => bool,   // Whether this is an empty list item, i.e. the
     *                               // second item in `list($a, , $b)`.
     *        )
     * </code>
     *
     * Non-empty list items will have the following additional array indexes set:
     * <code>
     *         'assignment'           => string,       // The content of the assignment part, cleaned of comments.
     *                                                 // This could be a nested list.
     *         'nested_list'          => bool,         // Whether this is a nested list.
     *         'assign_by_reference'  => bool,         // Is the variable assigned by reference?
     *         'reference_token'      => int|false,    // The stack pointer to the reference operator or
     *                                                 // FALSE when not a reference assignment.
     *         'variable'             => string|false, // The base variable being assigned to or
     *                                                 // FALSE in case of a nested list or variable variable.
     *                                                 // I.e. `$a` in `list($a['key'])`.
     *         'assignment_token'     => int,          // The start pointer for the assignment.
     *         'assignment_end_token' => int,          // The end pointer for the assignment.
     * </code>
     *
     *
     * Assignments with keys will have the following additional array indexes set:
     * <code>
     *         'key'                 => string, // The content of the key, cleaned of comments.
     *         'key_token'           => int,    // The stack pointer to the start of the key.
     *         'key_end_token'       => int,    // The stack pointer to the end of the key.
     *         'double_arrow_token'  => int,    // The stack pointer to the double arrow.
     * </code>
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the function token
     *                                               to acquire the parameters for.
     *
     * @return array An array with information on each assignment made, including skipped assignments (empty),
     *               or an empty array if no assignments are made at all (fatal error in PHP 7+).
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is not of
     *                                                      type T_LIST, T_OPEN_SHORT_ARRAY or
     *                                                      T_OPEN_SQUARE_BRACKET.
     */
    public static function getAssignments(File $phpcsFile, $stackPtr)
    {
        $openClose = self::getOpenClose($phpcsFile, $stackPtr);
        if ($openClose === false) {
            // The `getOpenClose()` method does the $stackPtr validation.
            throw new RuntimeException('The Lists::getAssignments() method expects a long/short list token.');
        }

        $opener = $openClose['opener'];
        $closer = $openClose['closer'];

        $tokens = $phpcsFile->getTokens();

        $vars         = [];
        $start        = null;
        $lastNonEmpty = null;
        $reference    = null;
        $list         = null;
        $lastComma    = $opener;
        $current      = [];

        for ($i = ($opener + 1); $i <= $closer; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']])) {
                continue;
            }

            switch ($tokens[$i]['code']) {
                case \T_DOUBLE_ARROW:
                    $current['key'] = GetTokensAsString::compact($phpcsFile, $start, $lastNonEmpty, true);

                    $current['key_token']          = $start;
                    $current['key_end_token']      = $lastNonEmpty;
                    $current['double_arrow_token'] = $i;

                    // Partial reset.
                    $start        = null;
                    $lastNonEmpty = null;
                    $reference    = null;
                    break;

                case \T_COMMA:
                case $tokens[$closer]['code']:
                    if ($tokens[$i]['code'] === $tokens[$closer]['code']) {
                        if ($i !== $closer) {
                            $lastNonEmpty = $i;
                            break;
                        } elseif ($start === null && $lastComma === $opener) {
                            // This is an empty list.
                            break 2;
                        }
                    }

                    $current['raw'] = \trim(GetTokensAsString::normal($phpcsFile, ($lastComma + 1), ($i - 1)));

                    if ($start === null) {
                        $current['is_empty'] = true;
                    } else {
                        $current['is_empty']    = false;
                        $current['assignment']  = \trim(
                            GetTokensAsString::compact($phpcsFile, $start, $lastNonEmpty, true)
                        );
                        $current['nested_list'] = isset($list);

                        $current['assign_by_reference'] = false;
                        $current['reference_token']     = false;
                        if (isset($reference)) {
                            $current['assign_by_reference'] = true;
                            $current['reference_token']     = $reference;
                        }

                        $current['variable'] = false;
                        if (isset($list) === false && $tokens[$start]['code'] === \T_VARIABLE) {
                            $current['variable'] = $tokens[$start]['content'];
                        }
                        $current['assignment_token']     = $start;
                        $current['assignment_end_token'] = $lastNonEmpty;
                    }

                    $vars[] = $current;

                    // Reset.
                    $start        = null;
                    $lastNonEmpty = null;
                    $reference    = null;
                    $list         = null;
                    $lastComma    = $i;
                    $current      = [];

                    break;

                case \T_LIST:
                case \T_OPEN_SHORT_ARRAY:
                    if ($start === null) {
                        $start = $i;
                    }

                    /*
                     * As the top level list has an open/close, we know we don't have a parse error and
                     * any nested lists will be tokenized correctly, so no need for extra checks here.
                     */
                    $nestedOpenClose = self::getOpenClose($phpcsFile, $i, true);
                    $list            = $i;
                    $i               = $nestedOpenClose['closer'];

                    $lastNonEmpty = $i;
                    break;

                case \T_BITWISE_AND:
                    $reference    = $i;
                    $lastNonEmpty = $i;
                    break;

                default:
                    if ($start === null) {
                        $start = $i;
                    }

                    $lastNonEmpty = $i;
                    break;
            }
        }

        return $vars;
    }
}
