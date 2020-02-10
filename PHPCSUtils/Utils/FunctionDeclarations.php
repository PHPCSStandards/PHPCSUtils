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
use PHPCSUtils\BackCompat\BCTokens;
use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Conditions;
use PHPCSUtils\Utils\GetTokensAsString;
use PHPCSUtils\Utils\ObjectDeclarations;
use PHPCSUtils\Utils\Scopes;
use PHPCSUtils\Utils\UseStatements;

/**
 * Utility functions for use when examining function declaration statements.
 *
 * @since 1.0.0 The `getProperties()` and the `getParameters()` methods are
 *              based on and inspired by respectively the `getMethodProperties()`
 *              and `getMethodParameters()` methods in the PHPCS native `File` class.
 *              Also see {@see \PHPCSUtils\BackCompat\BCFile}.
 */
class FunctionDeclarations
{

    /**
     * A list of all PHP magic functions.
     *
     * The array keys are the function names, the values as well, but without the double underscore.
     *
     * The function names are listed in lowercase as function names in PHP are case-insensitive
     * and comparisons against this list should therefore always be done in a case-insensitive manner.
     *
     * @since 1.0.0
     *
     * @var array <string> => <string>
     */
    public static $magicFunctions = [
        '__autoload' => 'autoload',
    ];

    /**
     * A list of all PHP magic methods.
     *
     * The array keys are the method names, the values as well, but without the double underscore.
     *
     * The method names are listed in lowercase as function names in PHP are case-insensitive
     * and comparisons against this list should therefore always be done in a case-insensitive manner.
     *
     * @since 1.0.0
     *
     * @var array <string> => <string>
     */
    public static $magicMethods = [
        '__construct'   => 'construct',
        '__destruct'    => 'destruct',
        '__call'        => 'call',
        '__callstatic'  => 'callstatic',
        '__get'         => 'get',
        '__set'         => 'set',
        '__isset'       => 'isset',
        '__unset'       => 'unset',
        '__sleep'       => 'sleep',
        '__wakeup'      => 'wakeup',
        '__tostring'    => 'tostring',
        '__set_state'   => 'set_state',
        '__clone'       => 'clone',
        '__invoke'      => 'invoke',
        '__debuginfo'   => 'debuginfo', // PHP 5.6.
        '__serialize'   => 'serialize', // PHP 7.4.
        '__unserialize' => 'unserialize', // PHP 7.4.
    ];

    /**
     * A list of all PHP native non-magic methods starting with a double underscore.
     *
     * These come from PHP modules such as SOAPClient.
     *
     * The array keys are the method names, the values the name of the PHP extension containing
     * the function.
     *
     * The method names are listed in lowercase as function names in PHP are case-insensitive
     * and comparisons against this list should therefore always be done in a case-insensitive manner.
     *
     * @since 1.0.0
     *
     * @var array <string> => <string>
     */
    public static $methodsDoubleUnderscore = [
        '__dorequest'              => 'SOAPClient',
        '__getcookies'             => 'SOAPClient',
        '__getfunctions'           => 'SOAPClient',
        '__getlastrequest'         => 'SOAPClient',
        '__getlastrequestheaders'  => 'SOAPClient',
        '__getlastresponse'        => 'SOAPClient',
        '__getlastresponseheaders' => 'SOAPClient',
        '__gettypes'               => 'SOAPClient',
        '__setcookie'              => 'SOAPClient',
        '__setlocation'            => 'SOAPClient',
        '__setsoapheaders'         => 'SOAPClient',
        '__soapcall'               => 'SOAPClient',
    ];

    /**
     * Tokens which can be the end token of an arrow function.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <true>
     */
    private static $arrowFunctionEndTokens = [
        \T_COLON                => true,
        \T_COMMA                => true,
        \T_SEMICOLON            => true,
        \T_CLOSE_PARENTHESIS    => true,
        \T_CLOSE_SQUARE_BRACKET => true,
        \T_CLOSE_CURLY_BRACKET  => true,
        \T_CLOSE_SHORT_ARRAY    => true,
        \T_OPEN_TAG             => true,
        \T_CLOSE_TAG            => true,
    ];

