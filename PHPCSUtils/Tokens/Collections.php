<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tokens;

/**
 * Collections of related tokens as often used and needed for sniffs.
 *
 * These are additional "token groups" to compliment the ones available through the PHPCS
 * native {@see \PHP_CodeSniffer\Util\Tokens} class.
 *
 * @see \PHP_CodeSniffer\Util\Tokens    PHPCS native token groups.
 * @see \PHPCSUtils\BackCompat\BCTokens Backward compatible version of the PHPCS native token groups.
 *
 * @since 1.0.0
 */
class Collections
{

    /**
     * Modifier keywords which can be used for a class declaration.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    public static $classModifierKeywords = [
        \T_FINAL    => \T_FINAL,
        \T_ABSTRACT => \T_ABSTRACT,
    ];

    /**
     * List of tokens which represent "closed" scopes.
     *
     * I.e. anything declared within that scope - except for other closed scopes - is
     * outside of the global namespace.
     *
     * This list doesn't contain `T_NAMESPACE` on purpose as variables declared
     * within a namespace scope are still global and not limited to that namespace.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    public static $closedScopes = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_INTERFACE  => \T_INTERFACE,
        \T_TRAIT      => \T_TRAIT,
        \T_FUNCTION   => \T_FUNCTION,
        \T_CLOSURE    => \T_CLOSURE,
    ];

    /**
     * Tokens which are used to create lists.
     *
     * @since 1.0.0
     *
     * @see \PHPCSUtils\Tokens\Collections::$shortListTokens Related property containing only tokens used
     *                                                       for short lists.
     *
     * @var array <int|string> => <int|string>
     */
    public static $listTokens = [
        \T_LIST              => \T_LIST,
        \T_OPEN_SHORT_ARRAY  => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY => \T_CLOSE_SHORT_ARRAY,
    ];

    /**
     * Tokens which are used to create lists.
     *
     * List which is backward-compatible with PHPCS < 3.3.0.
     * Should only be used selectively.
     *
     * @since 1.0.0
     *
     * @see \PHPCSUtils\Tokens\Collections::$shortListTokensBC Related property containing only tokens used
     *                                                         for short lists (cross-version).
     *
     * @var array <int|string> => <int|string>
     */
    public static $listTokensBC = [
        \T_LIST                 => \T_LIST,
        \T_OPEN_SHORT_ARRAY     => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY    => \T_CLOSE_SHORT_ARRAY,
        \T_OPEN_SQUARE_BRACKET  => \T_OPEN_SQUARE_BRACKET,
        \T_CLOSE_SQUARE_BRACKET => \T_CLOSE_SQUARE_BRACKET,
    ];

    /**
     * List of tokens which can end a namespace declaration statement.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    public static $namespaceDeclarationClosers = [
        \T_SEMICOLON          => \T_SEMICOLON,
        \T_OPEN_CURLY_BRACKET => \T_OPEN_CURLY_BRACKET,
        \T_CLOSE_TAG          => \T_CLOSE_TAG,
    ];

    /**
     * OO structures which can use the `extends` keyword.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    public static $OOCanExtend = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_INTERFACE  => \T_INTERFACE,
    ];

    /**
     * OO structures which can use the `implements` keyword.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    public static $OOCanImplement = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
    ];

    /**
     * OO scopes in which constants can be declared.
     *
     * Note: traits can not declare constants.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    public static $OOConstantScopes = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_INTERFACE  => \T_INTERFACE,
    ];

    /**
     * OO scopes in which properties can be declared.
     *
     * Note: interfaces can not declare properties.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    public static $OOPropertyScopes = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_TRAIT      => \T_TRAIT,
    ];

    /**
     * Token types which can be encountered in a parameter type declaration.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    public static $parameterTypeTokens = [
        \T_ARRAY_HINT   => \T_ARRAY_HINT, // PHPCS < 3.3.0.
        \T_CALLABLE     => \T_CALLABLE,
        \T_SELF         => \T_SELF,
        \T_PARENT       => \T_PARENT,
        \T_STRING       => \T_STRING,
        \T_NS_SEPARATOR => \T_NS_SEPARATOR,
    ];

    /**
     * Modifier keywords which can be used for a property declaration.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    public static $propertyModifierKeywords = [
        \T_PUBLIC    => \T_PUBLIC,
        \T_PRIVATE   => \T_PRIVATE,
        \T_PROTECTED => \T_PROTECTED,
        \T_STATIC    => \T_STATIC,
        \T_VAR       => \T_VAR,
    ];

    /**
     * Token types which can be encountered in a property type declaration.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    public static $propertyTypeTokens = [
        \T_ARRAY_HINT   => \T_ARRAY_HINT, // PHPCS < 3.3.0.
        \T_CALLABLE     => \T_CALLABLE,
        \T_SELF         => \T_SELF,
        \T_PARENT       => \T_PARENT,
        \T_STRING       => \T_STRING,
        \T_NS_SEPARATOR => \T_NS_SEPARATOR,
    ];

    /**
     * Token types which can be encountered in a return type declaration.
     *
     * @since 1.0.0
     *
     * @var array <int|string> => <int|string>
     */
    public static $returnTypeTokens = [
        \T_STRING       => \T_STRING,
        \T_CALLABLE     => \T_CALLABLE,
        \T_SELF         => \T_SELF,
        \T_PARENT       => \T_PARENT,
        \T_NS_SEPARATOR => \T_NS_SEPARATOR,
        \T_RETURN_TYPE  => \T_RETURN_TYPE, // PHPCS 2.4.0 < 3.3.0.
        \T_ARRAY_HINT   => \T_ARRAY_HINT, // PHPCS < 2.8.0.
    ];

    /**
     * Tokens which are used for short lists.
     *
     * @since 1.0.0
     *
     * @see \PHPCSUtils\Tokens\Collections::$listTokens Related property containing all tokens used for lists.
     *
     * @var array <int|string> => <int|string>
     */
    public static $shortListTokens = [
        \T_OPEN_SHORT_ARRAY  => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY => \T_CLOSE_SHORT_ARRAY,
    ];

    /**
     * Tokens which are used for short lists.
     *
     * List which is backward-compatible with PHPCS < 3.3.0.
     * Should only be used selectively.
     *
     * @since 1.0.0
     *
     * @see \PHPCSUtils\Tokens\Collections::$listTokensBC Related property containing all tokens used for lists
     *                                                    (cross-version).
     *
     * @var array <int|string> => <int|string>
     */
    public static $shortListTokensBC = [
        \T_OPEN_SHORT_ARRAY     => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY    => \T_CLOSE_SHORT_ARRAY,
        \T_OPEN_SQUARE_BRACKET  => \T_OPEN_SQUARE_BRACKET,
        \T_CLOSE_SQUARE_BRACKET => \T_CLOSE_SQUARE_BRACKET,
    ];
}
