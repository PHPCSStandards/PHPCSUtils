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
use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\FunctionDeclarations;
use PHPCSUtils\Utils\Parentheses;
use PHPCSUtils\Utils\Scopes;

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
        \T_EXIT                => true,
        \T_CONTINUE            => true,
        \T_BREAK               => true,
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
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokenization.
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
            || $tokens[$tokenBefore]['code'] === \T_CLOSURE
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
            $lastOwner = Parentheses::getOwner($phpcsFile, $lastOpener);

            if (isset(Collections::functionDeclarationTokensBC()[$tokens[$lastOwner]['code']]) === true
                // As of PHPCS 4.x, `T_USE` is a parenthesis owner.
                || $tokens[$lastOwner]['code'] === \T_USE
            ) {
                $params = FunctionDeclarations::getParameters($phpcsFile, $lastOwner);
                foreach ($params as $param) {
                    if ($param['reference_token'] === $stackPtr) {
                        // Function parameter declared to be passed by reference.
                        return true;
                    }
                }
            }
        }

        /*
         * Pass by reference in function calls, assign by reference in arrays and
         * closure use by reference in PHPCS 2.x and 3.x.
         */
        if ($tokens[$tokenBefore]['code'] === \T_OPEN_PARENTHESIS
            || $tokens[$tokenBefore]['code'] === \T_COMMA
            || $tokens[$tokenBefore]['code'] === \T_OPEN_SHORT_ARRAY
            || $tokens[$tokenBefore]['code'] === \T_OPEN_SQUARE_BRACKET // PHPCS 2.8.0 < 3.3.0.
        ) {
            if ($tokens[$tokenAfter]['code'] === \T_VARIABLE) {
                return true;
            } else {
                $skip   = Tokens::$emptyTokens;
                $skip  += Collections::namespacedNameTokens();
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
     * Determine if the passed token is a type union separator.
     *
     * The `T_BITWISE_OR` token is used in PHP as bitwise or, but as of PHP 8.0, also as the
     * separator in union type declarations.
     *
     * As of PHPCS 3.6.0, the type union separator will be tokenized as `T_TYPE_UNION` in PHPCS.
     * However, for any standard which needs to support PHPCS < 3.6.0, this method can analyze
     * whether a T_BITWISE_OR token is a type union separator or an actual bitwise or operator.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/pull/3032
     *
     * @since 1.0.0-alpha4
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the `T_BITWISE_OR` token.
     *
     * @return bool `TRUE` if the specified token position represents a type union separator.
     *              `FALSE` if the token represents a bitwise operator.
     */
    public static function isTypeUnion(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['type'] === 'T_TYPE_UNION') {
            // Just in case.
            return true;
        }

        if ($tokens[$stackPtr]['code'] !== \T_BITWISE_OR) {
            return false;
        }

        /*
         * Check if it's a union separator in a property or parameter type.
         */
        $ignore         = Collections::propertyTypeTokensBC();
        $ignore        += Collections::parameterTypeTokensBC();
        $ignore        += Tokens::$emptyTokens;
        $funcDeclTokens = Collections::functionDeclarationTokensBC();

        $afterType = $phpcsFile->findNext($ignore, ($stackPtr + 1), null, true, null, true);
        if ($afterType !== false) {
            if ($tokens[$afterType]['code'] !== \T_VARIABLE) {
                // Skip past reference and variadic indicators for parameter types.
                while (($tokens[$afterType]['code'] === \T_BITWISE_AND
                    || $tokens[$afterType]['code'] === \T_ELLIPSIS
                    || isset(Tokens::$emptyTokens[$tokens[$afterType]['code']]) === true)
                    && ($afterType + 1) < $phpcsFile->numTokens
                ) {
                    ++$afterType;
                }
            }

            if ($tokens[$afterType]['code'] === \T_VARIABLE) {
                if (Scopes::isOOProperty($phpcsFile, $afterType) === true) {
                    // Union separator in a property type.
                    return true;
                }

                if (Parentheses::lastOwnerIn($phpcsFile, $stackPtr, $funcDeclTokens) !== false) {
                    // Union separator for a parameter type.
                    return true;
                }

                /*
                 * This may be an arrow function in combination with PHPCS < 3.5.3.
                 * Even when on PHP 7.4, the parentheses won't have an owner yet, so the previous
                 * condition will fall through to this one.
                 */
                $lastOpen = Parentheses::getLastOpener($phpcsFile, $stackPtr);
                if ($lastOpen !== false) {
                    $maybeArrow = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($lastOpen - 1), null, true);
                    if ($maybeArrow !== false
                        && FunctionDeclarations::isArrowFunction($phpcsFile, $maybeArrow) === true
                    ) {
                        return true;
                    }
                }
            }
        }

        /*
         * Check if it's a union separator in a return type.
         */
        $ignore = Collections::returnTypeTokensBC() + Tokens::$emptyTokens;

        $beforeType = $phpcsFile->findPrevious($ignore, ($stackPtr - 1), null, true);

        // Union types cannot be nullable, but the function should be parse error tolerant.
        if ($beforeType !== false
            && ($tokens[$beforeType]['type'] === 'T_NULLABLE'
            // Handle nullable tokens in PHPCS < 2.8.0 and with arrow functions in PHPCS 2.8.0 - 2.9.0.
            || (\version_compare(Helper::getVersion(), '2.9.1', '<') === true
                && $tokens[$beforeType]['code'] === \T_INLINE_THEN))
        ) {
            $beforeType = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($beforeType - 1), null, true);
        }

        if ($beforeType !== false
            && ($tokens[$beforeType]['code'] === \T_COLON
            // Handle colon mistokenization in various PHPCS versions < 3.5.3.
            || $tokens[$beforeType]['code'] === \T_INLINE_ELSE)
        ) {
            $afterType = $phpcsFile->findNext($ignore, ($stackPtr + 1), null, true, null, true);
            if ($afterType !== false) {
                if ($tokens[$afterType]['code'] === \T_SEMICOLON) {
                    // Union separator in a return type in an abstract or interface method.
                    return true;
                }

                if (isset($tokens[$afterType]['scope_condition']) === true
                    && isset($funcDeclTokens[$tokens[$tokens[$afterType]['scope_condition']]['code']])
                ) {
                    // Union separator in a return type for a function, closure or arrow function.
                    return true;
                }

                $closeParens = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($beforeType - 1), null, true);

                /*
                 * This may be a return type using the namespace keyword as an operator in
                 * combination with PHPCS < 3.5.7 in which the scope indexes won't be set.
                 * {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/3066}
                 */
                if ($tokens[$afterType]['code'] === \T_OPEN_CURLY_BRACKET) {
                    if ($closeParens !== false
                        && $tokens[$closeParens]['code'] === \T_CLOSE_PARENTHESIS
                        && isset($tokens[$closeParens]['parenthesis_owner']) === true
                        && isset($funcDeclTokens[$tokens[$tokens[$closeParens]['parenthesis_owner']]['code']]) === true
                    ) {
                        return true;
                    }
                }

                /*
                 * This may be an arrow function in combination with PHPCS < 3.5.3.
                 * Even when on PHP 7.4, the double arrow won't have the scope condition set yet,
                 * so the previous conditions will fall through to this one.
                 */
                if ($tokens[$afterType]['code'] === \T_DOUBLE_ARROW) {
                    if ($closeParens !== false
                        && $tokens[$closeParens]['code'] === \T_CLOSE_PARENTHESIS
                        && isset($tokens[$closeParens]['parenthesis_opener'])
                    ) {
                        $beforeOpen = $phpcsFile->findPrevious(
                            Tokens::$emptyTokens,
                            ($tokens[$closeParens]['parenthesis_opener'] - 1),
                            null,
                            true
                        );

                        if ($beforeOpen !== false
                            && FunctionDeclarations::isArrowFunction($phpcsFile, $beforeOpen) === true
                        ) {
                            // Union separator in a return type for an arrow function.
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Determine whether a token is (part of) a nullsafe object operator.
     *
     * Helper method for PHP < 8.0 in combination with PHPCS versions in which the
     * `T_NULLSAFE_OBJECT_OPERATOR` token is not yet backfilled.
     * PHPCS backfills the token as of PHPCS 3.5.7.
     *
     * @since 1.0.0-alpha4
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the T_INLINE_THEN or T_OBJECT_OPERATOR
     *                                               token in the stack.
     *
     * @return bool `TRUE` if nullsafe object operator; or `FALSE` otherwise.
     */
    public static function isNullsafeObjectOperator(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        /*
         * Safeguard in case this method is used on PHP >= 8.0 or PHPCS >= 3.5.7
         * and the nullsafe object operator would be passed.
         */
        if ($tokens[$stackPtr]['type'] === 'T_NULLSAFE_OBJECT_OPERATOR') {
            return true;
        }

        if (isset(Collections::nullsafeObjectOperatorBC()[$tokens[$stackPtr]['code']]) === false) {
            return false;
        }

        /*
         * Note: not bypassing empty tokens as whitespace and comments are not allowed
         * within an operator.
         */
        if ($tokens[$stackPtr]['code'] === \T_INLINE_THEN) {
            if (isset($tokens[$stackPtr + 1]) && $tokens[$stackPtr + 1]['code'] === \T_OBJECT_OPERATOR) {
                return true;
            }
        }

        if ($tokens[$stackPtr]['code'] === \T_OBJECT_OPERATOR) {
            if (isset($tokens[$stackPtr - 1])  && $tokens[$stackPtr - 1]['code'] === \T_INLINE_THEN) {
                return true;
            }
        }

        // Not a nullsafe object operator token.
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
            || $tokens[$prev]['type'] === 'T_FN_ARROW'
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
