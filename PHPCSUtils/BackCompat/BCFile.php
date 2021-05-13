<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 *
 * The methods in this class are imported from the PHP_CodeSniffer project.
 * Note: this is not a one-on-one import of the `File` class!
 *
 * Copyright of the original code in this class as per the import:
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Jaroslav Hanslík <kukulich@kukulich.cz>
 * @author    jdavis <jdavis@bamboohr.com>
 * @author    Klaus Purer <klaus.purer@gmail.com>
 * @author    Juliette Reinders Folmer <jrf@phpcodesniffer.info>
 * @author    Nick Wilde <nick@briarmoon.ca>
 * @author    Martin Hujer <mhujer@gmail.com>
 * @author    Chris Wilkinson <c.wilkinson@elifesciences.org>
 *
 * With documentation contributions from:
 * @author    Pascal Borreli <pascal@borreli.com>
 * @author    Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @author    Stefano Kowalke <blueduck@gmx.net>
 * @author    George Mponos <gmponos@gmail.com>
 * @author    Tyson Andre <tysonandre775@hotmail.com>
 * @author    Klaus Purer <klaus.purer@protonmail.ch>
 *
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHPCSUtils\BackCompat;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\BCTokens;
use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\FunctionDeclarations;
use PHPCSUtils\Utils\Parentheses;

/**
 * PHPCS native utility functions.
 *
 * Backport of the latest versions of PHPCS native utility functions to make them
 * available in older PHPCS versions without the bugs and other quirks that the
 * older versions of the native functions had.
 *
 * Additionally, this class works round the following tokenizer issues for
 * any affected utility functions:
 * - Array return type declarations were incorrectly tokenized to `T_ARRAY_HINT`
 *   instead of `T_RETURN_TYPE` in some circumstances prior to PHPCS 2.8.0.
 * - `T_NULLABLE` was not available prior to PHPCS 2.8.0 and utility functions did
 *   not take nullability of types into account.
 * - The PHPCS native ignore annotations were not available prior to PHPCS 3.2.0.
 * - The way return types were tokenized has changed in PHPCS 3.3.0.
 *   Previously a lot of them were tokenized as `T_RETURN_HINT`. For namespaced
 *   classnames this only tokenized the classname, not the whole return type.
 *   Now tokenization is "normalized" with most tokens being `T_STRING`, including
 *   array return type declarations.
 * - Typed properties were not recognized prior to PHPCS 3.5.0, including the
 *   `?` nullability token not being converted to `T_NULLABLE`.
 * - Arrow functions were not recognized properly until PHPCS 3.5.3/4.
 * - General PHP cross-version incompatibilities.
 *
 * Most functions in this class will have a related twin-function in the relevant
 * class in the `PHPCSUtils\Utils` namespace.
 * These will be indicated with `@see` tags in the docblock of the function.
 *
 * The PHPCSUtils native twin-functions will often have additional features and/or
 * improved functionality, but will generally be fully compatible with the PHPCS
 * native functions.
 * The differences between the functions here and the twin functions are documented
 * in the docblock of the respective twin-function.
 *
 * @see \PHP_CodeSniffer\Files\File Original source of these utility methods.
 *
 * @since 1.0.0
 */
class BCFile
{

