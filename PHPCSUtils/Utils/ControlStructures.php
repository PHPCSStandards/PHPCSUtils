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

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;

/**
 * Utility functions for use when examining control structures.
 *
 * @since 1.0.0
 */
class ControlStructures
{

    /**
     * Check whether a control structure has a body.
     *
     * Some control structures - `while`, `for` and `declare` - can be declared without a body, like:
     * ```php
     * while (++$i < 10);
     * ```
     *
     * All other control structures will always have a body, though the body may be empty, where "empty" means:
     * no _code_ is found in the body. If a control structure body only contains a comment, it will be
     * regarded as empty.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile  The file being scanned.
     * @param int                         $stackPtr   The position of the token we are checking.
     * @param bool                        $allowEmpty Whether a control structure with an empty body should
     *                                                still be considered as having a body.
     *                                                Defaults to `true`.
     *
     * @return bool `TRUE` when the control structure has a body, or in case `$allowEmpty` is set to `FALSE`:
     *              when it has a non-empty body.
     *              `FALSE` in all other cases, including when a non-control structure token has been passed.
     */
    public static function hasBody(File $phpcsFile, $stackPtr, $allowEmpty = true)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false
            || isset(Collections::$controlStructureTokens[$tokens[$stackPtr]['code']]) === false
        ) {
            return false;
        }

        // Handle `else if`.
        if ($tokens[$stackPtr]['code'] === \T_ELSE && isset($tokens[$stackPtr]['scope_opener']) === false) {
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($next !== false && $tokens[$next]['code'] === \T_IF) {
                $stackPtr = $next;
            }
        }

        // Deal with declare alternative syntax without scope opener.
        if ($tokens[$stackPtr]['code'] === \T_DECLARE && isset($tokens[$stackPtr]['scope_opener']) === false) {
            $declareOpenClose = self::getDeclareScopeOpenClose($phpcsFile, $stackPtr);
            if ($declareOpenClose !== false) {
                // Set the opener + closer in the tokens array. This will only affect our local copy.
                $tokens[$stackPtr]['scope_opener'] = $declareOpenClose['opener'];
                $tokens[$stackPtr]['scope_closer'] = $declareOpenClose['closer'];
            }
        }

        /*
         * The scope markers are set. This is the simplest situation.
         */
        if (isset($tokens[$stackPtr]['scope_opener']) === true) {
            if ($allowEmpty === true) {
                return true;
            }

            // Check whether the body is empty.
            $start = ($tokens[$stackPtr]['scope_opener'] + 1);
            $end   = ($phpcsFile->numTokens + 1);
            if (isset($tokens[$stackPtr]['scope_closer']) === true) {
                $end = $tokens[$stackPtr]['scope_closer'];
            }

            $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $start, $end, true);
            if ($nextNonEmpty !== false) {
                return true;
            }

            return false;
        }

        /*
         * Control structure without scope markers.
         * Either single line statement or inline control structure.
         *
         * - Single line statement doesn't have a body and is therefore always empty.
         * - Inline control structure has to have a body and can never be empty.
         *
         * This code also needs to take live coding into account where a scope opener is found, but
         * no scope closer.
         */
        $searchStart = ($stackPtr + 1);
        if (isset($tokens[$stackPtr]['parenthesis_closer']) === true) {
            $searchStart = ($tokens[$stackPtr]['parenthesis_closer'] + 1);
        }

        $nextNonEmpty = $phpcsFile->findNext(
            Tokens::$emptyTokens,
            $searchStart,
            null,
            true
        );
        if ($nextNonEmpty === false || $tokens[$nextNonEmpty]['code'] === \T_SEMICOLON) {
            // Parse error or single line statement.
            return false;
        }

        if ($tokens[$nextNonEmpty]['code'] === \T_OPEN_CURLY_BRACKET) {
            if ($allowEmpty === true) {
                return true;
            }

            // Unrecognized scope opener due to parse error.
            $nextNext = $phpcsFile->findNext(
                Tokens::$emptyTokens,
                ($nextNonEmpty + 1),
                null,
                true
            );

            if ($nextNext === false) {
                return false;
            }

            return true;
        }

        return true;
    }

    /**
     * Check whether an IF or ELSE token is part of an "else if".
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the token we are checking.
     *
     * @return bool
     */
    public static function isElseIf(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] === \T_ELSEIF) {
            return true;
        }

        if ($tokens[$stackPtr]['code'] !== \T_ELSE && $tokens[$stackPtr]['code'] !== \T_IF) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] === \T_ELSE && isset($tokens[$stackPtr]['scope_opener']) === true) {
            return false;
        }

        switch ($tokens[$stackPtr]['code']) {
            case \T_ELSE:
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
                if ($next !== false && $tokens[$next]['code'] === \T_IF) {
                    return true;
                }
                break;

            case \T_IF:
                $previous = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
                if ($previous !== false && $tokens[$previous]['code'] === \T_ELSE) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * Get the scope opener and closer for a DECLARE statement.
     *
     * A `declare` statement can be:
     * - applied to the rest of the file, like `declare(ticks=1);`
     * - applied to a limited scope using curly braces;
     * - applied to a limited scope using the alternative control structure syntax.
     *
     * In the first case, the statement - correctly - won't have a scope opener/closer.
     * In the second case, the statement will have the scope opener/closer indexes.
     * In the last case, due to a bug in the PHPCS Tokenizer, it won't have the scope opener/closer indexes,
     * while it really should. This bug is fixed in PHPCS 3.5.4.
     *
     * In other words, if a sniff needs to support PHPCS < 3.5.4 and needs to take the alternative
     * control structure syntax into account, this method can be used to retrieve the
     * scope opener/closer for the declare statement.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/pull/2843 PHPCS PR #2843
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the token we are checking.
     *
     * @return array|false An array with the token pointers; or `FALSE` if not a `DECLARE` token
     *                     or if the opener/closer could not be determined.
     *                     The format of the array return value is:
     *                     ```php
     *                     array(
     *                       'opener' => integer, // Stack pointer to the scope opener.
     *                       'closer' => integer, // Stack pointer to the scope closer.
     *                     )
     *                     ```
     */
    public static function getDeclareScopeOpenClose(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || $tokens[$stackPtr]['code'] !== \T_DECLARE
        ) {
            return false;
        }

        if (isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer']) === true) {
            return [
                'opener' => $tokens[$stackPtr]['scope_opener'],
                'closer' => $tokens[$stackPtr]['scope_closer'],
            ];
        }

        $declareCount = 0;
        $opener       = null;
        $closer       = null;

        for ($i = $stackPtr; $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['code'] !== \T_DECLARE && $tokens[$i]['code'] !== \T_ENDDECLARE) {
                continue;
            }

            if ($tokens[$i]['code'] === \T_ENDDECLARE) {
                --$declareCount;

                if ($declareCount !== 0) {
                    continue;
                }

                // OK, we reached the target enddeclare.
                $closer = $i;
                break;
            }

            if ($tokens[$i]['code'] === \T_DECLARE) {
                ++$declareCount;

                // Find the scope opener
                if (isset($tokens[$i]['parenthesis_closer']) === false) {
                    // Parse error or live coding, nothing to do.
                    return false;
                }

                $scopeOpener = $phpcsFile->findNext(
                    Tokens::$emptyTokens,
                    ($tokens[$i]['parenthesis_closer'] + 1),
                    null,
                    true
                );

                if ($scopeOpener === false) {
                    // Live coding, nothing to do.
                    return false;
                }

                // Remember the scope opener for our target declare.
                if ($declareCount === 1) {
                    $opener = $scopeOpener;
                }

                $i = $scopeOpener;

                switch ($tokens[$scopeOpener]['code']) {
                    case \T_COLON:
                        // Nothing particular to do. Just continue the loop.
                        break;

                    case \T_OPEN_CURLY_BRACKET:
                        /*
                         * Live coding or nested declare statement with curlies.
                         */

                        if (isset($tokens[$scopeOpener]['scope_closer']) === false) {
                            // Live coding, nothing to do.
                            return false;
                        }

                        // Jump over the statement.
                        $i = $tokens[$scopeOpener]['scope_closer'];
                        --$declareCount;

                        break;

                    case \T_SEMICOLON:
                        // Nested single line declare statement.
                        --$declareCount;
                        break;

                    default:
                        // This is an unexpected token. Most likely a parse error. Bow out.
                        return false;
                }
            }

            if ($declareCount === 0) {
                break;
            }
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
     * Retrieve the exception(s) being caught in a CATCH condition.
     *
     * @since 1.0.0-alpha3
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokenization.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the token we are checking.
     *
     * @return array Array with information about the caught Exception(s).
     *               The returned array will contain the following information for
     *               each caught exception:
     *               ```php
     *               0 => array(
     *                 'type'           => string,  // The type declaration for the exception being caught.
     *                 'type_token'     => integer, // The stack pointer to the start of the type declaration.
     *                 'type_end_token' => integer, // The stack pointer to the end of the type declaration.
     *               )
     *               ```
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified `$stackPtr` is not of
     *                                                      type `T_CATCH` or doesn't exist.
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If no parenthesis opener or closer can be
     *                                                      determined (parse error).
     */
    public static function getCaughtExceptions(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || $tokens[$stackPtr]['code'] !== \T_CATCH
        ) {
            throw new RuntimeException('$stackPtr must be of type T_CATCH');
        }

        if (isset($tokens[$stackPtr]['parenthesis_opener'], $tokens[$stackPtr]['parenthesis_closer']) === false) {
            throw new RuntimeException('Parentheses opener/closer of the T_CATCH could not be determined');
        }

        $opener     = $tokens[$stackPtr]['parenthesis_opener'];
        $closer     = $tokens[$stackPtr]['parenthesis_closer'];
        $exceptions = [];

        $foundName  = '';
        $firstToken = null;
        $lastToken  = null;

        for ($i = ($opener + 1); $i <= $closer; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']])) {
                continue;
            }

            if (isset(Collections::namespacedNameTokens()[$tokens[$i]['code']]) === false) {
                // Add the current exception to the result array.
                $exceptions[] = [
                    'type'           => $foundName,
                    'type_token'     => $firstToken,
                    'type_end_token' => $lastToken,
                ];

                if ($tokens[$i]['code'] === \T_BITWISE_OR) {
                    // Multi-catch. Reset and continue.
                    $foundName  = '';
                    $firstToken = null;
                    $lastToken  = null;
                    continue;
                }

                break;
            }

            if (isset($firstToken) === false) {
                $firstToken = $i;
            }

            $foundName .= $tokens[$i]['content'];
            $lastToken  = $i;
        }

        return $exceptions;
    }
}