    /**
     * Returns the declaration name for a function.
     *
     * Alias for the {@see \PHPCSUtils\Utils\ObjectDeclarations::getName()} method.
     *
     * @codeCoverageIgnore
     *
     * @see \PHPCSUtils\BackCompat\BCFile::getDeclarationName() Original function.
     * @see \PHPCSUtils\Utils\ObjectDeclarations::getName()     PHPCSUtils native improved version.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the declaration token
     *                                               which declared the function.
     *
     * @return string|null The name of the function; or NULL if the passed token doesn't exist,
     *                     the function is anonymous or in case of a parse error/live coding.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not of type
     *                                                      T_FUNCTION, T_CLASS, T_TRAIT, or T_INTERFACE.
     */
    public static function getName(File $phpcsFile, $stackPtr)
    {
        return ObjectDeclarations::getName($phpcsFile, $stackPtr);
    }

    /**
     * Retrieves the visibility and implementation properties of a method.
     *
     * The format of the return value is:
     * <code>
     *   array(
     *    'scope'                 => 'public', // Public, private, or protected
     *    'scope_specified'       => true,     // TRUE if the scope keyword was found.
     *    'return_type'           => '',       // The return type of the method.
     *    'return_type_token'     => integer,  // The stack pointer to the start of the return type
     *                                         // or FALSE if there is no return type.
     *    'return_type_end_token' => integer,  // The stack pointer to the end of the return type
     *                                         // or FALSE if there is no return type.
     *    'nullable_return_type'  => false,    // TRUE if the return type is nullable.
     *    'is_abstract'           => false,    // TRUE if the abstract keyword was found.
     *    'is_final'              => false,    // TRUE if the final keyword was found.
     *    'is_static'             => false,    // TRUE if the static keyword was found.
     *    'has_body'              => false,    // TRUE if the method has a body
     *   );
     * </code>
     *
     * Main differences with the PHPCS version:
     * - Bugs fixed:
     *   - Handling of PHPCS annotations.
     *   - `has_body` index could be set to `true` for functions without body in the case of
     *      parse errors or live coding.
     * - Defensive coding against incorrect calls to this method.
     * - More efficient checking whether a function has a body.
     * - New `return_type_end_token` (int|false) array index.
     * - To allow for backward compatible handling of arrow functions, this method will also accept
     *   `T_STRING` tokens and examine them to check if these are arrow functions.
     *
     * @see \PHP_CodeSniffer\Files\File::getMethodProperties()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::getMethodProperties() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added BC support for PHP 7.4 arrow functions.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the function token to
     *                                               acquire the properties for.
     *
     * @return array
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_FUNCTION, T_CLOSURE, or T_FN token.
     */
    public static function getProperties(File $phpcsFile, $stackPtr)
    {
        $tokens         = $phpcsFile->getTokens();
        $arrowOpenClose = self::getArrowFunctionOpenClose($phpcsFile, $stackPtr);

        if (isset($tokens[$stackPtr]) === false
            || ($tokens[$stackPtr]['code'] !== \T_FUNCTION
                && $tokens[$stackPtr]['code'] !== \T_CLOSURE
                && $arrowOpenClose === [])
        ) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or T_FN');
        }

        if ($tokens[$stackPtr]['code'] === \T_FUNCTION) {
            $valid = Tokens::$methodPrefixes;
        } else {
            $valid = [\T_STATIC => \T_STATIC];
        }

        $valid += Tokens::$emptyTokens;