    /**
     * Returns the declaration names for classes, interfaces, traits, and functions.
     *
     * PHPCS cross-version compatible version of the `File::getDeclarationName()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - PHPCS 2.8.0: Returns `null` when passed an anonymous class. Previously, the method
     *                would throw a "_token not of an accepted type_" exception.
     * - PHPCS 2.9.0: Returns `null` when passed a PHP closure. Previously, the method
     *                would throw a "_token not of an accepted type_" exception.
     * - PHPCS 3.0.0: Added support for ES6 class/method syntax.
     * - PHPCS 3.0.0: The Exception thrown changed from a `PHP_CodeSniffer_Exception` to
     *                `\PHP_CodeSniffer\Exceptions\RuntimeException`.
     * - PHPCS 3.5.3: Allow for functions to be called `fn` for backwards compatibility.
     *                Related to PHP 7.4 `T_FN` arrow functions.
     * - PHPCS 3.5.5: Remove arrow function work-around which is no longer needed due to
     *                a change in the tokenization of arrow functions.
     *
     * Note:
     * - For ES6 classes in combination with PHPCS 2.x, passing a `T_STRING` token to
     *   this method will be accepted for JS files.
     * - Support for JS ES6 method syntax has not been back-filled for PHPCS < 3.0.0.
     *   and sniffing JS files is not officially supported by PHPCSUtils.
     *
     * @see \PHP_CodeSniffer\Files\File::getDeclarationName() Original source.
     * @see \PHPCSUtils\Utils\ObjectDeclarations::getName()   PHPCSUtils native improved version.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added BC support for PHP 7.4 arrow functions.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the declaration token
     *                                               which declared the class, interface,
     *                                               trait, or function.
     *
     * @return string|null The name of the class, interface, trait, or function;
     *                     or `NULL` if the function or class is anonymous or
     *                     in case of a parse error/live coding.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not of type
     *                                                      `T_FUNCTION`, `T_CLASS`, `T_TRAIT`, or `T_INTERFACE`.
     */
    public static function getDeclarationName(File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $tokenCode = $tokens[$stackPtr]['code'];

        if ($tokenCode === T_ANON_CLASS || $tokenCode === T_CLOSURE) {
            return null;
        }

        /*
         * BC: Work-around JS ES6 classes not being tokenized as T_CLASS in PHPCS < 3.0.0.
         */
        if (isset($phpcsFile->tokenizerType)
            && $phpcsFile->tokenizerType === 'JS'
            && $tokenCode === T_STRING
            && $tokens[$stackPtr]['content'] === 'class'
        ) {
            $tokenCode = T_CLASS;
        }

        if ($tokenCode !== T_FUNCTION
            && $tokenCode !== T_CLASS
            && $tokenCode !== T_INTERFACE
            && $tokenCode !== T_TRAIT
        ) {
            throw new RuntimeException('Token type "' . $tokens[$stackPtr]['type'] . '" is not T_FUNCTION, T_CLASS, T_INTERFACE or T_TRAIT');
        }

        if ($tokenCode === T_FUNCTION
            && strtolower($tokens[$stackPtr]['content']) !== 'function'
        ) {
            // This is a function declared without the "function" keyword.
            // So this token is the function name.
            return $tokens[$stackPtr]['content'];
        }

        $content = null;
        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['code'] === T_STRING
                // BC: PHPCS 3.5.3/3.5.4.
                || $tokens[$i]['type'] === 'T_FN'
            ) {
                /*
                 * BC: In PHPCS 2.6.0, in case of live coding, the last token in a file will be tokenized
                 * as T_STRING, but won't have the `content` index set.
                 */
                if (isset($tokens[$i]['content'])) {
                    $content = $tokens[$i]['content'];
                }
                break;
            }
        }

        return $content;
    }

    /**
     * Returns the method parameters for the specified function token.
     *
     * Also supports passing in a `T_USE` token for a closure use group.
     *
     * Each parameter is in the following format:
     * ```php
     * 0 => array(
     *   'name'                => '$var',  // The variable name.
     *   'token'               => integer, // The stack pointer to the variable name.
     *   'content'             => string,  // The full content of the variable definition.
     *   'pass_by_reference'   => boolean, // Is the variable passed by reference?
     *   'reference_token'     => integer, // The stack pointer to the reference operator
     *                                     // or FALSE if the param is not passed by reference.
     *   'variable_length'     => boolean, // Is the param of variable length through use of `...` ?
     *   'variadic_token'      => integer, // The stack pointer to the ... operator
     *                                     // or FALSE if the param is not variable length.
     *   'type_hint'           => string,  // The type hint for the variable.
     *   'type_hint_token'     => integer, // The stack pointer to the start of the type hint
     *                                     // or FALSE if there is no type hint.
     *   'type_hint_end_token' => integer, // The stack pointer to the end of the type hint
     *                                     // or FALSE if there is no type hint.
     *   'nullable_type'       => boolean, // TRUE if the var type is preceded by the nullability
     *                                     // operator.
     *   'comma_token'         => integer, // The stack pointer to the comma after the param
     *                                     // or FALSE if this is the last param.
     * )
     * ```
     *
     * Parameters with default values have the following additional array indexes:
     * ```php
     *   'default'             => string,  // The full content of the default value.
     *   'default_token'       => integer, // The stack pointer to the start of the default value.
     *   'default_equal_token' => integer, // The stack pointer to the equals sign.
     * ```
     *
     * Parameters declared using PHP 8 constructor property promotion, have these additional array indexes:
     * ```php
     *   'property_visibility' => string,  // The property visibility as declared.
     *   'visibility_token'    => integer, // The stack pointer to the visibility modifier token.
     * ```
     *
     * PHPCS cross-version compatible version of the `File::getMethodParameters()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - PHPCS 2.8.0: Now recognises `self` as a valid type declaration.
     * - PHPCS 2.8.0: The return array now contains a new `"token"` index containing the stack pointer
     *                to the variable.
     * - PHPCS 2.8.0: The return array now contains a new `"content"` index containing the raw content
     *                of the param definition.
     * - PHPCS 2.8.0: Added support for nullable types.
     *                The return array now contains a new `"nullable_type"` index set to `true` or `false`
     *                for each method parameter.
     * - PHPCS 2.8.0: Added support for closures.
     * - PHPCS 3.0.0: The Exception thrown changed from a `PHP_CodeSniffer_Exception` to
     *                `\PHP_CodeSniffer\Exceptions\TokenizerException`.
     * - PHPCS 3.3.0: The return array now contains a new `"type_hint_token"` array index.
     *                Provides the position in the token stack of the first token in the type declaration.
     * - PHPCS 3.3.1: Fixed incompatibility with PHP 7.3.
     * - PHPCS 3.5.0: The Exception thrown changed from a `\PHP_CodeSniffer\Exceptions\TokenizerException`
     *                to `\PHP_CodeSniffer\Exceptions\RuntimeException`.
     * - PHPCS 3.5.0: Added support for closure USE groups.
     * - PHPCS 3.5.0: The return array now contains yet more more information.
     *                - If a type hint is specified, the position of the last token in the hint will be
     *                  set in a `"type_hint_end_token"` array index.
     *                - If a default is specified, the position of the first token in the default value
     *                  will be set in a `"default_token"` array index.
     *                - If a default is specified, the position of the equals sign will be set in a
     *                  `"default_equal_token"` array index.
     *                - If the param is not the last, the position of the comma will be set in a
     *                  `"comma_token"` array index.
     *                - If the param is passed by reference, the position of the reference operator
     *                  will be set in a `"reference_token"` array index.
     *                - If the param is variable length, the position of the variadic operator will
     *                  be set in a `"variadic_token"` array index.
     * - PHPCS 3.5.3: Fixed a bug where the `"type_hint_end_token"` array index for a type hinted
     *                parameter would bleed through to the next (non-type hinted) parameter.
     * - PHPCS 3.5.3: Added support for PHP 7.4 `T_FN` arrow functions.
     * - PHPCS 3.5.7: Added support for namespace operators in type declarations. PHPCS#3066.
     * - PHPCS 3.6.0: Added support for PHP 8.0 union types. PHPCS#3032.
     * - PHPCS 3.6.0: Added support for PHP 8.0 constructor property promotion. PHPCS#3152.
     *
     * @see \PHP_CodeSniffer\Files\File::getMethodParameters()      Original source.
     * @see \PHPCSUtils\Utils\FunctionDeclarations::getParameters() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added BC support for PHP 7.4 arrow functions.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the function token
     *                                               to acquire the parameters for.
     *
     * @return array
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified `$stackPtr` is not of
     *                                                      type `T_FUNCTION`, `T_CLOSURE`, `T_USE`,
     *                                                      or `T_FN`.
     */
    public static function getMethodParameters(File $phpcsFile, $stackPtr)
    {
        $tokens         = $phpcsFile->getTokens();
        $arrowOpenClose = FunctionDeclarations::getArrowFunctionOpenClose($phpcsFile, $stackPtr);

        if ($tokens[$stackPtr]['code'] !== T_FUNCTION
            && $tokens[$stackPtr]['code'] !== T_CLOSURE
            && $tokens[$stackPtr]['code'] !== T_USE
            && $arrowOpenClose === false
        ) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or T_USE or T_FN');
        }

        if ($tokens[$stackPtr]['code'] === T_USE) {
            $opener = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($stackPtr + 1));
            if ($opener === false
                || (isset($tokens[$opener]['parenthesis_owner']) === true
                // BC: as of PHPCS 4.x, closure use tokens are parentheses owners.
                && $tokens[$opener]['parenthesis_owner'] !== $stackPtr)
            ) {
                throw new RuntimeException('$stackPtr was not a valid T_USE');
            }
        } elseif ($arrowOpenClose !== false) {
            // Arrow function in combination with PHP < 7.4 or PHPCS < 3.5.3/4/5.
            $opener = $arrowOpenClose['parenthesis_opener'];
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
        $visibilityToken  = null;

        for ($i = $paramStart; $i <= $closer; $i++) {
            // Check to see if this token has a parenthesis or bracket opener. If it does
            // it's likely to be an array which might have arguments in it. This
            // could cause problems in our parsing below, so lets just skip to the
            // end of it.
            if (isset($tokens[$i]['parenthesis_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $tokens[$i]['parenthesis_closer']) {
                    $i = ($tokens[$i]['parenthesis_closer'] + 1);
                }
            }

            if (isset($tokens[$i]['bracket_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $tokens[$i]['bracket_closer']) {
                    $i = ($tokens[$i]['bracket_closer'] + 1);
                }
            }

            // Changed from checking 'code' to 'type' to allow for T_NULLABLE not existing in PHPCS < 2.8.0.
            switch ($tokens[$i]['type']) {
                case 'T_BITWISE_AND':
                    if ($defaultStart === null) {
                        $passByReference = true;
                        $referenceToken  = $i;
                    }
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
                    if ($typeHintToken === false) {
                        $typeHintToken = $i;
                    }

                    $typeHint        .= $tokens[$i]['content'];
                    $typeHintEndToken = $i;
                    break;
                case 'T_SELF':
                case 'T_PARENT':
                case 'T_STATIC':
                    // Self and parent are valid, static invalid, but was probably intended as type hint.
                    if (isset($defaultStart) === false) {
                        if ($typeHintToken === false) {
                            $typeHintToken = $i;
                        }

                        $typeHint        .= $tokens[$i]['content'];
                        $typeHintEndToken = $i;
                    }
                    break;
                case 'T_STRING':
                case 'T_NAME_QUALIFIED':
                case 'T_NAME_FULLY_QUALIFIED':
                case 'T_NAME_RELATIVE':
                    // This is an identifier name, so it may be a type hint, but it could
                    // also be a constant used as a default value.
                    $prevComma = false;
                    for ($t = $i; $t >= $opener; $t--) {
                        if ($tokens[$t]['code'] === T_COMMA) {
                            $prevComma = $t;
                            break;
                        }
                    }

                    if ($prevComma !== false) {
                        $nextEquals = false;
                        for ($t = $prevComma; $t < $i; $t++) {
                            if ($tokens[$t]['code'] === T_EQUAL) {
                                $nextEquals = $t;
                                break;
                            }
                        }

                        if ($nextEquals !== false) {
                            break;
                        }
                    }

                    if ($defaultStart === null) {
                        if ($typeHintToken === false) {
                            $typeHintToken = $i;
                        }

                        $typeHint        .= $tokens[$i]['content'];
                        $typeHintEndToken = $i;
                    }
                    break;
                case 'T_NAMESPACE':
                case 'T_NS_SEPARATOR':
                case 'T_BITWISE_OR': // Union type separator PHPCS < 3.6.0.
                case 'T_TYPE_UNION': // Union type separator PHPCS >= 3.6.0.
                case 'T_FALSE':
                case 'T_NULL':
                    // Part of a type hint or default value.
                    if ($defaultStart === null) {
                        if ($typeHintToken === false) {
                            $typeHintToken = $i;
                        }

                        $typeHint        .= $tokens[$i]['content'];
                        $typeHintEndToken = $i;
                    }
                    break;
                case 'T_NULLABLE':
                case 'T_INLINE_THEN': // PHPCS < 2.8.0.
                    if ($defaultStart === null) {
                        $nullableType     = true;
                        $typeHint        .= $tokens[$i]['content'];
                        $typeHintEndToken = $i;
                    }
                    break;
                case 'T_PUBLIC':
                case 'T_PROTECTED':
                case 'T_PRIVATE':
                    if ($defaultStart === null) {
                        $visibilityToken = $i;
                    }
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
                    $vars[$paramCount]['content'] = trim($phpcsFile->getTokensAsString($paramStart, ($i - $paramStart)));

                    if ($defaultStart !== null) {
                        $vars[$paramCount]['default']             = trim($phpcsFile->getTokensAsString($defaultStart, ($i - $defaultStart)));
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

                    if ($visibilityToken !== null) {
                        $vars[$paramCount]['property_visibility'] = $tokens[$visibilityToken]['content'];
                        $vars[$paramCount]['visibility_token']    = $visibilityToken;
                    }

                    if ($tokens[$i]['code'] === T_COMMA) {
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
                    $visibilityToken  = null;

                    $paramCount++;
                    break;
                case 'T_EQUAL':
                    $defaultStart = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), null, true);
                    $equalToken   = $i;
                    break;
            }
        }

        return $vars;
    }

    /**
     * Returns the visibility and implementation properties of a method.
     *
     * The format of the return value is:
     * ```php
     * array(
     *   'scope'                 => 'public', // Public, private, or protected
     *   'scope_specified'       => true,     // TRUE if the scope keyword was found.
     *   'return_type'           => '',       // The return type of the method.
     *   'return_type_token'     => integer,  // The stack pointer to the start of the return type
     *                                        // or FALSE if there is no return type.
     *   'return_type_end_token' => integer,  // The stack pointer to the end of the return type
     *                                        // or FALSE if there is no return type.
     *   'nullable_return_type'  => false,    // TRUE if the return type is preceded by
     *                                        // the nullability operator.
     *   'is_abstract'           => false,    // TRUE if the abstract keyword was found.
     *   'is_final'              => false,    // TRUE if the final keyword was found.
     *   'is_static'             => false,    // TRUE if the static keyword was found.
     *   'has_body'              => false,    // TRUE if the method has a body
     * );
     * ```
     *
     * PHPCS cross-version compatible version of the `File::getMethodProperties()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - PHPCS 3.0.0: Removed the `"is_closure"` array index which was always `false` anyway.
     * - PHPCS 3.0.0: The Exception thrown changed from a `PHP_CodeSniffer_Exception` to
     *                `\PHP_CodeSniffer\Exceptions\TokenizerException`.
     * - PHPCS 3.3.0: New `"return_type"`, `"return_type_token"` and `"nullable_return_type"` array indexes.
     *                - The `"return_type"` index contains the return type of the function or closer,
     *                  or a blank string if not specified.
     *                - If the return type is nullable, the return type will contain the leading `?`.
     *                - A `"nullable_return_type"` array index in the return value will also be set to `true`.
     *                - If the return type contains namespace information, it will be cleaned of
     *                  whitespace and comments.
     *                - To access the original return value string, use the main tokens array.
     * - PHPCS 3.4.0: New `"has_body"` array index.
     *                `false` if the method has no body (as with abstract and interface methods)
     *                or `true` otherwise.
     * - PHPCS 3.5.0: The Exception thrown changed from a `\PHP_CodeSniffer\Exceptions\TokenizerException`
     *                to `\PHP_CodeSniffer\Exceptions\RuntimeException`.
     * - PHPCS 3.5.3: Added support for PHP 7.4 `T_FN` arrow functions.
     * - PHPCS 3.5.7: Added support for namespace operators in type declarations. PHPCS#3066.
     * - PHPCS 3.6.0: Added support for PHP 8.0 union types. PHPCS#3032.
     * - PHPCS 3.6.0: Added new `"return_type_end_token"` index. PHPCS#3153.
     *
     * @see \PHP_CodeSniffer\Files\File::getMethodProperties()      Original source.
     * @see \PHPCSUtils\Utils\FunctionDeclarations::getProperties() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added BC support for PHP 7.4 arrow functions.
     * @since 1.0.0-alpha3 Added support for PHP 8.0 static return type (expected in future PHPCS release).
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the function token to
     *                                               acquire the properties for.
     *
     * @return array
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      `T_FUNCTION`, `T_CLOSURE`, or `T_FN` token.
     */
    public static function getMethodProperties(File $phpcsFile, $stackPtr)
    {
        $tokens         = $phpcsFile->getTokens();
        $arrowOpenClose = FunctionDeclarations::getArrowFunctionOpenClose($phpcsFile, $stackPtr);

        if ($tokens[$stackPtr]['code'] !== T_FUNCTION
            && $tokens[$stackPtr]['code'] !== T_CLOSURE
            && $arrowOpenClose === false
        ) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or T_FN');
        }

        if ($tokens[$stackPtr]['code'] === T_FUNCTION) {
            $valid = [
                T_PUBLIC      => T_PUBLIC,
                T_PRIVATE     => T_PRIVATE,
                T_PROTECTED   => T_PROTECTED,
                T_STATIC      => T_STATIC,
                T_FINAL       => T_FINAL,
                T_ABSTRACT    => T_ABSTRACT,
                T_WHITESPACE  => T_WHITESPACE,
                T_COMMENT     => T_COMMENT,
                T_DOC_COMMENT => T_DOC_COMMENT,
            ];
        } else {
            $valid = [
                T_STATIC      => T_STATIC,
                T_WHITESPACE  => T_WHITESPACE,
                T_COMMENT     => T_COMMENT,
                T_DOC_COMMENT => T_DOC_COMMENT,
            ];
        }

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
                case T_PUBLIC:
                    $scope          = 'public';
                    $scopeSpecified = true;
                    break;
                case T_PRIVATE:
                    $scope          = 'private';
                    $scopeSpecified = true;
                    break;
                case T_PROTECTED:
                    $scope          = 'protected';
                    $scopeSpecified = true;
                    break;
                case T_ABSTRACT:
                    $isAbstract = true;
                    break;
                case T_FINAL:
                    $isFinal = true;
                    break;
                case T_STATIC:
                    $isStatic = true;
                    break;
            }
        }

        $returnType         = '';
        $returnTypeToken    = false;
        $nullableReturnType = false;
        $returnTypeEndToken = false;
        $hasBody            = true;
        $returnTypeTokens   = Collections::returnTypeTokensBC();

        $parenthesisCloser = null;
        if (isset($tokens[$stackPtr]['parenthesis_closer']) === true) {
            $parenthesisCloser = $tokens[$stackPtr]['parenthesis_closer'];
        } elseif ($arrowOpenClose !== false) {
            // Arrow function in combination with PHP < 7.4 or PHPCS < 3.5.3.
            $parenthesisCloser = $arrowOpenClose['parenthesis_closer'];
        }

        if (isset($parenthesisCloser) === true) {
            $scopeOpener = null;
            if (isset($tokens[$stackPtr]['scope_opener']) === true) {
                $scopeOpener = $tokens[$stackPtr]['scope_opener'];
            } elseif ($arrowOpenClose !== false) {
                // Arrow function in combination with PHP < 7.4 or PHPCS < 3.5.3.
                $scopeOpener = $arrowOpenClose['scope_opener'];
            }

            for ($i = $parenthesisCloser; $i < $phpcsFile->numTokens; $i++) {
                if (($scopeOpener === null && $tokens[$i]['code'] === T_SEMICOLON)
                    || ($scopeOpener !== null && $i === $scopeOpener)
                ) {
                    // End of function definition.
                    break;
                }

                /*
                 * Work-around for a scope map tokenizer bug in PHPCS.
                 * {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/3066}
                 */
                if ($scopeOpener === null && $tokens[$i]['code'] === \T_OPEN_CURLY_BRACKET) {
                    // End of function definition for which the scope opener is incorrectly not set.
                    $hasBody = true;
                    break;
                }

                if ($tokens[$i]['type'] === 'T_NULLABLE'
                    // Handle nullable tokens in PHPCS < 2.8.0.
                    || (defined('T_NULLABLE') === false && $tokens[$i]['code'] === T_INLINE_THEN)
                    // Handle nullable tokens with arrow functions in PHPCS 2.8.0 - 2.9.0.
                    || ($arrowOpenClose !== false && $tokens[$i]['code'] === T_INLINE_THEN
                        && version_compare(Helper::getVersion(), '2.9.1', '<') === true)
                ) {
                    $nullableReturnType = true;
                }

                if (isset($returnTypeTokens[$tokens[$i]['code']]) === true) {
                    if ($returnTypeToken === false) {
                        $returnTypeToken = $i;
                    }

                    $returnType        .= $tokens[$i]['content'];
                    $returnTypeEndToken = $i;
                }
            }

            $bodyTokens = [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET];
            if ($arrowOpenClose !== false) {
                $bodyTokens = [T_DOUBLE_ARROW => T_DOUBLE_ARROW];
                if (defined('T_FN_ARROW') === true) {
                    // PHPCS 3.5.3+.
                    $bodyTokens[T_FN_ARROW] = T_FN_ARROW;
                }
            }

            $end     = $phpcsFile->findNext(($bodyTokens + [T_SEMICOLON]), $parenthesisCloser);
            $hasBody = ($end !== false && isset($bodyTokens[$tokens[$end]['code']]));
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
     * Returns the visibility and implementation properties of a class member var.
     *
     * The format of the return value is:
     * ```php
     * array(
     *   'scope'           => string,  // Public, private, or protected.
     *   'scope_specified' => boolean, // TRUE if the scope was explicitly specified.
     *   'is_static'       => boolean, // TRUE if the static keyword was found.
     *   'type'            => string,  // The type of the var (empty if no type specified).
     *   'type_token'      => integer, // The stack pointer to the start of the type
     *                                 // or FALSE if there is no type.
     *   'type_end_token'  => integer, // The stack pointer to the end of the type
     *                                 // or FALSE if there is no type.
     *   'nullable_type'   => boolean, // TRUE if the type is preceded by the
     *                                 // nullability operator.
     * );
     * ```
     *
     * PHPCS cross-version compatible version of the `File::getMemberProperties()  method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - PHPCS 3.0.0: The Exception thrown changed from a `PHP_CodeSniffer_Exception` to
     *                `\PHP_CodeSniffer\Exceptions\TokenizerException`.
     * - PHPCS 3.4.0: Fixed method params being recognized as properties, PHPCS#2214.
     * - PHPCS 3.5.0: New `"type"`, `"type_token"`, `"type_end_token"` and `"nullable_type"` array indexes.
     *                - The `"type"` index contains the type of the member var, or a blank string
     *                  if not specified.
     *                - If the type is nullable, `"type"` will contain the leading `?`.
     *                - If a type is specified, the position of the first token in the type will
     *                  be set in a `"type_token"` array index.
     *                - If a type is specified, the position of the last token in the type will
     *                  be set in a `"type_end_token"` array index.
     *                - If the type is nullable, a `"nullable_type"` array index will also be set to `true`.
     *                - If the type contains namespace information, it will be cleaned of whitespace
     *                  and comments in the `"type"` value.
     * - PHPCS 3.5.0: The Exception thrown changed from a `\PHP_CodeSniffer\Exceptions\TokenizerException`
     *                to `\PHP_CodeSniffer\Exceptions\RuntimeException`.
     * - PHPCS 3.5.7: Added support for namespace operators in type declarations. PHPCS#3066.
     * - PHPCS 3.6.0: Added support for PHP 8.0 union types. PHPCS#3032.
     *
     * @see \PHP_CodeSniffer\Files\File::getMemberProperties() Original source.
     * @see \PHPCSUtils\Utils\Variables::getMemberProperties() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the `T_VARIABLE` token to
     *                                               acquire the properties for.
     *
     * @return array
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      `T_VARIABLE` token, or if the position is not
     *                                                      a class member variable.
     */
    public static function getMemberProperties(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_VARIABLE) {
            throw new RuntimeException('$stackPtr must be of type T_VARIABLE');
        }

        $conditions = array_keys($tokens[$stackPtr]['conditions']);
        $ptr        = array_pop($conditions);
        if (isset($tokens[$ptr]) === false
            || ($tokens[$ptr]['code'] !== T_CLASS
            && $tokens[$ptr]['code'] !== T_ANON_CLASS
            && $tokens[$ptr]['code'] !== T_TRAIT)
        ) {
            if (isset($tokens[$ptr]) === true
                && $tokens[$ptr]['code'] === T_INTERFACE
            ) {
                // T_VARIABLEs in interfaces can actually be method arguments
                // but they wont be seen as being inside the method because there
                // are no scope openers and closers for abstract methods. If it is in
                // parentheses, we can be pretty sure it is a method argument.
                if (isset($tokens[$stackPtr]['nested_parenthesis']) === false
                    || empty($tokens[$stackPtr]['nested_parenthesis']) === true
                ) {
                    $error = 'Possible parse error: interfaces may not include member vars';
                    $phpcsFile->addWarning($error, $stackPtr, 'Internal.ParseError.InterfaceHasMemberVar');
                    return [];
                }
            } else {
                throw new RuntimeException('$stackPtr is not a class member var');
            }
        }

        // Make sure it's not a method parameter.
        if (empty($tokens[$stackPtr]['nested_parenthesis']) === false) {
            $parenthesis = array_keys($tokens[$stackPtr]['nested_parenthesis']);
            $deepestOpen = array_pop($parenthesis);
            if ($deepestOpen > $ptr
                && isset($tokens[$deepestOpen]['parenthesis_owner']) === true
                && $tokens[$tokens[$deepestOpen]['parenthesis_owner']]['code'] === T_FUNCTION
            ) {
                throw new RuntimeException('$stackPtr is not a class member var');
            }
        }

        $valid  = Collections::$propertyModifierKeywords;
        $valid += Tokens::$emptyTokens;

        $scope          = 'public';
        $scopeSpecified = false;
        $isStatic       = false;

        $startOfStatement = $phpcsFile->findPrevious(
            [
                T_SEMICOLON,
                T_OPEN_CURLY_BRACKET,
                T_CLOSE_CURLY_BRACKET,
            ],
            ($stackPtr - 1)
        );

        for ($i = ($startOfStatement + 1); $i < $stackPtr; $i++) {
            if (isset($valid[$tokens[$i]['code']]) === false) {
                break;
            }

            switch ($tokens[$i]['code']) {
                case T_PUBLIC:
                    $scope          = 'public';
                    $scopeSpecified = true;
                    break;
                case T_PRIVATE:
                    $scope          = 'private';
                    $scopeSpecified = true;
                    break;
                case T_PROTECTED:
                    $scope          = 'protected';
                    $scopeSpecified = true;
                    break;
                case T_STATIC:
                    $isStatic = true;
                    break;
            }
        }

        $type               = '';
        $typeToken          = false;
        $typeEndToken       = false;
        $nullableType       = false;
        $propertyTypeTokens = Collections::propertyTypeTokensBC();

        if ($i < $stackPtr) {
            // We've found a type.
            for ($i; $i < $stackPtr; $i++) {
                if ($tokens[$i]['code'] === T_VARIABLE) {
                    // Hit another variable in a group definition.
                    break;
                }

                if ($tokens[$i]['type'] === 'T_NULLABLE'
                    // Handle nullable property types in PHPCS < 3.5.0.
                    || $tokens[$i]['code'] === T_INLINE_THEN
                ) {
                    $nullableType = true;
                }

                if (isset($propertyTypeTokens[$tokens[$i]['code']]) === true) {
                    $typeEndToken = $i;
                    if ($typeToken === false) {
                        $typeToken = $i;
                    }

                    $type .= $tokens[$i]['content'];
                }
            }

            if ($type !== '' && $nullableType === true) {
                $type = '?' . $type;
            }
        }

        return [
            'scope'           => $scope,
            'scope_specified' => $scopeSpecified,
            'is_static'       => $isStatic,
            'type'            => $type,
            'type_token'      => $typeToken,
            'type_end_token'  => $typeEndToken,
            'nullable_type'   => $nullableType,
        ];
    }

    /**
     * Returns the implementation properties of a class.
     *
     * The format of the return value is:
     * ```php
     * array(
     *   'is_abstract' => false, // TRUE if the abstract keyword was found.
     *   'is_final'    => false, // TRUE if the final keyword was found.
     * );
     * ```
     *
     * PHPCS cross-version compatible version of the `File::getClassProperties()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 1.3.0.
     * - PHPCS 3.0.0: The Exception thrown changed from a `PHP_CodeSniffer_Exception` to
     *                `\PHP_CodeSniffer\Exceptions\TokenizerException`.
     * - PHPCS 3.5.0: The Exception thrown changed from a `\PHP_CodeSniffer\Exceptions\TokenizerException`
     *                to`\PHP_CodeSniffer\Exceptions\RuntimeException`.
     *
     * @see \PHP_CodeSniffer\Files\File::getClassProperties()          Original source.
     * @see \PHPCSUtils\Utils\ObjectDeclarations::getClassProperties() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the `T_CLASS`
     *                                               token to acquire the properties for.
     *
     * @return array
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      `T_CLASS` token.
     */
    public static function getClassProperties(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_CLASS) {
            throw new RuntimeException('$stackPtr must be of type T_CLASS');
        }

        $valid = [
            T_FINAL       => T_FINAL,
            T_ABSTRACT    => T_ABSTRACT,
            T_WHITESPACE  => T_WHITESPACE,
            T_COMMENT     => T_COMMENT,
            T_DOC_COMMENT => T_DOC_COMMENT,
        ];

        $isAbstract = false;
        $isFinal    = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$tokens[$i]['code']]) === false) {
                break;
            }

            switch ($tokens[$i]['code']) {
                case T_ABSTRACT:
                    $isAbstract = true;
                    break;

                case T_FINAL:
                    $isFinal = true;
                    break;
            }
        }

        return [
            'is_abstract' => $isAbstract,
            'is_final'    => $isFinal,
        ];
    }

    /**
     * Determine if the passed token is a reference operator.
     *
     * PHPCS cross-version compatible version of the `File::isReference()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - PHPCS 3.1.1: Bug fix: misidentification of reference vs bitwise operator, PHPCS#1604/#1609.
     *                - An array assignment of a calculated value with a bitwise and operator in it,
     *                  was being misidentified as a reference.
     *                - A calculated default value for a function parameter with a bitwise and operator
     *                  in it, was being misidentified as a reference.
     *                - New by reference was not recognized as a reference.
     *                - References to class properties with `self::`, `parent::`, `static::`,
     *                  `namespace\ClassName::`, `classname::` were not recognized as references.
     * - PHPCS 3.5.3: Added support for PHP 7.4 `T_FN` arrow functions returning by reference.
     * - PHPCS 3.5.6: Bug fix: the reference operator for closures declared to return by reference was
     *                not recognized as a reference. PHPCS#2977.
     * - PHPCS 3.5.7: Bug fix: Parameters passed by reference in arrow functions are recognized correctly.
     *                PHPCS #3049/#3103
     *
     * @see \PHP_CodeSniffer\Files\File::isReference() Original source.
     * @see \PHPCSUtils\Utils\Operators::isReference() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added BC support for PHP 7.4 arrow functions.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
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

        if ($tokens[$stackPtr]['code'] !== T_BITWISE_AND) {
            return false;
        }

        $tokenBefore = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        if ($tokens[$tokenBefore]['code'] === T_FUNCTION
            || $tokens[$tokenBefore]['code'] === T_CLOSURE
            || FunctionDeclarations::isArrowFunction($phpcsFile, $tokenBefore) === true
        ) {
            // Function returns a reference.
            return true;
        }

        if ($tokens[$tokenBefore]['code'] === T_DOUBLE_ARROW) {
            // Inside a foreach loop or array assignment, this is a reference.
            return true;
        }

        if ($tokens[$tokenBefore]['code'] === T_AS) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if (isset(BCTokens::assignmentTokens()[$tokens[$tokenBefore]['code']]) === true) {
            // This is directly after an assignment. It's a reference. Even if
            // it is part of an operation, the other tests will handle it.
            return true;
        }

        $tokenAfter = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($tokens[$tokenAfter]['code'] === T_NEW) {
            return true;
        }

        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            $lastBracket = Parentheses::getLastOpener($phpcsFile, $stackPtr);
            if ($lastBracket !== false) {
                $owner = Parentheses::getOwner($phpcsFile, $lastBracket);
                if (isset(Collections::functionDeclarationTokensBC()[$tokens[$owner]['code']]) === true) {
                    $params = self::getMethodParameters($phpcsFile, $owner);
                    foreach ($params as $param) {
                        if ($param['reference_token'] === $stackPtr) {
                            // Function parameter declared to be passed by reference.
                            return true;
                        }
                    }
                }
            } else {
                $prev = false;
                for ($t = ($tokens[$lastBracket]['parenthesis_opener'] - 1); $t >= 0; $t--) {
                    if ($tokens[$t]['code'] !== T_WHITESPACE) {
                        $prev = $t;
                        break;
                    }
                }

                if ($prev !== false && $tokens[$prev]['code'] === T_USE) {
                    // Closure use by reference.
                    return true;
                }
            }
        }

        // Pass by reference in function calls and assign by reference in arrays.
        if ($tokens[$tokenBefore]['code'] === T_OPEN_PARENTHESIS
            || $tokens[$tokenBefore]['code'] === T_COMMA
            || $tokens[$tokenBefore]['code'] === T_OPEN_SHORT_ARRAY
        ) {
            if ($tokens[$tokenAfter]['code'] === T_VARIABLE) {
                return true;
            } else {
                $skip = Tokens::$emptyTokens;

                // BC for PHP 8 in combination with PHPCS < 3.5.7.
                $skip += Collections::nameTokens();

                $skip[] = T_NS_SEPARATOR;
                $skip[] = T_SELF;
                $skip[] = T_PARENT;
                $skip[] = T_STATIC;
                $skip[] = T_STRING;
                $skip[] = T_NAMESPACE;
                $skip[] = T_DOUBLE_COLON;

                $nextSignificantAfter = $phpcsFile->findNext(
                    $skip,
                    ($stackPtr + 1),
                    null,
                    true
                );
                if ($tokens[$nextSignificantAfter]['code'] === T_VARIABLE) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the content of the tokens from the specified start position in
     * the token stack for the specified length.
     *
     * PHPCS cross-version compatible version of the `File::getTokensAsString()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - PHPCS 3.3.0: New `$origContent` parameter to optionally return original
     *                (non tab-replaced) content.
     * - PHPCS 3.4.0:
     *                - Now throws a `RuntimeException` if the `$start` param is invalid.
     *                  This stops an infinite loop when the function is passed invalid data.
     *                - If the `$length` param is invalid, an empty string will be returned.
     *
     * @see \PHP_CodeSniffer\Files\File::getTokensAsString() Original source.
     * @see \PHPCSUtils\Utils\GetTokensAsString              Related set of functions.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being scanned.
     * @param int                         $start       The position to start from in the token stack.
     * @param int                         $length      The length of tokens to traverse from the start pos.
     * @param bool                        $origContent Whether the original content or the tab replaced
     *                                                 content should be used.
     *
     * @return string The token contents.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified start position does not exist.
     */
    public static function getTokensAsString(File $phpcsFile, $start, $length, $origContent = false)
    {
        $tokens = $phpcsFile->getTokens();

        if (is_int($start) === false || isset($tokens[$start]) === false) {
            throw new RuntimeException('The $start position for getTokensAsString() must exist in the token stack');
        }

        if (is_int($length) === false || $length <= 0) {
            return '';
        }

        $str = '';
        $end = ($start + $length);
        if ($end > $phpcsFile->numTokens) {
            $end = $phpcsFile->numTokens;
        }

        for ($i = $start; $i < $end; $i++) {
            // If tabs are being converted to spaces by the tokeniser, the
            // original content should be used instead of the converted content.
            if ($origContent === true && isset($tokens[$i]['orig_content']) === true) {
                $str .= $tokens[$i]['orig_content'];
            } else {
                $str .= $tokens[$i]['content'];
            }
        }

        return $str;
    }

    /**
     * Returns the position of the first non-whitespace token in a statement.
     *
     * PHPCS cross-version compatible version of the `File::findStartOfStatement()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 2.1.0.
     * - PHPCS 2.6.2: New optional `$ignore` parameter to selectively ignore stop points.
     * - PHPCS 3.5.5: Added support for PHP 7.4 `T_FN` arrow functions.
     *
     * @see \PHP_CodeSniffer\Files\File::findStartOfStatement() Original source.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added BC support for PHP 7.4 arrow functions.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $start     The position to start searching from in the token stack.
     * @param int|string|array            $ignore    Token types that should not be considered stop points.
     *
     * @return int
     */
    public static function findStartOfStatement(File $phpcsFile, $start, $ignore = null)
    {
        $tokens = $phpcsFile->getTokens();

        $endTokens = Tokens::$blockOpeners;

        $endTokens[T_COLON]            = true;
        $endTokens[T_COMMA]            = true;
        $endTokens[T_DOUBLE_ARROW]     = true;
        $endTokens[T_SEMICOLON]        = true;
        $endTokens[T_OPEN_TAG]         = true;
        $endTokens[T_CLOSE_TAG]        = true;
        $endTokens[T_OPEN_SHORT_ARRAY] = true;

        if ($ignore !== null) {
            $ignore = (array) $ignore;
            foreach ($ignore as $code) {
                unset($endTokens[$code]);
            }
        }

        $lastNotEmpty = $start;

        for ($i = $start; $i >= 0; $i--) {
            if (isset($endTokens[$tokens[$i]['code']]) === true) {
                // Found the end of the previous statement.
                return $lastNotEmpty;
            }

            if (isset($tokens[$i]['scope_opener']) === true
                && $i === $tokens[$i]['scope_closer']
                && $tokens[$i]['code'] !== T_CLOSE_PARENTHESIS
                && $tokens[$i]['code'] !== T_END_NOWDOC
                && $tokens[$i]['code'] !== T_END_HEREDOC
            ) {
                // Found the end of the previous scope block.
                return $lastNotEmpty;
            }

            // Skip nested statements.
            if (isset($tokens[$i]['bracket_opener']) === true
                && $i === $tokens[$i]['bracket_closer']
            ) {
                $i = $tokens[$i]['bracket_opener'];
            } elseif (isset($tokens[$i]['parenthesis_opener']) === true
                && $i === $tokens[$i]['parenthesis_closer']
            ) {
                $i = $tokens[$i]['parenthesis_opener'];
            }

            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === false) {
                $lastNotEmpty = $i;
            }
        }

        return 0;
    }

    /**
     * Returns the position of the last non-whitespace token in a statement.
     *
     * PHPCS cross-version compatible version of the `File::findEndOfStatement()  method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 2.1.0.
     * - PHPCS 2.6.2: New optional `$ignore` parameter to selectively ignore stop points.
     * - PHPCS 2.7.1: Improved handling of short arrays, PHPCS #1203.
     * - PHPCS 3.3.0: Bug fix: end of statement detection when passed a scope opener, PHPCS #1863.
     * - PHPCS 3.5.0: Improved handling of group use statements.
     * - PHPCS 3.5.3: Added support for PHP 7.4 `T_FN` arrow functions.
     * - PHPCS 3.5.4: Improved support for PHP 7.4 `T_FN` arrow functions.
     * - PHPCS 3.5.5: Improved support for PHP 7.4 `T_FN` arrow functions, PHPCS #2895.
     *
     * @see \PHP_CodeSniffer\Files\File::findEndOfStatement() Original source.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added BC support for PHP 7.4 arrow functions.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $start     The position to start searching from in the token stack.
     * @param int|string|array            $ignore    Token types that should not be considered stop points.
     *
     * @return int
     */
    public static function findEndOfStatement(File $phpcsFile, $start, $ignore = null)
    {
        $tokens = $phpcsFile->getTokens();

        $endTokens = [
            T_COLON                => true,
            T_COMMA                => true,
            T_DOUBLE_ARROW         => true,
            T_SEMICOLON            => true,
            T_CLOSE_PARENTHESIS    => true,
            T_CLOSE_SQUARE_BRACKET => true,
            T_CLOSE_CURLY_BRACKET  => true,
            T_CLOSE_SHORT_ARRAY    => true,
            T_OPEN_TAG             => true,
            T_CLOSE_TAG            => true,
        ];

        if ($ignore !== null) {
            $ignore = (array) $ignore;
            foreach ($ignore as $code) {
                unset($endTokens[$code]);
            }
        }

        $lastNotEmpty = $start;
        for ($i = $start; $i < $phpcsFile->numTokens; $i++) {
            if ($i !== $start && isset($endTokens[$tokens[$i]['code']]) === true) {
                // Found the end of the statement.
                if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS
                    || $tokens[$i]['code'] === T_CLOSE_SQUARE_BRACKET
                    || $tokens[$i]['code'] === T_CLOSE_CURLY_BRACKET
                    || $tokens[$i]['code'] === T_CLOSE_SHORT_ARRAY
                    || $tokens[$i]['code'] === T_OPEN_TAG
                    || $tokens[$i]['code'] === T_CLOSE_TAG
                ) {
                    return $lastNotEmpty;
                }

                return $i;
            }

            // Skip nested statements.
            if (isset($tokens[$i]['scope_closer']) === true
                && ($i === $tokens[$i]['scope_opener']
                || $i === $tokens[$i]['scope_condition'])
            ) {
                if ($tokens[$i]['type'] === 'T_FN') {
                    $lastNotEmpty = $tokens[$i]['scope_closer'];

                    // Minus 1 as the closer can be shared.
                    $i = ($tokens[$i]['scope_closer'] - 1);
                    continue;
                }

                if ($i === $start && isset(Tokens::$scopeOpeners[$tokens[$i]['code']]) === true) {
                    return $tokens[$i]['scope_closer'];
                }

                $i = $tokens[$i]['scope_closer'];
            } elseif (isset($tokens[$i]['bracket_closer']) === true
                && $i === $tokens[$i]['bracket_opener']
            ) {
                $i = $tokens[$i]['bracket_closer'];
            } elseif (isset($tokens[$i]['parenthesis_closer']) === true
                && $i === $tokens[$i]['parenthesis_opener']
            ) {
                $i = $tokens[$i]['parenthesis_closer'];
            } elseif ($tokens[$i]['code'] === T_OPEN_USE_GROUP) {
                $end = $phpcsFile->findNext(T_CLOSE_USE_GROUP, ($i + 1));
                if ($end !== false) {
                    $i = $end;
                }
            } elseif (isset(Collections::arrowFunctionTokensBC()[$tokens[$i]['code']]) === true) {
                // Potentially a PHP 7.4 arrow function in combination with PHP < 7.4 or PHPCS < 3.5.3/3.5.4.
                $arrowFunctionOpenClose = FunctionDeclarations::getArrowFunctionOpenClose($phpcsFile, $i);
                if ($arrowFunctionOpenClose !== false) {
                    if ($i === $start) {
                        return $arrowFunctionOpenClose['scope_closer'];
                    }

                    $lastNotEmpty = $arrowFunctionOpenClose['scope_closer'];

                    // Minus 1 as the closer can be shared.
                    $i = ($arrowFunctionOpenClose['scope_closer'] - 1);
                    continue;
                }
            }

            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === false) {
                $lastNotEmpty = $i;
            }
        }

        return ($phpcsFile->numTokens - 1);
    }

    /**
     * Determine if the passed token has a condition of one of the passed types.
     *
     * PHPCS cross-version compatible version of the `File::hasCondition()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - This method has received no significant code updates since PHPCS 2.6.0.
     *
     * @see \PHP_CodeSniffer\Files\File::hasCondition()  Original source.
     * @see \PHPCSUtils\Utils\Conditions::hasCondition() PHPCSUtils native alternative.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the token we are checking.
     * @param int|string|array            $types     The type(s) of tokens to search for.
     *
     * @return bool
     */
    public static function hasCondition(File $phpcsFile, $stackPtr, $types)
    {
        return $phpcsFile->hasCondition($stackPtr, $types);
    }

    /**
     * Return the position of the condition for the passed token.
     *
     * PHPCS cross-version compatible version of the `File::getCondition()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 1.3.0.
     * - PHPCS 3.5.4: New `$first` parameter which allows for the closest matching token to be returned.
     *                By default, it continues to return the first matched token found from the top of the file.
     *
     * @see \PHP_CodeSniffer\Files\File::getCondition()  Original source.
     * @see \PHPCSUtils\Utils\Conditions::getCondition() More versatile alternative.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Added support for the PHPCS 3.5.4 `$first` parameter.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the token we are checking.
     * @param int|string                  $type      The type of token to search for.
     * @param bool                        $first     If `true`, will return the matched condition
     *                                               furthest away from the passed token.
     *                                               If `false`, will return the matched condition
     *                                               closest to the passed token.
     *
     * @return int|false Integer stack pointer to the condition or `FALSE` if the token
     *                   does not have the condition.
     */
    public static function getCondition(File $phpcsFile, $stackPtr, $type, $first = true)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (isset($tokens[$stackPtr]['conditions']) === false) {
            return false;
        }

        $conditions = $tokens[$stackPtr]['conditions'];
        if ($first === false) {
            $conditions = array_reverse($conditions, true);
        }

        foreach ($conditions as $token => $condition) {
            if ($condition === $type) {
                return $token;
            }
        }

        return false;
    }

    /**
     * Returns the name of the class that the specified class extends.
     * (works for classes, anonymous classes and interfaces)
     *
     * PHPCS cross-version compatible version of the `File::findExtendedClassName()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 1.2.0.
     * - PHPCS 2.8.0: Now supports anonymous classes.
     * - PHPCS 3.1.0: Now supports interfaces extending interfaces (incorrectly, only supporting
     *                single interface extension).
     * - PHPCS 3.3.2: Fixed bug causing bleed through with nested classes, PHPCS#2127.
     *
     * @see \PHP_CodeSniffer\Files\File::findExtendedClassName()          Original source.
     * @see \PHPCSUtils\Utils\ObjectDeclarations::findExtendedClassName() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The stack position of the class or interface.
     *
     * @return string|false The extended class name or `FALSE` on error or if there
     *                      is no extended class name.
     */
    public static function findExtendedClassName(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] !== T_CLASS
            && $tokens[$stackPtr]['code'] !== T_ANON_CLASS
            && $tokens[$stackPtr]['code'] !== T_INTERFACE
        ) {
            return false;
        }

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return false;
        }

        $classOpenerIndex = $tokens[$stackPtr]['scope_opener'];
        $extendsIndex     = $phpcsFile->findNext(T_EXTENDS, $stackPtr, $classOpenerIndex);
        if ($extendsIndex === false) {
            return false;
        }

        $find = [
            T_NS_SEPARATOR => T_NS_SEPARATOR,
            T_STRING       => T_STRING,
            T_WHITESPACE   => T_WHITESPACE,
        ];

        // BC for PHP 8 in combination with PHPCS < 3.5.7.
        $find += Collections::nameTokens();

        $end  = $phpcsFile->findNext($find, ($extendsIndex + 1), ($classOpenerIndex + 1), true);
        $name = $phpcsFile->getTokensAsString(($extendsIndex + 1), ($end - $extendsIndex - 1));
        $name = trim($name);

        if ($name === '') {
            return false;
        }

        return $name;
    }

    /**
     * Returns the names of the interfaces that the specified class implements.
     *
     * PHPCS cross-version compatible version of the `File::findImplementedInterfaceNames()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 2.7.0.
     * - PHPCS 2.8.0: Now supports anonymous classes.
     *
     * @see \PHP_CodeSniffer\Files\File::findImplementedInterfaceNames()          Original source.
     * @see \PHPCSUtils\Utils\ObjectDeclarations::findImplementedInterfaceNames() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The stack position of the class.
     *
     * @return array|false Array with names of the implemented interfaces or `FALSE` on
     *                     error or if there are no implemented interface names.
     */
    public static function findImplementedInterfaceNames(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] !== T_CLASS
            && $tokens[$stackPtr]['code'] !== T_ANON_CLASS
        ) {
            return false;
        }

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return false;
        }

        $classOpenerIndex = $tokens[$stackPtr]['scope_opener'];
        $implementsIndex  = $phpcsFile->findNext(T_IMPLEMENTS, $stackPtr, $classOpenerIndex);
        if ($implementsIndex === false) {
            return false;
        }

        $find = [
            T_NS_SEPARATOR => T_NS_SEPARATOR,
            T_STRING       => T_STRING,
            T_WHITESPACE   => T_WHITESPACE,
            T_COMMA        => T_COMMA,
        ];

        // BC for PHP 8 in combination with PHPCS < 3.5.7.
        $find += Collections::nameTokens();

        $end  = $phpcsFile->findNext($find, ($implementsIndex + 1), ($classOpenerIndex + 1), true);
        $name = $phpcsFile->getTokensAsString(($implementsIndex + 1), ($end - $implementsIndex - 1));
        $name = trim($name);

        if ($name === '') {
            return false;
        } else {
            $names = explode(',', $name);
            $names = array_map('trim', $names);
            return $names;
        }
    }
}
