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
use PHPCSUtils\BackCompat\Helper;
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
     * This method also accepts `T_OPEN/CLOSE_SQUARE_BRACKET` tokens to allow it to be
     * PHPCS cross-version compatible as the short array tokenizing has been plagued by
     * a number of bugs over time.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the short array bracket token.
     *
     * @return bool True if the token passed is the open/close bracket of a short array.
     *              False if the token is a short list bracket, a plain square bracket
     *              or not one of the accepted tokens.
     */
    public static function isShortArray(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Is this one of the tokens this function handles ?
        if (isset($tokens[$stackPtr]) === false
            || isset(Collections::$shortArrayTokensBC[$tokens[$stackPtr]['code']]) === false
        ) {
            return false;
        }

        // All known tokenizer bugs are in PHPCS versions before 3.3.0.
        $phpcsVersion = Helper::getVersion();

        /*
         * Deal with square brackets which may be incorrectly tokenized short arrays.
         */
        if (isset(Collections::$shortArrayTokens[$tokens[$stackPtr]['code']]) === false) {
            if (\version_compare($phpcsVersion, '3.3.0', '>=')) {
                // These will just be properly tokenized, plain square brackets. No need for further checks.
                return false;
            }

            $opener = $stackPtr;
            if ($tokens[$stackPtr]['code'] === \T_CLOSE_SQUARE_BRACKET) {
                $opener = $tokens[$stackPtr]['bracket_opener'];
            }

            if (isset($tokens[$opener]['bracket_closer']) === false) {
                return false;
            }

            $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($opener - 1), null, true);

            if (\version_compare($phpcsVersion, '2.8.0', '>=')) {
                /*
                 * BC: Work around a bug in the tokenizer of PHPCS 2.8.0 - 3.2.3 where a `[` would be
                 * tokenized as T_OPEN_SQUARE_BRACKET instead of T_OPEN_SHORT_ARRAY if it was
                 * preceded by a PHP open tag at the very start of the file.
                 *
                 * If we have square brackets which are not that specific situation, they are just plain
                 * square brackets.
                 *
                 * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/1971
                 */
                if ($prevNonEmpty !== 0 || $tokens[$prevNonEmpty]['code'] !== \T_OPEN_TAG) {
                    return false;
                }
            }
        }

        // In all other circumstances, make sure this isn't a short list instead of a short array.
        return (Lists::isShortList($phpcsFile, $stackPtr) === false);
    }
}