        $scope          = 'public';
        $scopeSpecified = false;
        $isAbstract     = false;
        $isFinal        = false;
        $isStatic       = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$tokens[$i]['code']]) === false) {
                break;
            }

            switch ($tokens[$i]['code']) {
                case \T_PUBLIC:
                    $scope          = 'public';
                    $scopeSpecified = true;
                    break;
                case \T_PRIVATE:
                    $scope          = 'private';
                    $scopeSpecified = true;
                    break;
                case \T_PROTECTED:
                    $scope          = 'protected';
                    $scopeSpecified = true;
                    break;
                case \T_ABSTRACT:
                    $isAbstract = true;
                    break;
                case \T_FINAL:
                    $isFinal = true;
                    break;
                case \T_STATIC:
                    $isStatic = true;
                    break;
            }
        }

        $returnType         = '';
        $returnTypeToken    = false;
        $returnTypeEndToken = false;
        $nullableReturnType = false;
        $hasBody            = false;

        $parenthesisCloser = null;
        if (isset($tokens[$stackPtr]['parenthesis_closer']) === true) {
            $parenthesisCloser = $tokens[$stackPtr]['parenthesis_closer'];
        } elseif ($arrowOpenClose !== [] && $arrowOpenClose['parenthesis_closer'] !== false) {
            // Arrow function in combination with PHP < 7.4 or PHPCS < 3.5.3.
            $parenthesisCloser = $arrowOpenClose['parenthesis_closer'];
        }

        if (isset($parenthesisCloser) === true) {
            $scopeOpener = null;
            if (isset($tokens[$stackPtr]['scope_opener']) === true) {
                $scopeOpener = $tokens[$stackPtr]['scope_opener'];
            } elseif ($arrowOpenClose !== [] && $arrowOpenClose['scope_opener'] !== false) {
                // Arrow function in combination with PHP < 7.4 or PHPCS < 3.5.3.
                $scopeOpener = $arrowOpenClose['scope_opener'];
            }

            for ($i = $parenthesisCloser; $i < $phpcsFile->numTokens; $i++) {
                if ($i === $scopeOpener) {
                    // End of function definition.
                    $hasBody = true;
                    break;
                }

                if ($scopeOpener === null && $tokens[$i]['code'] === \T_SEMICOLON) {
                    // End of abstract/interface function definition.
                    break;
                }

                if ($tokens[$i]['type'] === 'T_NULLABLE'
                    // Handle nullable tokens in PHPCS < 2.8.0.
                    || (\defined('T_NULLABLE') === false && $tokens[$i]['code'] === \T_INLINE_THEN)
                    // Handle nullable tokens with arrow functions in PHPCS 2.8.0 - 2.9.0.
                    || ($arrowOpenClose !== [] && $tokens[$i]['code'] === \T_INLINE_THEN
                        && \version_compare(Helper::getVersion(), '2.9.1', '<') === true)
                ) {
                    $nullableReturnType = true;
                }

                if (isset(Collections::$returnTypeTokens[$tokens[$i]['code']]) === true) {
                    if ($returnTypeToken === false) {
                        $returnTypeToken = $i;
                    }

                    $returnType        .= $tokens[$i]['content'];
                    $returnTypeEndToken = $i;
                }
            }
        }

        if ($returnType !== '' && $nullableReturnType === true) {
            $returnType = '?' . $returnType;
        }

        return [
            'scope'                 => $scope,
            'scope_specified'       => $scopeSpecified,
            'return_type'           => $returnType,
            'return_type_token'     => $returnTypeToken,
            'return_type_end_token' => $returnTypeEndToken,
            'nullable_return_type'  => $nullableReturnType,
            'is_abstract'           => $isAbstract,
            'is_final'              => $isFinal,
            'is_static'             => $isStatic,
            'has_body'              => $hasBody,
        ];
    }

    /**
     * Retrieves the method parameters for the specified function token.
     *
     * Also supports passing in a USE token for a closure use group.
     *
     * The returned array will contain the following information for each parameter:
     *
     * <code>
     *   0 => array(
     *         'name'                => '$var',  // The variable name.
     *         'token'               => integer, // The stack pointer to the variable name.
     *         'content'             => string,  // The full content of the variable definition.
     *         'pass_by_reference'   => boolean, // Is the variable passed by reference?
     *         'reference_token'     => integer, // The stack pointer to the reference operator
     *                                           // or FALSE if the param is not passed by reference.
     *         'variable_length'     => boolean, // Is the param of variable length through use of `...` ?
     *         'variadic_token'      => integer, // The stack pointer to the ... operator
     *                                           // or FALSE if the param is not variable length.
     *         'type_hint'           => string,  // The type hint for the variable.
     *         'type_hint_token'     => integer, // The stack pointer to the start of the type hint
     *                                           // or FALSE if there is no type hint.
     *         'type_hint_end_token' => integer, // The stack pointer to the end of the type hint
     *                                           // or FALSE if there is no type hint.
     *         'nullable_type'       => boolean, // TRUE if the var type is nullable.
     *         'comma_token'         => integer, // The stack pointer to the comma after the param
     *                                           // or FALSE if this is the last param.
     *        )
     * </code>
     *
     * Parameters with default values have the following additional array indexes:
     *         'default'             => string,  // The full content of the default value.
     *         'default_token'       => integer, // The stack pointer to the start of the default value.
     *         'default_equal_token' => integer, // The stack pointer to the equals sign.
     *
     * Main differences with the PHPCS version:
     * - Defensive coding against incorrect calls to this method.
     * - More efficient and more stable checking whether a T_USE token is a closure use.
     * - More efficient and more stable looping of the default value.
     * - Clearer exception message when a non-closure use token was passed to the function.
     * - To allow for backward compatible handling of arrow functions, this method will also accept
     *   `T_STRING` tokens and examine them to check if these are arrow functions.
     *
     * @see \PHP_CodeSniffer\Files\File::getMethodParameters()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::getMethodParameters() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added BC support for PHP 7.4 arrow functions.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the function token
     *                                               to acquire the parameters for.
     *
     * @return array
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is not of
     *                                                      type T_FUNCTION, T_CLOSURE, T_USE,
     *                                                      or T_FN.
     */
    public static function getParameters(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || ($tokens[$stackPtr]['code'] !== \T_FUNCTION
                && $tokens[$stackPtr]['code'] !== \T_CLOSURE
                && $tokens[$stackPtr]['code'] !== \T_USE
                && self::isArrowFunction($phpcsFile, $stackPtr) === false)
        ) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or T_USE or T_FN');
        }

        if ($tokens[$stackPtr]['code'] === \T_USE) {
            $opener = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($opener === false
                || $tokens[$opener]['code'] !== \T_OPEN_PARENTHESIS
                || UseStatements::isClosureUse($phpcsFile, $stackPtr) === false
            ) {
                throw new RuntimeException('$stackPtr was not a valid closure T_USE');
            }
        } elseif (isset(Collections::arrowFunctionTokensBC()[$tokens[$stackPtr]['code']]) === true) {
            /*
             * Arrow function in combination with PHP < 7.4 or PHPCS < 3.5.3.
             */
            $opener = $phpcsFile->findNext((Tokens::$emptyTokens + [\T_BITWISE_AND]), ($stackPtr + 1), null, true);
            if ($opener === false || $tokens[$opener]['code'] !== \T_OPEN_PARENTHESIS) {
                // Live coding or syntax error, so no params to find.
                return [];
            }
        } else {
            if (isset($tokens[$stackPtr]['parenthesis_opener']) === false) {
                // Live coding or syntax error, so no params to find.
                return [];
            }

            $opener = $tokens[$stackPtr]['parenthesis_opener'];
        }

        if (isset($tokens[$opener]['parenthesis_closer']) === false) {
            // Live coding or syntax error, so no params to find.
            return [];
        }

        $closer = $tokens[$opener]['parenthesis_closer'];

        $vars             = [];
        $currVar          = null;
        $paramStart       = ($opener + 1);
        $defaultStart     = null;
        $equalToken       = null;
        $paramCount       = 0;
        $passByReference  = false;
        $referenceToken   = false;
        $variableLength   = false;
        $variadicToken    = false;
        $typeHint         = '';
        $typeHintToken    = false;
        $typeHintEndToken = false;
        $nullableType     = false;

        for ($i = $paramStart; $i <= $closer; $i++) {
            // Changed from checking 'code' to 'type' to allow for T_NULLABLE not existing in PHPCS < 2.8.0.
            switch ($tokens[$i]['type']) {
                case 'T_BITWISE_AND':
                    $passByReference = true;
                    $referenceToken  = $i;
                    break;

                case 'T_VARIABLE':
                    $currVar = $i;
                    break;

                case 'T_ELLIPSIS':
                    $variableLength = true;
                    $variadicToken  = $i;
                    break;

                case 'T_ARRAY_HINT': // PHPCS < 3.3.0.
                case 'T_CALLABLE':
                case 'T_SELF':
                case 'T_PARENT':
                case 'T_STATIC': // Self and parent are valid, static invalid, but was probably intended as type hint.
                case 'T_STRING':
                case 'T_NS_SEPARATOR':
                    if ($typeHintToken === false) {
                        $typeHintToken = $i;
                    }

                    $typeHint        .= $tokens[$i]['content'];
                    $typeHintEndToken = $i;
                    break;

                case 'T_NULLABLE':
                case 'T_INLINE_THEN': // PHPCS < 2.8.0.
                    $nullableType     = true;
                    $typeHint        .= $tokens[$i]['content'];
                    $typeHintEndToken = $i;
                    break;

                case 'T_CLOSE_PARENTHESIS':
                case 'T_COMMA':
                    // If it's null, then there must be no parameters for this
                    // method.
                    if ($currVar === null) {
                        continue 2;
                    }

                    $vars[$paramCount]            = [];
                    $vars[$paramCount]['token']   = $currVar;
                    $vars[$paramCount]['name']    = $tokens[$currVar]['content'];
                    $vars[$paramCount]['content'] = \trim(
                        GetTokensAsString::normal($phpcsFile, $paramStart, ($i - 1))
                    );

                    if ($defaultStart !== null) {
                        $vars[$paramCount]['default']             = \trim(
                            GetTokensAsString::normal($phpcsFile, $defaultStart, ($i - 1))
                        );
                        $vars[$paramCount]['default_token']       = $defaultStart;
                        $vars[$paramCount]['default_equal_token'] = $equalToken;
                    }

                    $vars[$paramCount]['pass_by_reference']   = $passByReference;
                    $vars[$paramCount]['reference_token']     = $referenceToken;
                    $vars[$paramCount]['variable_length']     = $variableLength;
                    $vars[$paramCount]['variadic_token']      = $variadicToken;
                    $vars[$paramCount]['type_hint']           = $typeHint;
                    $vars[$paramCount]['type_hint_token']     = $typeHintToken;
                    $vars[$paramCount]['type_hint_end_token'] = $typeHintEndToken;
                    $vars[$paramCount]['nullable_type']       = $nullableType;

                    if ($tokens[$i]['code'] === \T_COMMA) {
                        $vars[$paramCount]['comma_token'] = $i;
                    } else {
                        $vars[$paramCount]['comma_token'] = false;
                    }

                    // Reset the vars, as we are about to process the next parameter.
                    $currVar          = null;
                    $paramStart       = ($i + 1);
                    $defaultStart     = null;
                    $equalToken       = null;
                    $passByReference  = false;
                    $referenceToken   = false;
                    $variableLength   = false;
                    $variadicToken    = false;
                    $typeHint         = '';
                    $typeHintToken    = false;
                    $typeHintEndToken = false;
                    $nullableType     = false;

                    $paramCount++;
                    break;

                case 'T_EQUAL':
                    $defaultStart = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), null, true);
                    $equalToken   = $i;

                    // Skip past everything in the default value before going into the next switch loop.
                    for ($j = ($i + 1); $j <= $closer; $j++) {
                        // Skip past array()'s et al as default values.
                        if (isset($tokens[$j]['parenthesis_opener'], $tokens[$j]['parenthesis_closer'])) {
                            $j = $tokens[$j]['parenthesis_closer'];

                            if ($j === $closer) {
                                // Found the end of the parameter.
                                break;
                            }

                            continue;
                        }

                        // Skip past short arrays et al as default values.
                        if (isset($tokens[$j]['bracket_opener'])) {
                            $j = $tokens[$j]['bracket_closer'];
                            continue;
                        }

                        if ($tokens[$j]['code'] === \T_COMMA) {
                            break;
                        }
                    }

                    $i = ($j - 1);
                    break;
            }
        }

        return $vars;
    }

    /**
     * Check if an arbitrary token is a PHP 7.4 arrow function keyword token.
     *
     * Helper function for backward-compatibility with PHP < 7.4 in combination with PHPCS < 3.5.3/4
     * in which the `T_FN` token is not yet backfilled.
     *
     * Note: While this function can determine whether a token should be regarded as `T_FN`, if the
     * token isn't a PHP native `T_FN` or backfilled `T_FN` token, the token will still not have
     * the `parenthesis_owner`, `parenthesis_opener`, `parenthesis_closer`, `scope_owner`
     * `scope_opener` or `scope_closer` keys assigned in the tokens array.
     * Use the `FunctionDeclarations::getArrowFunctionOpenClose()` utility method to retrieve
     * these when they're needed.
     *
     * @see \PHPCSUtils\Utils\FunctionDeclarations::getArrowFunctionOpenClose()
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The token to check. Typically a T_FN or
     *                                               T_STRING token as those are the only two
     *                                               tokens which can be the arrow function keyword.
     *
     * @return bool
     */
    public static function isArrowFunction(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['type'] === 'T_FN') {
            // Either PHP 7.4 or PHPCS 3.5.3+. Check if this is not a real function called "fn".
            $prevNonEmpty = $phpcsFile->findPrevious(
                Tokens::$emptyTokens + [\T_BITWISE_AND],
                ($stackPtr - 1),
                null,
                true
            );
            if ($tokens[$prevNonEmpty]['code'] === \T_FUNCTION) {
                return false;
            }

            return true;
        }

        if (\defined('T_FN') === true) {
            // If the token exists and isn't used, it's not an arrow function.
            return false;
        }

        if ($tokens[$stackPtr]['code'] !== \T_STRING
            || \strtolower($tokens[$stackPtr]['content']) !== 'fn'
        ) {
            return false;
        }

        $nextNonEmpty = $phpcsFile->findNext((Tokens::$emptyTokens + [\T_BITWISE_AND]), ($stackPtr + 1), null, true);
        if ($nextNonEmpty === false
            || ($tokens[$nextNonEmpty]['code'] === \T_OPEN_PARENTHESIS
            // Make sure it is not a real function called "fn".
            && (isset($tokens[$nextNonEmpty]['parenthesis_owner']) === false
            || $tokens[$tokens[$nextNonEmpty]['parenthesis_owner']]['code'] !== \T_FUNCTION))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve the parenthesis opener, parenthesis closer, the scope opener and the scope closer
     * for an arrow function.
     *
     * Helper function for backward-compatibility with PHP < 7.4 in combination with PHPCS < 3.5.3/4
     * in which the `T_FN` token is not yet backfilled and does not have parenthesis opener/closer
     * nor scope opener/closer indexes assigned in the `$tokens` array.
     *
     * Note: The backfill in PHPCS 3.5.3 is incomplete and this function will - in a limited set of
     * circumstances - not work on PHPCS 3.5.3.
     * As PHPCS 3.5.3 is not supported by PHPCSUtils due to the broken PHP 7.4 numeric literals backfill
     * anyway, this will not be fixed.
     *
     * @see \PHPCSUtils\Utils\FunctionDeclarations::isArrowFunction()
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The token to retrieve the opener/closers for.
     *                                               Typically a T_FN or T_STRING token as those are the
     *                                               only two tokens which can be the arrow function keyword.
     *
     * @return array An array with the token pointers or an empty array if this is not an arrow function.
     *               The format of the return value is:
     *               <code>
     *               array(
     *                 'parenthesis_opener' => integer|false, // Stack pointer or false if undetermined.
     *                 'parenthesis_closer' => integer|false, // Stack pointer or false if undetermined.
     *                 'scope_opener'       => integer|false, // Stack pointer or false if undetermined.
     *                 'scope_closer'       => integer|false, // Stack pointer or false if undetermined.
     *               )
     *               </code>
     */
    public static function getArrowFunctionOpenClose(File $phpcsFile, $stackPtr)
    {
        if (self::isArrowFunction($phpcsFile, $stackPtr) === false) {
            return [];
        }

        $returnValue = [
            'parenthesis_opener' => false,
            'parenthesis_closer' => false,
            'scope_opener'       => false,
            'scope_closer'       => false,
        ];

        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['type'] === 'T_FN'
            && \version_compare(Helper::getVersion(), '3.5.3', '>=') === true
        ) {
            if (isset($tokens[$stackPtr]['parenthesis_opener']) === true) {
                $returnValue['parenthesis_opener'] = $tokens[$stackPtr]['parenthesis_opener'];
            }

            if (isset($tokens[$stackPtr]['parenthesis_closer']) === true) {
                $returnValue['parenthesis_closer'] = $tokens[$stackPtr]['parenthesis_closer'];
            }

            if (isset($tokens[$stackPtr]['scope_opener']) === true) {
                $returnValue['scope_opener'] = $tokens[$stackPtr]['scope_opener'];
            }

            if (isset($tokens[$stackPtr]['scope_closer']) === true) {
                $returnValue['scope_closer'] = $tokens[$stackPtr]['scope_closer'];
            }

            return $returnValue;
        }

        /*
         * Either a T_STRING token pre-PHP 7.4, or T_FN on PHP 7.4, in combination with PHPCS < 3.5.3.
         * Now see about finding the relevant arrow function tokens.
         */
        $nextNonEmpty = $phpcsFile->findNext(
            (Tokens::$emptyTokens + [\T_BITWISE_AND]),
            ($stackPtr + 1),
            null,
            true
        );
        if ($nextNonEmpty === false || $tokens[$nextNonEmpty]['code'] !== \T_OPEN_PARENTHESIS) {
            return $returnValue;
        }

        $returnValue['parenthesis_opener'] = $nextNonEmpty;
        if (isset($tokens[$nextNonEmpty]['parenthesis_closer']) === false) {
            return $returnValue;
        }

        $returnValue['parenthesis_closer'] = $tokens[$nextNonEmpty]['parenthesis_closer'];

        $ignore                 = Tokens::$emptyTokens;
        $ignore                += Collections::$returnTypeTokens;
        $ignore[\T_COLON]       = \T_COLON;
        $ignore[\T_INLINE_ELSE] = \T_INLINE_ELSE; // PHPCS < 2.9.1.
        $ignore[\T_INLINE_THEN] = \T_INLINE_THEN; // PHPCS < 2.9.1.

        if (\defined('T_NULLABLE') === true) {
            $ignore[\T_NULLABLE] = \T_NULLABLE;
        }

        $arrow = $phpcsFile->findNext(
            $ignore,
            ($tokens[$nextNonEmpty]['parenthesis_closer'] + 1),
            null,
            true
        );

        if ($arrow === false
            || $tokens[$arrow]['code'] !== \T_DOUBLE_ARROW
        ) {
            return $returnValue;
        }

        $returnValue['scope_opener'] = $arrow;
        $inTernary                   = false;

        for ($scopeCloser = ($arrow + 1); $scopeCloser < $phpcsFile->numTokens; $scopeCloser++) {
            if (isset(self::$arrowFunctionEndTokens[$tokens[$scopeCloser]['code']]) === true
                && ($tokens[$scopeCloser]['code'] !== \T_COLON || $inTernary === false)
            ) {
                break;
            }

            if ($tokens[$scopeCloser]['type'] === 'T_FN'
                || ($tokens[$scopeCloser]['code'] === \T_STRING
                && $tokens[$scopeCloser]['content'] === 'fn')
            ) {
                $nested = self::getArrowFunctionOpenClose($phpcsFile, $scopeCloser);
                if (isset($nested['scope_closer']) && $nested['scope_closer'] !== false) {
                    // We minus 1 here in case the closer can be shared with us.
                    $scopeCloser = ($nested['scope_closer'] - 1);
                    continue;
                }
            }

            if (isset($tokens[$scopeCloser]['scope_closer']) === true
                && $tokens[$scopeCloser]['code'] !== \T_INLINE_ELSE
            ) {
                // We minus 1 here in case the closer can be shared with us.
                $scopeCloser = ($tokens[$scopeCloser]['scope_closer'] - 1);
                continue;
            }

            if (isset($tokens[$scopeCloser]['parenthesis_closer']) === true) {
                $scopeCloser = $tokens[$scopeCloser]['parenthesis_closer'];
                continue;
            }

            if (isset($tokens[$scopeCloser]['bracket_closer']) === true) {
                $scopeCloser = $tokens[$scopeCloser]['bracket_closer'];
                continue;
            }

            if ($tokens[$scopeCloser]['code'] === \T_INLINE_THEN) {
                $inTernary = true;
                continue;
            }

            if ($tokens[$scopeCloser]['code'] === \T_INLINE_ELSE) {
                if ($inTernary === false) {
                    break;
                }

                $inTernary = false;
            }
        }

        if ($scopeCloser !== $phpcsFile->numTokens) {
            $returnValue['scope_closer'] = $scopeCloser;
        }

        return $returnValue;
    }

    /**
     * Checks if a given function is a PHP magic function.
     *
     * @todo Add check for the function declaration being namespaced!
     *
     * @see \PHPCSUtils\Utils\FunctionDeclaration::isMagicFunctionName() For when you already know the name of the
     *                                                                   function and scope checking is done in the
     *                                                                   sniff.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The T_FUNCTION token to check.
     *
     * @return bool
     */
    public static function isMagicFunction(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== \T_FUNCTION) {
            return false;
        }

        if (Conditions::hasCondition($phpcsFile, $stackPtr, BCTokens::ooScopeTokens()) === true) {
            return false;
        }

        $name = self::getName($phpcsFile, $stackPtr);
        return self::isMagicFunctionName($name);
    }

    /**
     * Verify if a given function name is the name of a PHP magic function.
     *
     * @since 1.0.0
     *
     * @param string $name The full function name.
     *
     * @return bool
     */
    public static function isMagicFunctionName($name)
    {
        $name = \strtolower($name);
        return (isset(self::$magicFunctions[$name]) === true);
    }

    /**
     * Checks if a given function is a PHP magic method.
     *
     * @see \PHPCSUtils\Utils\FunctionDeclaration::isMagicMethodName() For when you already know the name of the
     *                                                                 method and scope checking is done in the
     *                                                                 sniff.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The T_FUNCTION token to check.
     *
     * @return bool
     */
    public static function isMagicMethod(File $phpcsFile, $stackPtr)
    {
        if (Scopes::isOOMethod($phpcsFile, $stackPtr) === false) {
            return false;
        }

        $name = self::getName($phpcsFile, $stackPtr);
        return self::isMagicMethodName($name);
    }

    /**
     * Verify if a given function name is the name of a PHP magic method.
     *
     * @since 1.0.0
     *
     * @param string $name The full function name.
     *
     * @return bool
     */
    public static function isMagicMethodName($name)
    {
        $name = \strtolower($name);
        return (isset(self::$magicMethods[$name]) === true);
    }

    /**
     * Checks if a given function is a PHP native double underscore method.
     *
     * @see \PHPCSUtils\Utils\FunctionDeclaration::isPHPDoubleUnderscoreMethodName() For when you already know the
     *                                                                               name of the method and scope
     *                                                                               checking is done in the sniff.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The T_FUNCTION token to check.
     *
     * @return bool
     */
    public static function isPHPDoubleUnderscoreMethod(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== \T_FUNCTION) {
            return false;
        }

        $scopePtr = Scopes::validDirectScope($phpcsFile, $stackPtr, BCTokens::ooScopeTokens());
        if ($scopePtr === false) {
            return false;
        }

        /*
         * If this is a class, make sure it extends something, as otherwise, the methods
         * still can't be overloads for the SOAPClient methods.
         * For a trait/interface we don't know the concrete implementation context, so skip
         * this check.
         */
        if ($tokens[$scopePtr]['code'] === \T_CLASS || $tokens[$scopePtr]['code'] === \T_ANON_CLASS) {
            $extends = ObjectDeclarations::findExtendedClassName($phpcsFile, $scopePtr);
            if ($extends === false) {
                return false;
            }
        }

        $name = self::getName($phpcsFile, $stackPtr);
        return self::isPHPDoubleUnderscoreMethodName($name);
    }

    /**
     * Verify if a given function name is the name of a PHP native double underscore method.
     *
     * @since 1.0.0
     *
     * @param string $name The full function name.
     *
     * @return bool
     */
    public static function isPHPDoubleUnderscoreMethodName($name)
    {
        $name = \strtolower($name);
        return (isset(self::$methodsDoubleUnderscore[$name]) === true);
    }

    /**
     * Checks if a given function is a magic method or a PHP native double underscore method.
     *
     * @see \PHPCSUtils\Utils\FunctionDeclaration::isSpecialMethodName() For when you already know the name of the
     *                                                                   method and scope checking is done in the
     *                                                                   sniff.
     *
     * @since 1.0.0
     *
     * {@internal Not the most efficient way of checking this, but less efficient ways will get
     *            less reliable results or introduce a lot of code duplication.}
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The T_FUNCTION token to check.
     *
     * @return bool
     */
    public static function isSpecialMethod(File $phpcsFile, $stackPtr)
    {
        if (self::isMagicMethod($phpcsFile, $stackPtr) === true) {
            return true;
        }

        if (self::isPHPDoubleUnderscoreMethod($phpcsFile, $stackPtr) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verify if a given function name is the name of a magic method or a PHP native double underscore method.
     *
     * @since 1.0.0
     *
     * @param string $name The full function name.
     *
     * @return bool
     */
    public static function isSpecialMethodName($name)
    {
        $name = \strtolower($name);
        return (isset(self::$magicMethods[$name]) === true || isset(self::$methodsDoubleUnderscore[$name]) === true);
    }
}
