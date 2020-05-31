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
use PHPCSUtils\BackCompat\BCTokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\FunctionDeclarations;
use PHPCSUtils\Utils\Parentheses;

/**
 * Utility functions for use when working with operators.
 *
 * @link https://www.php.net/language.operators PHP manual on operators.
 *
 * @since 1.0.0 The `isReference()` method is based on and inspired by
 *              the method of the same name in the PHPCS native `File` class.
 *              Also see {@see \PHPCSUtils\BackCompat\BCFile}.
 *              The `isUnaryPlusMinus()` method is, in part, inspired by the
 *              `Squiz.WhiteSpace.OperatorSpacing` sniff.
 */
class Operators
{

    /**
     * Tokens which indicate that a plus/minus is unary when they preceed it.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <irrelevant>
     */
    private static $extraUnaryIndicators = [
        \T_STRING_CONCAT       => true,
        \T_RETURN              => true,
        \T_ECHO                => true,
        \T_PRINT               => true,
        \T_YIELD               => true,
        \T_COMMA               => true,
        \T_OPEN_PARENTHESIS    => true,
        \T_OPEN_SQUARE_BRACKET => true,
        \T_OPEN_SHORT_ARRAY    => true,
        \T_OPEN_CURLY_BRACKET  => true,
        \T_COLON               => true,
        \T_INLINE_THEN         => true,
        \T_INLINE_ELSE         => true,
        \T_CASE                => true,
    ];

    /**
     * Determine if the passed token is a reference operator.
     *
     * Main differences with the PHPCS version:
     * - Defensive coding against incorrect calls to this method.
     * - Improved handling of select tokenizer errors involving short lists/short arrays.
     *
     * @see \PHP_CodeSniffer\Files\File::isReference()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::isReference() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added BC support for PHP 7.4 arrow functions.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the `T_BITWISE_AND` token.
     *
     * @return bool `TRUE` if the specified token position represents a reference.
     *              `FALSE` if the token represents a bitwise operator.
     */
    public static function isReference(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== \T_BITWISE_AND) {
            return false;
        }

        $tokenBefore = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        if ($tokens[$tokenBefore]['code'] === \T_FUNCTION
            || $tokens[$tokenBefore]['code'] === T_CLOSURE
            || FunctionDeclarations::isArrowFunction($phpcsFile, $tokenBefore) === true
        ) {
            // Function returns a reference.
            return true;
        }

        if ($tokens[$tokenBefore]['code'] === \T_DOUBLE_ARROW) {
            // Inside a foreach loop or array assignment, this is a reference.
            return true;
        }

        if ($tokens[$tokenBefore]['code'] === \T_AS) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if (isset(BCTokens::assignmentTokens()[$tokens[$tokenBefore]['code']]) === true) {
            // This is directly after an assignment. It's a reference. Even if
            // it is part of an operation, the other tests will handle it.
            return true;
        }

        $tokenAfter = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($tokens[$tokenAfter]['code'] === \T_NEW) {
            return true;
        }

        $lastOpener = Parentheses::getLastOpener($phpcsFile, $stackPtr);
        if ($lastOpener !== false) {
            $lastOwner = Parentheses::lastOwnerIn($phpcsFile, $stackPtr, [\T_FUNCTION, \T_CLOSURE]);
            if ($lastOwner !== false) {
                $params = FunctionDeclarations::getParameters($phpcsFile, $lastOwner);
                foreach ($params as $param) {
                    if ($param['pass_by_reference'] === true) {
                        // Function parameter declared to be passed by reference.
                        return true;
                    }
                }
            } elseif (isset($tokens[$lastOpener]['parenthesis_owner']) === false) {
                $prev = false;
                for ($t = ($lastOpener - 1); $t >= 0; $t--) {
                    if ($tokens[$t]['code'] !== \T_WHITESPACE) {
                        $prev = $t;
                        break;
                    }
                }

                if ($prev !== false && $tokens[$prev]['code'] === \T_USE) {
                    // Closure use by reference.
                    return true;
                }
            }
        }

        // Pass by reference in function calls and assign by reference in arrays.
        if ($tokens[$tokenBefore]['code'] === \T_OPEN_PARENTHESIS
            || $tokens[$tokenBefore]['code'] === \T_COMMA
            || $tokens[$tokenBefore]['code'] === \T_OPEN_SHORT_ARRAY
            || $tokens[$tokenBefore]['code'] === \T_OPEN_SQUARE_BRACKET // PHPCS 2.8.0 < 3.3.0.
        ) {
            if ($tokens[$tokenAfter]['code'] === \T_VARIABLE) {
                return true;
            } else {
                $skip   = Tokens::$emptyTokens;
                $skip  += Collections::$OONameTokens;
                $skip  += Collections::$OOHierarchyKeywords;
                $skip[] = \T_DOUBLE_COLON;

                $nextSignificantAfter = $phpcsFile->findNext(
                    $skip,
                    ($stackPtr + 1),
                    null,
                    true
                );
                if ($tokens[$nextSignificantAfter]['code'] === \T_VARIABLE) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine whether a T_MINUS/T_PLUS token is a unary operator.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the plus/minus token.
     *
     * @return bool `TRUE` if the token passed is a unary operator.
     *              `FALSE` otherwise, i.e. if the token is an arithmetic operator,
     *              or if the token is not a `T_PLUS`/`T_MINUS` token.
     */
    public static function isUnaryPlusMinus(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || ($tokens[$stackPtr]['code'] !== \T_PLUS
            && $tokens[$stackPtr]['code'] !== \T_MINUS)
        ) {
            return false;
        }

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false) {
            // Live coding or parse error.
            return false;
        }

        if (isset(BCTokens::operators()[$tokens[$next]['code']]) === true) {
            // Next token is an operator, so this is not a unary.
            return false;
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        /*
         * Check the preceeding token for an indication that this is not an arithmetic operation.
         */
        if (isset(BCTokens::operators()[$tokens[$prev]['code']]) === true
            || isset(BCTokens::comparisonTokens()[$tokens[$prev]['code']]) === true
            || isset(Tokens::$booleanOperators[$tokens[$prev]['code']]) === true
            || isset(BCTokens::assignmentTokens()[$tokens[$prev]['code']]) === true
            || isset(Tokens::$castTokens[$tokens[$prev]['code']]) === true
            || isset(self::$extraUnaryIndicators[$tokens[$prev]['code']]) === true
        ) {
            return true;
        }

        /*
         * BC for PHPCS < 3.1.0 in which the PHP 5.5 T_YIELD token was not yet backfilled.
         * Note: not accounting for T_YIELD_FROM as that would be a parse error anyway.
         */
        if ($tokens[$prev]['code'] === \T_STRING && $tokens[$prev]['content'] === 'yield') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether a ternary is a short ternary/elvis operator, i.e. without "middle".
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the ternary then/else
     *                                               operator in the stack.
     *
     * @return bool `TRUE` if short ternary; or `FALSE` otherwise.
     */
    public static function isShortTernary(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] === \T_INLINE_THEN) {
            $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($nextNonEmpty !== false && $tokens[$nextNonEmpty]['code'] === \T_INLINE_ELSE) {
                return true;
            }
        }

        if ($tokens[$stackPtr]['code'] === \T_INLINE_ELSE) {
            $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($prevNonEmpty !== false && $tokens[$prevNonEmpty]['code'] === \T_INLINE_THEN) {
                return true;
            }
        }

        // Not a ternary operator token.
        return false;
    }
}
