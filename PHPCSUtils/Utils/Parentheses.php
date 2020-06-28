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
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Utility functions for use when examining parenthesis tokens and arbitrary tokens wrapped in
 * parentheses.
 *
 * @since 1.0.0
 */
class Parentheses
{

    /**
     * Get the pointer to the parentheses owner of an open/close parenthesis.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added BC support for PHP 7.4 arrow functions.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of `T_OPEN/CLOSE_PARENTHESIS` token.
     *
     * @return int|false Integer stack pointer to the parentheses owner; or `FALSE` if the
     *                   parenthesis does not have a (direct) owner or if the token passed
     *                   was not a parenthesis.
     */
    public static function getOwner(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['parenthesis_owner'])) {
            return $tokens[$stackPtr]['parenthesis_owner'];
        }

        /*
         * `T_LIST` and `T_ANON_CLASS` only became parentheses owners in PHPCS 3.5.0.
         * `T_FN` was only backfilled in PHPCS 3.5.3/4/5.
         * - On PHP 7.4 with PHPCS < 3.5.3, T_FN will not yet be a parentheses owner.
         * - On PHP < 7.4 with PHPCS < 3.5.3, T_FN will be tokenized as T_STRING and not yet be a parentheses owner.
         *
         * {@internal As the 'parenthesis_owner' index is only set on parentheses, we didn't need to do any
         * input validation before, but now we do.}
         */
        if (\version_compare(Helper::getVersion(), '3.5.4', '>=') === true) {
            return false;
        }

        if (isset($tokens[$stackPtr]) === false
            || ($tokens[$stackPtr]['code'] !== \T_OPEN_PARENTHESIS
            && $tokens[$stackPtr]['code'] !== \T_CLOSE_PARENTHESIS)
        ) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] === \T_CLOSE_PARENTHESIS) {
            $stackPtr = $tokens[$stackPtr]['parenthesis_opener'];
        }

        $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($prevNonEmpty !== false
            && ($tokens[$prevNonEmpty]['code'] === \T_LIST
            || $tokens[$prevNonEmpty]['code'] === \T_ANON_CLASS
            // Work-around: anon classes were, in certain circumstances, tokenized as T_CLASS prior to PHPCS 3.4.0.
            || $tokens[$prevNonEmpty]['code'] === \T_CLASS
            // Possibly an arrow function.
            || FunctionDeclarations::isArrowFunction($phpcsFile, $prevNonEmpty) === true)
        ) {
            return $prevNonEmpty;
        }

        return false;
    }

    /**
     * Check whether the parenthesis owner of an open/close parenthesis is within a limited
     * set of valid owners.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added BC support for PHP 7.4 arrow functions.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of `T_OPEN/CLOSE_PARENTHESIS` token.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return bool `TRUE` if the owner is within the list of `$validOwners`; `FALSE` if not and
     *              if the parenthesis does not have a (direct) owner.
     */
    public static function isOwnerIn(File $phpcsFile, $stackPtr, $validOwners)
    {
        $owner = self::getOwner($phpcsFile, $stackPtr);
        if ($owner === false) {
            return false;
        }

        $tokens      = $phpcsFile->getTokens();
        $validOwners = (array) $validOwners;

        /*
         * Work around tokenizer bug where anon classes were, in certain circumstances, tokenized
         * as `T_CLASS` prior to PHPCS 3.4.0.
         * As `T_CLASS` is normally not an parenthesis owner, we can safely add it to the array
         * without doing a version check.
         */
        if (\in_array(\T_ANON_CLASS, $validOwners, true)) {
            $validOwners[] = \T_CLASS;
        }

        /*
         * Allow for T_FN token being tokenized as T_STRING before PHPCS 3.5.3.
         */
        if (\defined('T_FN') && \in_array(\T_FN, $validOwners, true)) {
            $validOwners += Collections::arrowFunctionTokensBC();
        }

        return \in_array($tokens[$owner]['code'], $validOwners, true);
    }

    /**
     * Check whether the passed token is nested within parentheses owned by one of the valid owners.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return bool
     */
    public static function hasOwner(File $phpcsFile, $stackPtr, $validOwners)
    {
        return (self::nestedParensWalker($phpcsFile, $stackPtr, $validOwners) !== false);
    }

    /**
     * Retrieve the position of the opener to the first (outer) set of parentheses an arbitrary
     * token is wrapped in, where the parentheses owner is within the set of valid owners.
     *
     * If no `$validOwners` are specified, the opener to the first set of parentheses surrounding
     * the token will be returned.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false Integer stack pointer to the parentheses opener; or `FALSE` if the token
     *                   does not have parentheses owned by any of the valid owners or if
     *                   the token is not nested in parentheses at all.
     */
    public static function getFirstOpener(File $phpcsFile, $stackPtr, $validOwners = [])
    {
        return self::nestedParensWalker($phpcsFile, $stackPtr, $validOwners, false);
    }

    /**
     * Retrieve the position of the closer to the first (outer) set of parentheses an arbitrary
     * token is wrapped in, where the parentheses owner is within the set of valid owners.
     *
     * If no `$validOwners` are specified, the closer to the first set of parentheses surrounding
     * the token will be returned.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false Integer stack pointer to the parentheses closer; or `FALSE` if the token
     *                   does not have parentheses owned by any of the valid owners or if
     *                   the token is not nested in parentheses at all.
     */
    public static function getFirstCloser(File $phpcsFile, $stackPtr, $validOwners = [])
    {
        $opener = self::getFirstOpener($phpcsFile, $stackPtr, $validOwners);
        $tokens = $phpcsFile->getTokens();
        if ($opener !== false && isset($tokens[$opener]['parenthesis_closer']) === true) {
            return $tokens[$opener]['parenthesis_closer'];
        }

        return false;
    }

    /**
     * Retrieve the position of the parentheses owner to the first (outer) set of parentheses an
     * arbitrary token is wrapped in, where the parentheses owner is within the set of valid owners.
     *
     * If no `$validOwners` are specified, the owner to the first set of parentheses surrounding
     * the token will be returned or `false` if the first set of parentheses does not have an owner.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false Integer stack pointer to the parentheses owner; or `FALSE` if the token
     *                   does not have parentheses owned by any of the valid owners or if
     *                   the token is not nested in parentheses at all.
     */
    public static function getFirstOwner(File $phpcsFile, $stackPtr, $validOwners = [])
    {
        $opener = self::getFirstOpener($phpcsFile, $stackPtr, $validOwners);
        if ($opener !== false) {
            return self::getOwner($phpcsFile, $opener);
        }

        return false;
    }

    /**
     * Retrieve the position of the opener to the last (inner) set of parentheses an arbitrary
     * token is wrapped in, where the parentheses owner is within the set of valid owners.
     *
     * If no `$validOwners` are specified, the opener to the last set of parentheses surrounding
     * the token will be returned.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false Integer stack pointer to the parentheses opener; or `FALSE` if the token
     *                   does not have parentheses owned by any of the valid owners or if
     *                   the token is not nested in parentheses at all.
     */
    public static function getLastOpener(File $phpcsFile, $stackPtr, $validOwners = [])
    {
        return self::nestedParensWalker($phpcsFile, $stackPtr, $validOwners, true);
    }

    /**
     * Retrieve the position of the closer to the last (inner) set of parentheses an arbitrary
     * token is wrapped in, where the parentheses owner is within the set of valid owners.
     *
     * If no `$validOwners` are specified, the closer to the last set of parentheses surrounding
     * the token will be returned.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false Integer stack pointer to the parentheses closer; or `FALSE` if the token
     *                   does not have parentheses owned by any of the valid owners or if
     *                   the token is not nested in parentheses at all.
     */
    public static function getLastCloser(File $phpcsFile, $stackPtr, $validOwners = [])
    {
        $opener = self::getLastOpener($phpcsFile, $stackPtr, $validOwners);
        $tokens = $phpcsFile->getTokens();
        if ($opener !== false && isset($tokens[$opener]['parenthesis_closer']) === true) {
            return $tokens[$opener]['parenthesis_closer'];
        }

        return false;
    }

    /**
     * Retrieve the position of the parentheses owner to the last (inner) set of parentheses an
     * arbitrary token is wrapped in where the parentheses owner is within the set of valid owners.
     *
     * If no `$validOwners` are specified, the owner to the last set of parentheses surrounding
     * the token will be returned or `false` if the last set of parentheses does not have an owner.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false Integer stack pointer to the parentheses owner; or `FALSE` if the token
     *                   does not have parentheses owned by any of the valid owners or if
     *                   the token is not nested in parentheses at all.
     */
    public static function getLastOwner(File $phpcsFile, $stackPtr, $validOwners = [])
    {
        $opener = self::getLastOpener($phpcsFile, $stackPtr, $validOwners);
        if ($opener !== false) {
            return self::getOwner($phpcsFile, $opener);
        }

        return false;
    }

    /**
     * Check whether the owner of a outermost wrapping set of parentheses of an arbitrary token
     * is within a limited set of acceptable token types.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position in the stack of the
     *                                                 token to verify.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false Integer stack pointer to the valid parentheses owner; or `FALSE` if
     *                   the token was not wrapped in parentheses or if the outermost set
     *                   of parentheses in which the token is wrapped does not have an owner
     *                   within the set of owners considered valid.
     */
    public static function firstOwnerIn(File $phpcsFile, $stackPtr, $validOwners)
    {
        $opener = self::getFirstOpener($phpcsFile, $stackPtr);

        if ($opener !== false && self::isOwnerIn($phpcsFile, $opener, $validOwners) === true) {
            return self::getOwner($phpcsFile, $opener);
        }

        return false;
    }

    /**
     * Check whether the owner of a innermost wrapping set of parentheses of an arbitrary token
     * is within a limited set of acceptable token types.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position in the stack of the
     *                                                 token to verify.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false Integer stack pointer to the valid parentheses owner; or `FALSE` if
     *                   the token was not wrapped in parentheses or if the innermost set
     *                   of parentheses in which the token is wrapped does not have an owner
     *                   within the set of owners considered valid.
     */
    public static function lastOwnerIn(File $phpcsFile, $stackPtr, $validOwners)
    {
        $opener = self::getLastOpener($phpcsFile, $stackPtr);

        if ($opener !== false && self::isOwnerIn($phpcsFile, $opener, $validOwners) === true) {
            return self::getOwner($phpcsFile, $opener);
        }

        return false;
    }

    /**
     * Helper method. Retrieve the position of a parentheses opener for an arbitrary passed token.
     *
     * If no `$validOwners` are specified, the opener to the first set of parentheses surrounding
     * the token - or if `$reverse = true`, the last set of parentheses - will be returned.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Optional. Array of token constants for the owners
     *                                                 which should be considered valid.
     * @param bool                        $reverse     Optional. Whether to search for the first/outermost
     *                                                 (`false`) or the last/innermost (`true`) set of
     *                                                 parentheses with the specified owner(s).
     *
     * @return int|false Integer stack pointer to the parentheses opener; or `FALSE` if the token
     *                   does not have parentheses owned by any of the valid owners or if
     *                   the token is not nested in parentheses at all.
     */
    private static function nestedParensWalker(File $phpcsFile, $stackPtr, $validOwners = [], $reverse = false)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token is nested in parenthesis.
        if (empty($tokens[$stackPtr]['nested_parenthesis']) === true) {
            return false;
        }

        $validOwners = (array) $validOwners;
        $parentheses = $tokens[$stackPtr]['nested_parenthesis'];

        if (empty($validOwners) === true) {
            // No owners specified, just return the first/last parentheses opener.
            if ($reverse === true) {
                \end($parentheses);
            } else {
                \reset($parentheses);
            }

            return \key($parentheses);
        }

        if ($reverse === true) {
            $parentheses = \array_reverse($parentheses, true);
        }

        foreach ($parentheses as $opener => $closer) {
            if (self::isOwnerIn($phpcsFile, $opener, $validOwners) === true) {
                // We found a token with a valid owner.
                return $opener;
            }
        }

        return false;
    }
}
