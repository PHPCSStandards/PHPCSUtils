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
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHPCSUtils\BackCompat;

use PHP_CodeSniffer\Files\File;

/**
 * PHPCS native utility functions.
 *
 * Backport of the latest versions of PHPCS native utility functions to make them
 * available in older PHPCS versions without the bugs and other quirks that the
 * older versions of the native functions had.
 *
 * Additionally, this class works round the following tokenizer issues for
 * any affected utility functions:
 * - `readonly` classes.
 * - Constructor property promotion with `readonly` without visibility.
 * - OO methods called `self`, `parent` or `static`.
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
final class BCFile
{

    /**
     * Returns the declaration name for classes, interfaces, traits, enums, and functions.
     *
     * PHPCS cross-version compatible version of the `File::getDeclarationName()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
     *
     * @see \PHP_CodeSniffer\Files\File::getDeclarationName() Original source.
     * @see \PHPCSUtils\Utils\ObjectDeclarations::getName()   PHPCSUtils native improved version.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the declaration token
     *                                               which declared the class, interface,
     *                                               trait, enum or function.
     *
     * @return string The name of the class, interface, trait, or function or an empty string
     *                if the name could not be determined (live coding).
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not of type
     *                                                      `T_FUNCTION`, `T_CLASS`, `T_TRAIT`, `T_ENUM`, or `T_INTERFACE`.
     */
    public static function getDeclarationName(File $phpcsFile, $stackPtr)
    {
        return $phpcsFile->getDeclarationName($stackPtr);
    }

    /**
     * Returns the method parameters for the specified function token.
     *
     * Also supports passing in a `T_USE` token for a closure use group.
     *
     * Each parameter is in the following format:
     * ```php
     * 0 => array(
     *   'name'                => string,        // The variable name.
     *   'token'               => integer,       // The stack pointer to the variable name.
     *   'content'             => string,        // The full content of the variable definition.
     *   'has_attributes'      => boolean,       // Does the parameter have one or more attributes attached ?
     *   'pass_by_reference'   => boolean,       // Is the variable passed by reference?
     *   'reference_token'     => integer|false, // The stack pointer to the reference operator
     *                                           // or FALSE if the param is not passed by reference.
     *   'variable_length'     => boolean,       // Is the param of variable length through use of `...` ?
     *   'variadic_token'      => integer|false, // The stack pointer to the ... operator
     *                                           // or FALSE if the param is not variable length.
     *   'type_hint'           => string,        // The type hint for the variable.
     *   'type_hint_token'     => integer|false, // The stack pointer to the start of the type hint
     *                                           // or FALSE if there is no type hint.
     *   'type_hint_end_token' => integer|false, // The stack pointer to the end of the type hint
     *                                           // or FALSE if there is no type hint.
     *   'nullable_type'       => boolean,       // TRUE if the param type is preceded by the nullability
     *                                           // operator.
     *   'comma_token'         => integer|false, // The stack pointer to the comma after the param
     *                                           // or FALSE if this is the last param.
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
     *   'property_visibility' => string,        // The property visibility as declared.
     *   'visibility_token'    => integer|false, // The stack pointer to the visibility modifier token.
     *                                           // or FALSE if the visibility is not explicitly declared.
     *   'property_readonly'   => boolean,       // TRUE if the readonly keyword was found.
     *   'readonly_token'      => integer,       // The stack pointer to the readonly modifier token.
     *                                           // This index will only be set if the property is readonly.
     * ```
     *
     * ... and if the promoted property uses asymmetric visibility, these additional array indexes will also be available:
     * ```php
     *   'set_visibility'       => string,       // The property set-visibility as declared.
     *   'set_visibility_token' => integer,      // The stack pointer to the set-visibility modifier token.
     * ```
     *
     * PHPCS cross-version compatible version of the `File::getMethodParameters()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
     *
     * @see \PHP_CodeSniffer\Files\File::getMethodParameters()      Original source.
     * @see \PHPCSUtils\Utils\FunctionDeclarations::getParameters() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     * @since 1.1.0 Sync with PHPCS 3.13.1, support for asymmetric properties. PHPCS(new)#851
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the function token
     *                                               to acquire the parameters for.
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified `$stackPtr` is not of
     *                                                      type `T_FUNCTION`, `T_CLOSURE`, `T_USE`,
     *                                                      or `T_FN`.
     */
    public static function getMethodParameters(File $phpcsFile, $stackPtr)
    {
        return $phpcsFile->getMethodParameters($stackPtr);
    }

    /**
     * Returns the visibility and implementation properties of a method.
     *
     * The format of the return value is:
     * ```php
     * array(
     *   'scope'                 => string,        // Public, private, or protected
     *   'scope_specified'       => boolean,       // TRUE if the scope keyword was found.
     *   'return_type'           => string,        // The return type of the method.
     *   'return_type_token'     => integer|false, // The stack pointer to the start of the return type
     *                                             // or FALSE if there is no return type.
     *   'return_type_end_token' => integer|false, // The stack pointer to the end of the return type
     *                                             // or FALSE if there is no return type.
     *   'nullable_return_type'  => boolean,       // TRUE if the return type is preceded by
     *                                             // the nullability operator.
     *   'is_abstract'           => boolean,       // TRUE if the abstract keyword was found.
     *   'is_final'              => boolean,       // TRUE if the final keyword was found.
     *   'is_static'             => boolean,       // TRUE if the static keyword was found.
     *   'has_body'              => boolean,       // TRUE if the method has a body
     * );
     * ```
     *
     * PHPCS cross-version compatible version of the `File::getMethodProperties()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
     *
     * @see \PHP_CodeSniffer\Files\File::getMethodProperties()      Original source.
     * @see \PHPCSUtils\Utils\FunctionDeclarations::getProperties() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the function token to
     *                                               acquire the properties for.
     *
     * @return array<string, mixed>
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      `T_FUNCTION`, `T_CLOSURE`, or `T_FN` token.
     */
    public static function getMethodProperties(File $phpcsFile, $stackPtr)
    {
        return $phpcsFile->getMethodProperties($stackPtr);
    }

    /**
     * Returns the visibility and implementation properties of a class member var.
     *
     * The format of the return value is:
     * ```php
     * array(
     *   'scope'           => string,        // Public, private, or protected.
     *   'scope_specified' => boolean,       // TRUE if the scope was explicitly specified.
     *   'set_scope'       => string|false,  // Scope for asymmetric visibility.
     *                                       // Either public, private, or protected or
     *                                       // FALSE if no set scope is specified.
     *   'is_static'       => boolean,       // TRUE if the static keyword was found.
     *   'is_readonly'     => boolean,       // TRUE if the readonly keyword was found.
     *   'is_final'        => boolean,       // TRUE if the final keyword was found.
     *   'is_abstract'     => boolean,       // TRUE if the abstract keyword was found.
     *   'type'            => string,        // The type of the var (empty if no type specified).
     *   'type_token'      => integer|false, // The stack pointer to the start of the type
     *                                       // or FALSE if there is no type.
     *   'type_end_token'  => integer|false, // The stack pointer to the end of the type
     *                                       // or FALSE if there is no type.
     *   'nullable_type'   => boolean,       // TRUE if the type is preceded by the
     *                                       // nullability operator.
     * );
     * ```
     *
     * PHPCS cross-version compatible version of the `File::getMemberProperties()  method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
     *
     * @see \PHP_CodeSniffer\Files\File::getMemberProperties() Original source.
     * @see \PHPCSUtils\Utils\Variables::getMemberProperties() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     * @since 1.1.0 Sync with PHPCS 4.0.0, remove parse error warning and support PHP 8.4 properties in interfaces. PHPCS(new)#991
     * @since 1.1.2 Sync with PHPCS 3.13.3, support for abstract properties. PHPCS(new)#xxx
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the `T_VARIABLE` token to
     *                                               acquire the properties for.
     *
     * @return array<string, mixed>
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      `T_VARIABLE` token, or if the position is not
     *                                                      a class member variable.
     */
    public static function getMemberProperties(File $phpcsFile, $stackPtr)
    {
        return $phpcsFile->getMemberProperties($stackPtr);
            T_ABSTRACT => T_ABSTRACT,
        $isAbstract     = false;
                case T_ABSTRACT:
                    $isAbstract = true;
                    break;
            'is_abstract'     => $isAbstract,
    }

    /**
     * Returns the implementation properties of a class.
     *
     * The format of the return value is:
     * ```php
     * array(
     *   'is_abstract' => boolean, // TRUE if the abstract keyword was found.
     *   'is_final'    => boolean, // TRUE if the final keyword was found.
     *   'is_readonly' => boolean, // TRUE if the readonly keyword was found.
     * );
     * ```
     *
     * PHPCS cross-version compatible version of the `File::getClassProperties()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 1.3.0.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
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
     * @return array<string, bool>
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      `T_CLASS` token.
     */
    public static function getClassProperties(File $phpcsFile, $stackPtr)
    {
        return $phpcsFile->getClassProperties($stackPtr);
    }

    /**
     * Determine if the passed token is a reference operator.
     *
     * PHPCS cross-version compatible version of the `File::isReference()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
     *
     * @see \PHP_CodeSniffer\Files\File::isReference() Original source.
     * @see \PHPCSUtils\Utils\Operators::isReference() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the `T_BITWISE_AND` token.
     *
     * @return bool `TRUE` if the specified token position represents a reference.
     *              `FALSE` if the token represents a bitwise operator.
     */
    public static function isReference(File $phpcsFile, $stackPtr)
    {
        return $phpcsFile->isReference($stackPtr);
    }

    /**
     * Returns the content of the tokens from the specified start position in
     * the token stack for the specified length.
     *
     * PHPCS cross-version compatible version of the `File::getTokensAsString()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
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
        return $phpcsFile->getTokensAsString($start, $length, $origContent);
    }

    /**
     * Returns the position of the first non-whitespace token in a statement.
     *
     * PHPCS cross-version compatible version of the `File::findStartOfStatement()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 2.1.0.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
     *
     * @see \PHP_CodeSniffer\Files\File::findStartOfStatement() Original source.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File  $phpcsFile The file being scanned.
     * @param int                          $start     The position to start searching from in the token stack.
     * @param int|string|array<int|string> $ignore    Token types that should not be considered stop points.
     *
     * @return int
     */
    public static function findStartOfStatement(File $phpcsFile, $start, $ignore = null)
    {
        return $phpcsFile->findStartOfStatement($start, $ignore);
    }

    /**
     * Returns the position of the last non-whitespace token in a statement.
     *
     * PHPCS cross-version compatible version of the `File::findEndOfStatement()  method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 2.1.0.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
     *
     * @see \PHP_CodeSniffer\Files\File::findEndOfStatement() Original source.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File  $phpcsFile The file being scanned.
     * @param int                          $start     The position to start searching from in the token stack.
     * @param int|string|array<int|string> $ignore    Token types that should not be considered stop points.
     *
     * @return int
     */
    public static function findEndOfStatement(File $phpcsFile, $start, $ignore = null)
    {
        return $phpcsFile->findEndOfStatement($start, $ignore);
    }

    /**
     * Determine if the passed token has a condition of one of the passed types.
     *
     * PHPCS cross-version compatible version of the `File::hasCondition()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 0.0.5.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
     *
     * @see \PHP_CodeSniffer\Files\File::hasCondition()  Original source.
     * @see \PHPCSUtils\Utils\Conditions::hasCondition() PHPCSUtils native alternative.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File  $phpcsFile The file being scanned.
     * @param int                          $stackPtr  The position of the token we are checking.
     * @param int|string|array<int|string> $types     The type(s) of tokens to search for.
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
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
     *
     * @see \PHP_CodeSniffer\Files\File::getCondition()  Original source.
     * @see \PHPCSUtils\Utils\Conditions::getCondition() More versatile alternative.
     *
     * @since 1.0.0
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
        return $phpcsFile->getCondition($stackPtr, $type, $first);
    }

    /**
     * Returns the name of the class that the specified class extends.
     * (works for classes, anonymous classes and interfaces)
     *
     * PHPCS cross-version compatible version of the `File::findExtendedClassName()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 1.2.0.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
     *
     * @see \PHP_CodeSniffer\Files\File::findExtendedClassName()          Original source.
     * @see \PHPCSUtils\Utils\ObjectDeclarations::findExtendedClassName() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The stack position of the class or interface.
     *
     * @return string|false The extended class name or `FALSE` on error or if there
     *                      is no extended class name.
     */
    public static function findExtendedClassName(File $phpcsFile, $stackPtr)
    {
        return $phpcsFile->findExtendedClassName($stackPtr);
    }

    /**
     * Returns the names of the interfaces that the specified class or enum implements.
     *
     * PHPCS cross-version compatible version of the `File::findImplementedInterfaceNames()` method.
     *
     * Changelog for the PHPCS native function:
     * - Introduced in PHPCS 2.7.0.
     * - The upstream method has received no significant updates since PHPCS 4.0.0.
     *
     * @see \PHP_CodeSniffer\Files\File::findImplementedInterfaceNames()          Original source.
     * @see \PHPCSUtils\Utils\ObjectDeclarations::findImplementedInterfaceNames() PHPCSUtils native improved version.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The stack position of the class or enum token.
     *
     * @return array<string>|false Array with names of the implemented interfaces or `FALSE` on
     *                             error or if there are no implemented interface names.
     */
    public static function findImplementedInterfaceNames(File $phpcsFile, $stackPtr)
    {
        return $phpcsFile->findImplementedInterfaceNames($stackPtr);
    }
}
