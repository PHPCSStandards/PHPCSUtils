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
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\GetTokensAsString;

/**
 * Utility functions to retrieve information about parameters passed to function calls,
 * array declarations, isset and unset constructs.
 *
 * @since 1.0.0
 */
class PassedParameters
{

    /**
     * The token types these methods can handle.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <irrelevant>
     */
    private static $allowedConstructs = [
        \T_STRING              => true,
        \T_VARIABLE            => true,
        \T_SELF                => true,
        \T_STATIC              => true,
        \T_ARRAY               => true,
        \T_OPEN_SHORT_ARRAY    => true,
        \T_ISSET               => true,
        \T_UNSET               => true,
        // BC for various short array tokenizer issues. See the Arrays class for more details.
        \T_OPEN_SQUARE_BRACKET => true,
    ];

    /**
     * Tokens which are considered stop point, either because they are the end
     * of the parameter (comma) or because we need to skip over them.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    private static $callParsingStopPoints = [
        \T_COMMA                => \T_COMMA,
        \T_OPEN_SHORT_ARRAY     => \T_OPEN_SHORT_ARRAY,
        \T_OPEN_SQUARE_BRACKET  => \T_OPEN_SQUARE_BRACKET,
        \T_OPEN_PARENTHESIS     => \T_OPEN_PARENTHESIS,
        \T_DOC_COMMENT_OPEN_TAG => \T_DOC_COMMENT_OPEN_TAG,
    ];

    /**
     * Checks if any parameters have been passed.
     *
     * - If passed a `T_STRING` or `T_VARIABLE` stack pointer, it will treat it as a function call.
     *   If a `T_STRING` or `T_VARIABLE` which is *not* a function call is passed, the behaviour is
     *   undetermined.
     * - If passed a `T_SELF` or `T_STATIC` stack pointer, it will accept it as a
     *   function call when used like `new self()`.
     * - If passed a `T_ARRAY` or `T_OPEN_SHORT_ARRAY` stack pointer, it will detect
     *   whether the array has values or is empty.
     * - If passed a `T_ISSET` or `T_UNSET` stack pointer, it will detect whether those
     *   language constructs have "parameters".
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the `T_STRING`, `T_VARIABLE`, `T_ARRAY`,
     *                                               `T_OPEN_SHORT_ARRAY`, `T_ISSET`, or `T_UNSET` token.
     *
     * @return bool
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the token passed is not one of the
     *                                                      accepted types or doesn't exist.
     */
    public static function hasParameters(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr], self::$allowedConstructs[$tokens[$stackPtr]['code']]) === false) {
            throw new RuntimeException(
                'The hasParameters() method expects a function call, array, isset or unset token to be passed.'
            );
        }

        if ($tokens[$stackPtr]['code'] === \T_SELF || $tokens[$stackPtr]['code'] === \T_STATIC) {
            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$prev]['code'] !== \T_NEW) {
                throw new RuntimeException(
                    'The hasParameters() method expects a function call, array, isset or unset token to be passed.'
                );
            }
        }

        if (($tokens[$stackPtr]['code'] === \T_OPEN_SHORT_ARRAY
            || $tokens[$stackPtr]['code'] === \T_OPEN_SQUARE_BRACKET)
            && Arrays::isShortArray($phpcsFile, $stackPtr) === false
        ) {
            throw new RuntimeException(
                'The hasParameters() method expects a function call, array, isset or unset token to be passed.'
            );
        }

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false) {
            return false;
        }

        // Deal with short array syntax.
        if ($tokens[$stackPtr]['code'] === \T_OPEN_SHORT_ARRAY
            || $tokens[$stackPtr]['code'] === \T_OPEN_SQUARE_BRACKET
        ) {
            if ($next === $tokens[$stackPtr]['bracket_closer']) {
                // No parameters.
                return false;
            }

            return true;
        }

        // Deal with function calls, long arrays, isset and unset.
        // Next non-empty token should be the open parenthesis.
        if ($tokens[$next]['code'] !== \T_OPEN_PARENTHESIS) {
            return false;
        }

        if (isset($tokens[$next]['parenthesis_closer']) === false) {
            return false;
        }

        $closeParenthesis = $tokens[$next]['parenthesis_closer'];
        $nextNextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), ($closeParenthesis + 1), true);

        if ($nextNextNonEmpty === $closeParenthesis) {
            // No parameters.
            return false;
        }

        return true;
    }

    /**
     * Get information on all parameters passed.
     *
     * See {@see PassedParameters::hasParameters()} for information on the supported constructs.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the `T_STRING`, `T_VARIABLE`, `T_ARRAY`,
     *                                               `T_OPEN_SHORT_ARRAY`, `T_ISSET`, or `T_UNSET` token.
     *
     * @return array A multi-dimentional array information on each parameter/array item.
     *               The information gathered about each parameter/array item is in the following format:
     *               ```php
     *               1 => array(
     *                 'start' => int,    // The stack pointer to the first token in the parameter/array item.
     *                 'end'   => int,    // The stack pointer to the last token in the parameter/array item.
     *                 'raw'   => string, // A string with the contents of all tokens between `start` and `end`.
     *                 'clean' => string, // Same as `raw`, but all comment tokens have been stripped out.
     *               )
     *               ```
     *               _Note: The array starts at index 1._
     *               If no parameters/array items are found, an empty array will be returned.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the token passed is not one of the
     *                                                      accepted types or doesn't exist.
     */
    public static function getParameters(File $phpcsFile, $stackPtr)
    {
        if (self::hasParameters($phpcsFile, $stackPtr) === false) {
            return [];
        }

        // Ok, we know we have a valid token with parameters and valid open & close brackets/parenthesis.
        $tokens = $phpcsFile->getTokens();

        // Mark the beginning and end tokens.
        if ($tokens[$stackPtr]['code'] === \T_OPEN_SHORT_ARRAY
            || $tokens[$stackPtr]['code'] === \T_OPEN_SQUARE_BRACKET
        ) {
            $opener = $stackPtr;
            $closer = $tokens[$stackPtr]['bracket_closer'];
        } else {
            $opener = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            $closer = $tokens[$opener]['parenthesis_closer'];
        }

        $parameters   = [];
        $nextComma    = $opener;
        $paramStart   = ($opener + 1);
        $cnt          = 1;
        $stopPoints   = self::$callParsingStopPoints + Tokens::$scopeOpeners;
        $stopPoints[] = $tokens[$closer]['code'];

        while (($nextComma = $phpcsFile->findNext($stopPoints, ($nextComma + 1), ($closer + 1))) !== false) {
            // Ignore anything within square brackets.
            if (isset($tokens[$nextComma]['bracket_opener'], $tokens[$nextComma]['bracket_closer'])
                && $nextComma === $tokens[$nextComma]['bracket_opener']
            ) {
                $nextComma = $tokens[$nextComma]['bracket_closer'];
                continue;
            }

            // Skip past nested arrays, function calls and arbitrary groupings.
            if ($tokens[$nextComma]['code'] === \T_OPEN_PARENTHESIS
                && isset($tokens[$nextComma]['parenthesis_closer'])
            ) {
                $nextComma = $tokens[$nextComma]['parenthesis_closer'];
                continue;
            }

            // Skip past closures, anonymous classes and anything else scope related.
            if (isset($tokens[$nextComma]['scope_condition'], $tokens[$nextComma]['scope_closer'])
                && $tokens[$nextComma]['scope_condition'] === $nextComma
            ) {
                $nextComma = $tokens[$nextComma]['scope_closer'];
                continue;
            }

            // Skip over potentially large docblocks.
            if ($tokens[$nextComma]['code'] === \T_DOC_COMMENT_OPEN_TAG
                && isset($tokens[$nextComma]['comment_closer'])
            ) {
                $nextComma = $tokens[$nextComma]['comment_closer'];
                continue;
            }

            if ($tokens[$nextComma]['code'] !== \T_COMMA
                && $tokens[$nextComma]['code'] !== $tokens[$closer]['code']
            ) {
                // Just in case.
                continue;
            }

            // Ok, we've reached the end of the parameter.
            $paramEnd                  = ($nextComma - 1);
            $parameters[$cnt]['start'] = $paramStart;
            $parameters[$cnt]['end']   = $paramEnd;
            $parameters[$cnt]['raw']   = \trim(GetTokensAsString::normal($phpcsFile, $paramStart, $paramEnd));
            $parameters[$cnt]['clean'] = \trim(GetTokensAsString::noComments($phpcsFile, $paramStart, $paramEnd));

            // Check if there are more tokens before the closing parenthesis.
            // Prevents function calls with trailing comma's from setting an extra parameter:
            // `functionCall( $param1, $param2, );`.
            $hasNextParam = $phpcsFile->findNext(
                Tokens::$emptyTokens,
                ($nextComma + 1),
                $closer,
                true
            );
            if ($hasNextParam === false) {
                break;
            }

            // Prepare for the next parameter.
            $paramStart = ($nextComma + 1);
            $cnt++;
        }

        return $parameters;
    }

    /**
     * Get information on a specific parameter passed.
     *
     * See {@see PassedParameters::hasParameters()} for information on the supported constructs.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the `T_STRING`, `T_VARIABLE`, `T_ARRAY`,
     *                                                 `T_OPEN_SHORT_ARRAY`, `T_ISSET` or `T_UNSET` token.
     * @param int                         $paramOffset The 1-based index position of the parameter to retrieve.
     *
     * @return array|false Array with information on the parameter/array item at the specified offset.
     *                     Or `FALSE` if the specified parameter/array item is not found.
     *                     The format of the return value is:
     *                     ```php
     *                     array(
     *                       'start' => int,    // The stack pointer to the first token in the parameter/array item.
     *                       'end'   => int,    // The stack pointer to the last token in the parameter/array item.
     *                       'raw'   => string, // A string with the contents of all tokens between `start` and `end`.
     *                       'clean' => string, // Same as `raw`, but all comment tokens have been stripped out.
     *                     )
     *                     ```
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the token passed is not one of the
     *                                                      accepted types or doesn't exist.
     */
    public static function getParameter(File $phpcsFile, $stackPtr, $paramOffset)
    {
        $parameters = self::getParameters($phpcsFile, $stackPtr);

        if (isset($parameters[$paramOffset]) === false) {
            return false;
        }

        return $parameters[$paramOffset];
    }

    /**
     * Count the number of parameters which have been passed.
     *
     * See {@see PassedParameters::hasParameters()} for information on the supported constructs.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the `T_STRING`, `T_VARIABLE`, `T_ARRAY`,
     *                                               `T_OPEN_SHORT_ARRAY`, `T_ISSET` or `T_UNSET` token.
     *
     * @return int
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the token passed is not one of the
     *                                                      accepted types or doesn't exist.
     */
    public static function getParameterCount(File $phpcsFile, $stackPtr)
    {
        if (self::hasParameters($phpcsFile, $stackPtr) === false) {
            return 0;
        }

        return \count(self::getParameters($phpcsFile, $stackPtr));
    }
}
