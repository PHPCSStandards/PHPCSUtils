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
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\GetTokensAsString;
use PHPCSUtils\Utils\NamingConventions;

/**
 * Utility functions to retrieve information about parameters passed to function calls,
 * class instantiations, array declarations, isset and unset constructs.
 *
 * @since 1.0.0
 */
class PassedParameters
{

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
     * - If passed a `T_STRING`, `T_NAME_FULLY_QUALIFIED`, `T_NAME_RELATIVE`, `T_NAME_QUALIFIED`
     *   or `T_VARIABLE` stack pointer, it will treat it as a function call.
     *   If a `T_STRING` or `T_VARIABLE` which is *not* a function call is passed, the behaviour is
     *   undetermined.
     * - If passed a `T_ANON_CLASS` stack pointer, it will accept it as a class instantiation.
     * - If passed a `T_SELF` or `T_STATIC` stack pointer, it will accept it as a
     *   class instantiation function call when used like `new self()`.
     * - If passed a `T_ARRAY` or `T_OPEN_SHORT_ARRAY` stack pointer, it will detect
     *   whether the array has values or is empty.
     * - If passed a `T_ISSET` or `T_UNSET` stack pointer, it will detect whether those
     *   language constructs have "parameters".
     *
     * @since 1.0.0
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokenization.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile    The file where this token was found.
     * @param int                         $stackPtr     The position of the `T_STRING`, PHP 8.0 identifier
     *                                                  name token, `T_VARIABLE`, `T_ARRAY`, `T_OPEN_SHORT_ARRAY`,
     *                                                  `T_ISSET`, or `T_UNSET` token.
     * @param true|null                   $isShortArray Optional. Short-circuit the short array check for
     *                                                  `T_OPEN_SHORT_ARRAY` tokens if it isn't necessary.
     *                                                  Efficiency tweak for when this has already been established,
     *                                                  Use with EXTREME care.
     *
     * @return bool
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the token passed is not one of the
     *                                                      accepted types or doesn't exist.
     */
    public static function hasParameters(File $phpcsFile, $stackPtr, $isShortArray = null)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || isset(Collections::parameterPassingTokens()[$tokens[$stackPtr]['code']]) === false
        ) {
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
            && $isShortArray !== true
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
     * @since 1.0.0-alpha4 Added support for PHP 8.0 function calls with named arguments by
     *                     introducing the new `'name_start'`, `'name_end'` and `'name'` index keys.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile    The file where this token was found.
     * @param int                         $stackPtr     The position of the `T_STRING`, PHP 8.0 identifier
     *                                                  name token, `T_VARIABLE`, `T_ARRAY`, `T_OPEN_SHORT_ARRAY`,
     *                                                  `T_ISSET`, or `T_UNSET` token.
     * @param int                         $limit        Optional. Limit the parameter retrieval to the first #
     *                                                  parameters/array entries.
     * @param true|null                   $isShortArray Optional. Short-circuit the short array check for
     *                                                  `T_OPEN_SHORT_ARRAY` tokens if it isn't necessary.
     *                                                  Efficiency tweak for when this has already been established,
     *                                                  Use with EXTREME care.
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
     *               For function calls passing named arguments, the format is as follows:
     *               ```php
     *               1 => array(
     *                 'name_start' => int,    // The stack pointer to the first token in the parameter name.
     *                 'name_end'   => int,    // The stack pointer to the last token in the parameter name.
     *                                         // This will normally be the colon, but may be different in
     *                                         // PHPCS versions prior to the version adding support for
     *                                         // named parameters (PHPCS 3.6.0).
     *                 'name'       => string, // The parameter name as a string (without the colon).
     *                 'start'      => int,    // The stack pointer to the first token in the parameter value.
     *                 'end'        => int,    // The stack pointer to the last token in the parameter value.
     *                 'raw'        => string, // A string with the contents of all tokens between `start` and `end`.
     *                 'clean'      => string, // Same as `raw`, but all comment tokens have been stripped out.
     *               )
     *               ```
     *               The `'start'`, `'end'`, `'raw'` and `'clean'` indexes will always contain just and only
     *               information on the parameter value.
     *               _Note: The array starts at index 1._
     *               If no parameters/array items are found, an empty array will be returned.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the token passed is not one of the
     *                                                      accepted types or doesn't exist.
     */
    public static function getParameters(File $phpcsFile, $stackPtr, $limit = 0, $isShortArray = null)
    {
        if (self::hasParameters($phpcsFile, $stackPtr, $isShortArray) === false) {
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

        $mayHaveNames = (isset(Collections::functionCallTokens()[$tokens[$stackPtr]['code']]) === true);

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
            $paramEnd = ($nextComma - 1);

            if ($mayHaveNames === true) {
                $firstNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $paramStart, ($paramEnd + 1), true);
                if ($firstNonEmpty !== $paramEnd) {
                    /*
                     * BC: Prior to support for named parameters being added to PHPCS in PHPCS 3.6.0, the
                     * parameter name + the colon would in most cases be tokenized as one token: T_GOTO_LABEL.
                     */
                    if ($tokens[$firstNonEmpty]['code'] === \T_GOTO_LABEL) {
                        $parameters[$cnt]['name_start'] = $paramStart;
                        $parameters[$cnt]['name_end']   = $firstNonEmpty;
                        $parameters[$cnt]['name']       = \substr($tokens[$firstNonEmpty]['content'], 0, -1);
                        $paramStart                     = ($firstNonEmpty + 1);
                    } else {
                        // PHPCS 3.6.0 and select situations in PHPCS < 3.6.0.
                        $secondNonEmpty = $phpcsFile->findNext(
                            Tokens::$emptyTokens,
                            ($firstNonEmpty + 1),
                            ($paramEnd + 1),
                            true
                        );

                        /*
                         * BC: Checking the content of the colon token instead of the token type as in PHPCS < 3.6.0
                         * the colon _may_ be tokenized as `T_STRING` or even `T_INLINE_ELSE`.
                         */
                        if ($tokens[$secondNonEmpty]['content'] === ':'
                            && ($tokens[$firstNonEmpty]['type'] === 'T_PARAM_NAME'
                            || NamingConventions::isValidIdentifierName($tokens[$firstNonEmpty]['content']) === true)
                        ) {
                            $parameters[$cnt]['name_start'] = $paramStart;
                            $parameters[$cnt]['name_end']   = $secondNonEmpty;
                            $parameters[$cnt]['name']       = $tokens[$firstNonEmpty]['content'];
                            $paramStart                     = ($secondNonEmpty + 1);
                        }
                    }
                }
            }

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

            // Stop if there is a valid limit and the limit has been reached.
            if (\is_int($limit) && $limit > 0 && $cnt === $limit) {
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
     * @see PassedParameters::getParameterFromStack() For when the parameter stack of a function call is
     *                                                already retrieved.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the `T_STRING`, PHP 8.0 identifier
     *                                                 name token, `T_VARIABLE`, `T_ARRAY`, `T_OPEN_SHORT_ARRAY`,
     *                                                 `T_ISSET`, or `T_UNSET` token.
     * @param int                         $paramOffset The 1-based index position of the parameter to retrieve.
     * @param string|string[]             $paramNames  Optional. Either the name of the target parameter
     *                                                 to retrieve as a string or an array of names for the
     *                                                 same target parameter.
     *                                                 Only relevant for function calls.
     *                                                 An arrays of names is supported to allow for functions
     *                                                 for which the parameter names have undergone name
     *                                                 changes over time.
     *                                                 When specified, the name will take precedence over the
     *                                                 offset.
     *                                                 For PHP 8 support, it is STRONGLY recommended to
     *                                                 always pass both the offset as well as the parameter
     *                                                 name when examining function calls.
     *
     * @return array|false Array with information on the parameter/array item at the specified offset,
     *                     or with the specified name.
     *                     Or `FALSE` if the specified parameter/array item is not found.
     *                     See {@see PassedParameters::getParameters()} for the format of the returned
     *                     (single-dimensional) array.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the token passed is not one of the
     *                                                      accepted types or doesn't exist.
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If a function call parameter is requested and
     *                                                      the `$paramName` parameter is not passed.
     */
    public static function getParameter(File $phpcsFile, $stackPtr, $paramOffset, $paramNames = [])
    {
        $tokens = $phpcsFile->getTokens();

        if (empty($paramNames) === true) {
            $parameters = self::getParameters($phpcsFile, $stackPtr, $paramOffset);
        } else {
            $parameters = self::getParameters($phpcsFile, $stackPtr);
        }

        /*
         * Non-function calls.
         */
        if (isset(Collections::functionCallTokens()[$tokens[$stackPtr]['code']]) === false) {
            if (isset($parameters[$paramOffset]) === true) {
                return $parameters[$paramOffset];
            }

            return false;
        }

        /*
         * Function calls.
         */
        return self::getParameterFromStack($parameters, $paramOffset, $paramNames);
    }

    /**
     * Count the number of parameters which have been passed.
     *
     * See {@see PassedParameters::hasParameters()} for information on the supported constructs.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the `T_STRING`, PHP 8.0 identifier
     *                                               name token, `T_VARIABLE`, `T_ARRAY`, `T_OPEN_SHORT_ARRAY`,
     *                                               `T_ISSET`, or `T_UNSET` token.
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

    /**
     * Get information on a specific function call parameter passed.
     *
     * This is an efficiency method to correcty handle positional versus named parameters
     * for function calls when multiple parameters need to be examined.
     *
     * See {@see PassedParameters::hasParameters()} for information on the supported constructs.
     *
     * @since 1.0.0
     *
     * @param array           $parameters  The output of a previous call to {@see PassedParameters::getParameters()}.
     * @param int             $paramOffset The 1-based index position of the parameter to retrieve.
     * @param string|string[] $paramNames  Either the name of the target parameter to retrieve
     *                                     as a string or an array of names for the same target parameter.
     *                                     An arrays of names is supported to allow for functions
     *                                     for which the parameter names have undergone name
     *                                     changes over time.
     *                                     The name will take precedence over the offset.
     *
     * @return array|false Array with information on the parameter at the specified offset,
     *                     or with the specified name.
     *                     Or `FALSE` if the specified parameter is not found.
     *                     See {@see PassedParameters::getParameters()} for the format of the returned
     *                     (single-dimensional) array.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the `$paramNames` parameter is not passed
     *                                                      and the requested parameter was not passed
     *                                                      as a positional parameter in the function call
     *                                                      being examined.
     */
    public static function getParameterFromStack(array $parameters, $paramOffset, $paramNames)
    {
        if (empty($parameters) === true) {
            return false;
        }

        // First check for positional parameters.
        if (isset($parameters[$paramOffset]) === true
            && isset($parameters[$paramOffset]['name']) === false
        ) {
            return $parameters[$paramOffset];
        }

        if (empty($paramNames) === true) {
            throw new RuntimeException(
                'To allow for support for PHP 8 named parameters, the $paramNames parameter must be passed.'
            );
        }

        $paramNames = \array_flip((array) $paramNames);

        // Next check if a named parameter was passed with the specified name.
        foreach ($parameters as $paramDetails) {
            if (isset($paramDetails['name']) === false) {
                continue;
            }

            if (isset($paramNames[$paramDetails['name']]) === true) {
                return $paramDetails;
            }
        }

        return false;
    }
}
