<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tokens;

use PHPCSUtils\Exceptions\InvalidTokenArray;

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
 * @since 1.0.0-alpha4 Dropped support for PHPCS < 3.7.1.
 * @since 1.0.0-alpha4 Direct property access is deprecated for forward-compatibility reasons.
 *                     Use the methods of the same name as the property instead.
 *
 * @method static array arrayOpenTokensBC()           Tokens which can open an array (PHPCS cross-version compatible).
 * @method static array arrayTokens()                 Tokens which are used to create arrays.
 * @method static array arrayTokensBC()               Tokens which are used to create arrays
 *                                                    (PHPCS cross-version compatible).
 * @method static array classModifierKeywords()       Modifier keywords which can be used for a class declaration.
 * @method static array closedScopes()                List of tokens which represent "closed" scopes.
 * @method static array constantModifierKeywords()    Tokens which can be used as modifiers for a constant
 *                                                    declaration (in OO structures).
 * @method static array controlStructureTokens()      Control structure tokens.
 * @method static array functionDeclarationTokens()   Tokens which represent a keyword which starts
 *                                                    a function declaration.
 * @method static array incrementDecrementOperators() Increment/decrement operator tokens.
 * @method static array listTokens()                  Tokens which are used to create lists.
 * @method static array listTokensBC()                Tokens which are used to create lists
 *                                                    (PHPCS cross-version compatible)
 * @method static array namespaceDeclarationClosers() List of tokens which can end a namespace declaration statement.
 * @method static array nameTokens()                  Tokens used for "names", be it namespace, OO, function
 *                                                    or constant names.
 * @method static array objectOperators()             Object operator tokens.
 * @method static array phpOpenTags()                 Tokens which open PHP.
 * @method static array propertyModifierKeywords()    Modifier keywords which can be used for a property declaration.
 * @method static array shortArrayListOpenTokensBC()  Tokens which can open a short array or short list
 *                                                    (PHPCS cross-version compatible).
 * @method static array shortArrayTokens()            Tokens which are used for short arrays.
 * @method static array shortArrayTokensBC()          Tokens which are used for short arrays
 *                                                    (PHPCS cross-version compatible).
 * @method static array shortListTokens()             Tokens which are used for short lists.
 * @method static array shortListTokensBC()           Tokens which are used for short lists
 *                                                    (PHPCS cross-version compatible).
 */
final class Collections
{

    /**
     * DEPRECATED: Tokens for control structures which can use the alternative control structure syntax.
     *
     * @since 1.0.0-alpha2
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::alternativeControlStructureSyntaxes()}
     *                          method instead.
     *
     * @var array <int> => <int>
     */
    public static $alternativeControlStructureSyntaxTokens = [
        \T_IF      => \T_IF,
        \T_ELSEIF  => \T_ELSEIF,
        \T_ELSE    => \T_ELSE,
        \T_FOR     => \T_FOR,
        \T_FOREACH => \T_FOREACH,
        \T_SWITCH  => \T_SWITCH,
        \T_WHILE   => \T_WHILE,
        \T_DECLARE => \T_DECLARE,
    ];

    /**
     * DEPRECATED: Tokens representing alternative control structure syntax closer keywords.
     *
     * @since 1.0.0-alpha2
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::alternativeControlStructureSyntaxClosers()}
     *                          method instead.
     *
     * @var array <int> => <int>
     */
    public static $alternativeControlStructureSyntaxCloserTokens = [
        \T_ENDIF      => \T_ENDIF,
        \T_ENDFOR     => \T_ENDFOR,
        \T_ENDFOREACH => \T_ENDFOREACH,
        \T_ENDWHILE   => \T_ENDWHILE,
        \T_ENDSWITCH  => \T_ENDSWITCH,
        \T_ENDDECLARE => \T_ENDDECLARE,
    ];

    /**
     * Tokens which can open an array (PHPCS cross-version compatible).
     *
     * Includes `T_OPEN_SQUARE_BRACKET` to allow for handling intermittent tokenizer issues related
     * to the retokenization to `T_OPEN_SHORT_ARRAY`.
     * Should only be used selectively.
     *
     * @see \PHPCSUtils\Tokens\Collections::arrayTokensBC()      Related method to retrieve tokens used
     *                                                           for arrays (PHPCS cross-version).
     * @see \PHPCSUtils\Tokens\Collections::shortArrayTokensBC() Related method to retrieve only tokens used
     *                                                           for short arrays (PHPCS cross-version).
     *
     * @since 1.0.0-alpha4 Use the {@see Collections::arrayOpenTokensBC()} method for access.
     *
     * @return array <int|string> => <int|string>
     */
    private static $arrayOpenTokensBC = [
        \T_ARRAY               => \T_ARRAY,
        \T_OPEN_SHORT_ARRAY    => \T_OPEN_SHORT_ARRAY,
        \T_OPEN_SQUARE_BRACKET => \T_OPEN_SQUARE_BRACKET,
    ];

    /**
     * DEPRECATED: Tokens which are used to create arrays.
     *
     * @see \PHPCSUtils\Tokens\Collections::arrayOpenTokensBC() Related method to retrieve only the "open" tokens
     *                                                          used for arrays (PHPCS cross-version compatible).
     * @see \PHPCSUtils\Tokens\Collections::arrayTokensBC()     Related method to retrieve tokens used
     *                                                          for arrays (PHPCS cross-version compatible).
     * @see \PHPCSUtils\Tokens\Collections::shortArrayTokens()  Related method to retrieve only tokens used
     *                                                          for short arrays.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::arrayTokens()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $arrayTokens = [
        \T_ARRAY             => \T_ARRAY,
        \T_OPEN_SHORT_ARRAY  => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY => \T_CLOSE_SHORT_ARRAY,
    ];

    /**
     * DEPRECATED: Tokens which are used to create arrays (PHPCS cross-version compatible).
     *
     * Includes `T_OPEN_SQUARE_BRACKET` and `T_CLOSE_SQUARE_BRACKET` to allow for handling
     * intermittent tokenizer issues related to the retokenization to `T_OPEN_SHORT_ARRAY`.
     * Should only be used selectively.
     *
     * @see \PHPCSUtils\Tokens\Collections::arrayOpenTokensBC()  Related method to retrieve only the "open" tokens
     *                                                           used for arrays (PHPCS cross-version compatible).
     * @see \PHPCSUtils\Tokens\Collections::shortArrayTokensBC() Related method to retrieve only tokens used
     *                                                           for short arrays (PHPCS cross-version compatible).
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::arrayTokensBC()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $arrayTokensBC = [
        \T_ARRAY                => \T_ARRAY,
        \T_OPEN_SHORT_ARRAY     => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY    => \T_CLOSE_SHORT_ARRAY,
        \T_OPEN_SQUARE_BRACKET  => \T_OPEN_SQUARE_BRACKET,
        \T_CLOSE_SQUARE_BRACKET => \T_CLOSE_SQUARE_BRACKET,
    ];

    /**
     * DEPRECATED: Modifier keywords which can be used for a class declaration.
     *
     * @since 1.0.0-alpha1
     * @since 1.0.0-alpha4 Added the T_READONLY token for PHP 8.2 readonly classes.
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::classModifierKeywords()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $classModifierKeywords = [
        \T_FINAL    => \T_FINAL,
        \T_ABSTRACT => \T_ABSTRACT,
        \T_READONLY => \T_READONLY,
    ];

    /**
     * DEPRECATED: List of tokens which represent "closed" scopes.
     *
     * I.e. anything declared within that scope - except for other closed scopes - is
     * outside of the global namespace.
     *
     * This list doesn't contain the `T_NAMESPACE` token on purpose as variables declared
     * within a namespace scope are still global and not limited to that namespace.
     *
     * @since 1.0.0-alpha1
     * @since 1.0.0-alpha4 Added the PHP 8.1 T_ENUM token.
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::closedScopes()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $closedScopes = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_INTERFACE  => \T_INTERFACE,
        \T_TRAIT      => \T_TRAIT,
        \T_ENUM       => \T_ENUM,
        \T_FUNCTION   => \T_FUNCTION,
        \T_CLOSURE    => \T_CLOSURE,
    ];

    /**
     * Modifier keywords which can be used for constant declarations (in OO structures).
     *
     * - PHP 7.1 added class constants visibility support.
     * - PHP 8.1 added support for final class constants.
     *
     * @since 1.0.0-alpha4 Use the {@see Collections::constantModifierKeywords()} method for access.
     *
     * @var array <int|string> => <int|string>
     */
    private static $constantModifierKeywords = [
        \T_PUBLIC    => \T_PUBLIC,
        \T_PRIVATE   => \T_PRIVATE,
        \T_PROTECTED => \T_PROTECTED,
        \T_FINAL     => \T_FINAL,
    ];

    /**
     * DEPRECATED: Control structure tokens.
     *
     * @since 1.0.0-alpha2
     * @since 1.0.0-alpha4 Added the T_MATCH token for PHP 8.0 match expressions.
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::controlStructureTokens()} method instead.
     *
     * @var array <int> => <int>
     */
    public static $controlStructureTokens = [
        \T_IF      => \T_IF,
        \T_ELSEIF  => \T_ELSEIF,
        \T_ELSE    => \T_ELSE,
        \T_FOR     => \T_FOR,
        \T_FOREACH => \T_FOREACH,
        \T_SWITCH  => \T_SWITCH,
        \T_DO      => \T_DO,
        \T_WHILE   => \T_WHILE,
        \T_DECLARE => \T_DECLARE,
        \T_MATCH   => \T_MATCH,
    ];

    /**
     * Tokens which represent a keyword which starts a function declaration.
     *
     * @since 1.0.0-alpha4 Use the {@see Collections::functionDeclarationTokens()} method for access.
     *
     * @return array <int|string> => <int|string>
     */
    private static $functionDeclarationTokens = [
        \T_FUNCTION => \T_FUNCTION,
        \T_CLOSURE  => \T_CLOSURE,
        \T_FN       => \T_FN,
    ];

    /**
     * DEPRECATED: Increment/decrement operator tokens.
     *
     * @since 1.0.0-alpha3
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::incrementDecrementOperators()} method instead.
     *
     * @var array <int> => <int>
     */
    public static $incrementDecrementOperators = [
        \T_DEC => \T_DEC,
        \T_INC => \T_INC,
    ];

    /**
     * DEPRECATED: Tokens which are used to create lists.
     *
     * @see \PHPCSUtils\Tokens\Collections::listTokensBC()    Related method to retrieve tokens used
     *                                                        for lists (PHPCS cross-version).
     * @see \PHPCSUtils\Tokens\Collections::shortListTokens() Related method to retrieve only tokens used
     *                                                        for short lists.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::listTokens()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $listTokens = [
        \T_LIST              => \T_LIST,
        \T_OPEN_SHORT_ARRAY  => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY => \T_CLOSE_SHORT_ARRAY,
    ];

    /**
     * DEPRECATED: Tokens which are used to create lists (PHPCS cross-version compatible).
     *
     * Includes `T_OPEN_SQUARE_BRACKET` and `T_CLOSE_SQUARE_BRACKET` to allow for handling
     * intermittent tokenizer issues related to the retokenization to `T_OPEN_SHORT_ARRAY`.
     * Should only be used selectively.
     *
     * @see \PHPCSUtils\Tokens\Collections::shortListTokensBC() Related method to retrieve only tokens used
     *                                                          for short lists (cross-version).
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::listTokensBC()} method instead.
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
     * DEPRECATED: Tokens for the PHP magic constants.
     *
     * @since 1.0.0-alpha3
     *
     * @deprecated 1.0.0-alpha4 Use the {@see \PHP_CodeSniffer\Util\Tokens::$magicConstants} property
     *                          or the {@see \PHPCSUtils\BackCompat\BCTokens::magicConstants()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $magicConstants = [
        \T_CLASS_C  => \T_CLASS_C,
        \T_DIR      => \T_DIR,
        \T_FILE     => \T_FILE,
        \T_FUNC_C   => \T_FUNC_C,
        \T_LINE     => \T_LINE,
        \T_METHOD_C => \T_METHOD_C,
        \T_NS_C     => \T_NS_C,
        \T_TRAIT_C  => \T_TRAIT_C,
    ];

    /**
     * DEPRECATED: List of tokens which can end a namespace declaration statement.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::namespaceDeclarationClosers()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $namespaceDeclarationClosers = [
        \T_SEMICOLON          => \T_SEMICOLON,
        \T_OPEN_CURLY_BRACKET => \T_OPEN_CURLY_BRACKET,
        \T_CLOSE_TAG          => \T_CLOSE_TAG,
    ];

    /**
     * Tokens used for "names", be it namespace, OO, function or constant names.
     *
     * Includes the tokens introduced in PHP 8.0 for "Namespaced names as single token".
     *
     * Note: the PHP 8.0 namespaced name tokens are backfilled in PHPCS since PHPCS 3.5.7,
     * but are not used yet (the PHP 8.0 tokenization is "undone" in PHPCS).
     * As of PHPCS 4.0.0, these tokens _will_ be used and the PHP 8.0 tokenization is respected.
     *
     * @link https://wiki.php.net/rfc/namespaced_names_as_token PHP RFC on namespaced names as single token
     *
     * @since 1.0.0-alpha4 Use the {@see Collections::nameTokens()} method for access.
     *
     * @return array <int|string> => <int|string>
     */
    private static $nameTokens = [
        \T_STRING               => \T_STRING,
        \T_NAME_QUALIFIED       => \T_NAME_QUALIFIED,
        \T_NAME_FULLY_QUALIFIED => \T_NAME_FULLY_QUALIFIED,
        \T_NAME_RELATIVE        => \T_NAME_RELATIVE,
    ];

    /**
     * DEPRECATED: Object operator tokens.
     *
     * @since 1.0.0-alpha3
     * @since 1.0.0-alpha4 Added the PHP 8.0 T_NULLSAFE_OBJECT_OPERATOR token.
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::objectOperators()} method instead.
     *
     * @var array <int> => <int>
     */
    public static $objectOperators = [
        \T_DOUBLE_COLON             => \T_DOUBLE_COLON,
        \T_OBJECT_OPERATOR          => \T_OBJECT_OPERATOR,
        \T_NULLSAFE_OBJECT_OPERATOR => \T_NULLSAFE_OBJECT_OPERATOR,
    ];

    /**
     * DEPRECATED: OO structures which can use the "extends" keyword.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::ooCanExtend()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $OOCanExtend = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_INTERFACE  => \T_INTERFACE,
    ];

    /**
     * DEPRECATED: OO structures which can use the "implements" keyword.
     *
     * @since 1.0.0-alpha1
     * @since 1.0.0-alpha4 Added the PHP 8.1 T_ENUM token.
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::ooCanImplement()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $OOCanImplement = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_ENUM       => \T_ENUM,
    ];

    /**
     * DEPRECATED: OO scopes in which constants can be declared.
     *
     * Note: traits can only declare constants since PHP 8.2.
     *
     * @since 1.0.0-alpha1
     * @since 1.0.0-alpha4 Added the PHP 8.1 T_ENUM token.
     * @since 1.0.0-alpha4 Added the T_TRAIT token for PHP 8.2 constants in traits.
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::ooConstantScopes()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $OOConstantScopes = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_INTERFACE  => \T_INTERFACE,
        \T_ENUM       => \T_ENUM,
        \T_TRAIT      => \T_TRAIT,
    ];

    /**
     * DEPRECATED: Tokens types used for "forwarding" calls within OO structures.
     *
     * @link https://www.php.net/language.oop5.paamayim-nekudotayim PHP Manual on OO forwarding calls
     *
     * @since 1.0.0-alpha3
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::ooHierarchyKeywords()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $OOHierarchyKeywords = [
        \T_PARENT => \T_PARENT,
        \T_SELF   => \T_SELF,
        \T_STATIC => \T_STATIC,
    ];

    /**
     * DEPRECATED: Tokens types which can be encountered in the fully/partially qualified name of an OO structure.
     *
     * @since 1.0.0-alpha3
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::namespacedNameTokens()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $OONameTokens = [
        \T_NS_SEPARATOR => \T_NS_SEPARATOR,
        \T_STRING       => \T_STRING,
        \T_NAMESPACE    => \T_NAMESPACE,
    ];

    /**
     * DEPRECATED: OO scopes in which properties can be declared.
     *
     * Note: interfaces can not declare properties.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::ooPropertyScopes()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $OOPropertyScopes = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_TRAIT      => \T_TRAIT,
    ];

    /**
     * DEPRECATED: Token types which can be encountered in a parameter type declaration.
     *
     * @since 1.0.0-alpha1
     * @since 1.0.0-alpha4 Added the T_TYPE_UNION, T_FALSE, T_NULL tokens for PHP 8.0 union type support.
     * @since 1.0.0-alpha4 Added the T_TYPE_INTERSECTION token for PHP 8.1 intersection type support.
     * @since 1.0.0-alpha4 Added the T_TRUE token for PHP 8.2 true type support.
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::parameterTypeTokens()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $parameterTypeTokens = [
        \T_CALLABLE          => \T_CALLABLE,
        \T_SELF              => \T_SELF,
        \T_PARENT            => \T_PARENT,
        \T_FALSE             => \T_FALSE,
        \T_TRUE              => \T_TRUE,
        \T_NULL              => \T_NULL,
        \T_STRING            => \T_STRING,
        \T_NS_SEPARATOR      => \T_NS_SEPARATOR,
        \T_TYPE_UNION        => \T_TYPE_UNION,
        \T_TYPE_INTERSECTION => \T_TYPE_INTERSECTION,
    ];

    /**
     * Tokens which open PHP.
     *
     * @since 1.0.0-alpha4 Use the {@see Collections::phpOpenTags()} method for access.
     *
     * @return array <int|string> => <int|string>
     */
    private static $phpOpenTags = [
        \T_OPEN_TAG           => \T_OPEN_TAG,
        \T_OPEN_TAG_WITH_ECHO => \T_OPEN_TAG_WITH_ECHO,
    ];

    /**
     * DEPRECATED: Modifier keywords which can be used for a property declaration.
     *
     * @since 1.0.0-alpha1
     * @since 1.0.0-alpha4 Added the T_READONLY token for PHP 8.1 readonly properties.
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::propertyModifierKeywords()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $propertyModifierKeywords = [
        \T_PUBLIC    => \T_PUBLIC,
        \T_PRIVATE   => \T_PRIVATE,
        \T_PROTECTED => \T_PROTECTED,
        \T_STATIC    => \T_STATIC,
        \T_VAR       => \T_VAR,
        \T_READONLY  => \T_READONLY,
    ];

    /**
     * DEPRECATED: Token types which can be encountered in a property type declaration.
     *
     * @since 1.0.0-alpha1
     * @since 1.0.0-alpha4 Added the T_TYPE_UNION, T_FALSE, T_NULL tokens for PHP 8.0 union type support.
     * @since 1.0.0-alpha4 Added the T_TYPE_INTERSECTION token for PHP 8.1 intersection type support.
     * @since 1.0.0-alpha4 Added the T_TRUE token for PHP 8.2 true type support.
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::propertyTypeTokens()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $propertyTypeTokens = [
        \T_CALLABLE          => \T_CALLABLE,
        \T_SELF              => \T_SELF,
        \T_PARENT            => \T_PARENT,
        \T_FALSE             => \T_FALSE,
        \T_TRUE              => \T_TRUE,
        \T_NULL              => \T_NULL,
        \T_STRING            => \T_STRING,
        \T_NS_SEPARATOR      => \T_NS_SEPARATOR,
        \T_TYPE_UNION        => \T_TYPE_UNION,
        \T_TYPE_INTERSECTION => \T_TYPE_INTERSECTION,
    ];

    /**
     * DEPRECATED: Token types which can be encountered in a return type declaration.
     *
     * @since 1.0.0-alpha1
     * @since 1.0.0-alpha4 Added the T_TYPE_UNION, T_FALSE, T_NULL tokens for PHP 8.0 union type support.
     * @since 1.0.0-alpha4 Added the T_TYPE_INTERSECTION token for PHP 8.1 intersection type support.
     * @since 1.0.0-alpha4 Added the T_TRUE token for PHP 8.2 true type support.
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::returnTypeTokens()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $returnTypeTokens = [
        \T_CALLABLE          => \T_CALLABLE,
        \T_SELF              => \T_SELF,
        \T_PARENT            => \T_PARENT,
        \T_STATIC            => \T_STATIC,
        \T_FALSE             => \T_FALSE,
        \T_TRUE              => \T_TRUE,
        \T_NULL              => \T_NULL,
        \T_STRING            => \T_STRING,
        \T_NS_SEPARATOR      => \T_NS_SEPARATOR,
        \T_TYPE_UNION        => \T_TYPE_UNION,
        \T_TYPE_INTERSECTION => \T_TYPE_INTERSECTION,
    ];

    /**
     * Tokens which can open a short array or short list (PHPCS cross-version compatible).
     *
     * Includes `T_OPEN_SQUARE_BRACKET` to allow for handling intermittent tokenizer issues related
     * to the retokenization to `T_OPEN_SHORT_ARRAY`.
     * Should only be used selectively.
     *
     * @since 1.0.0-alpha4 Use the {@see Collections::shortArrayListOpenTokensBC()} method for access.
     *
     * @return array <int|string> => <int|string>
     */
    private static $shortArrayListOpenTokensBC = [
        \T_OPEN_SHORT_ARRAY    => \T_OPEN_SHORT_ARRAY,
        \T_OPEN_SQUARE_BRACKET => \T_OPEN_SQUARE_BRACKET,
    ];

    /**
     * DEPRECATED: Tokens which are used for short arrays.
     *
     * @see \PHPCSUtils\Tokens\Collections::arrayTokens() Related method to retrieve all tokens used for arrays.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::shortArrayTokens()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $shortArrayTokens = [
        \T_OPEN_SHORT_ARRAY  => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY => \T_CLOSE_SHORT_ARRAY,
    ];

    /**
     * DEPRECATED: Tokens which are used for short arrays (PHPCS cross-version compatible).
     *
     * Includes `T_OPEN_SQUARE_BRACKET` and `T_CLOSE_SQUARE_BRACKET` to allow for handling
     * intermittent tokenizer issues related to the retokenization to `T_OPEN_SHORT_ARRAY`.
     * Should only be used selectively.
     *
     * @see \PHPCSUtils\Tokens\Collections::arrayTokensBC() Related method to retrieve all tokens used for arrays
     *                                                      (cross-version).
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::shortArrayTokensBC()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $shortArrayTokensBC = [
        \T_OPEN_SHORT_ARRAY     => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY    => \T_CLOSE_SHORT_ARRAY,
        \T_OPEN_SQUARE_BRACKET  => \T_OPEN_SQUARE_BRACKET,
        \T_CLOSE_SQUARE_BRACKET => \T_CLOSE_SQUARE_BRACKET,
    ];

    /**
     * DEPRECATED: Tokens which are used for short lists.
     *
     * @see \PHPCSUtils\Tokens\Collections::listTokens() Related method to retrieve all tokens used for lists.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::shortListTokens()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $shortListTokens = [
        \T_OPEN_SHORT_ARRAY  => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY => \T_CLOSE_SHORT_ARRAY,
    ];

    /**
     * DEPRECATED: Tokens which are used for short lists (PHPCS cross-version compatible).
     *
     * Includes `T_OPEN_SQUARE_BRACKET` and `T_CLOSE_SQUARE_BRACKET` to allow for handling
     * intermittent tokenizer issues related to the retokenization to `T_OPEN_SHORT_ARRAY`.
     * Should only be used selectively.
     *
     * @see \PHPCSUtils\Tokens\Collections::listTokensBC() Related method to retrieve all tokens used for lists
     *                                                     (cross-version).
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::shortListTokensBC()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $shortListTokensBC = [
        \T_OPEN_SHORT_ARRAY     => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY    => \T_CLOSE_SHORT_ARRAY,
        \T_OPEN_SQUARE_BRACKET  => \T_OPEN_SQUARE_BRACKET,
        \T_CLOSE_SQUARE_BRACKET => \T_CLOSE_SQUARE_BRACKET,
    ];

    /**
     * DEPRECATED: Tokens which can start a - potentially multi-line - text string.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::textStringStartTokens()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $textStingStartTokens = [
        \T_START_HEREDOC            => \T_START_HEREDOC,
        \T_START_NOWDOC             => \T_START_NOWDOC,
        \T_CONSTANT_ENCAPSED_STRING => \T_CONSTANT_ENCAPSED_STRING,
        \T_DOUBLE_QUOTED_STRING     => \T_DOUBLE_QUOTED_STRING,
    ];

    /**
     * Handle calls to (undeclared) methods for token arrays which don't need special handling.
     *
     * @since 1.0.0-alpha4
     *
     * @param string $name The name of the method which has been called.
     * @param array  $args Any arguments passed to the method.
     *                     Unused as none of the methods take arguments.
     *
     * @return array <int|string> => <int|string> Token array
     *
     * @throws \PHPCSUtils\Exceptions\InvalidTokenArray When an invalid token array is requested.
     */
    public static function __callStatic($name, $args)
    {
        if (isset(self::${$name})) {
            return self::${$name};
        }

        // Unknown token array requested.
        throw InvalidTokenArray::create($name);
    }

    /**
     * Throw a deprecation notice with a standardized deprecation message.
     *
     * @since 1.0.0-alpha4
     *
     * @param string $method      The name of the method which is deprecated.
     * @param string $version     The version since which the method is deprecated.
     * @param string $replacement What to use instead.
     *
     * @return void
     */
    private static function triggerDeprecation($method, $version, $replacement)
    {
        \trigger_error(
            \sprintf(
                'The %1$s::%2$s() method is deprecated since PHPCSUtils %3$s.'
                . ' Use %4$s instead.',
                __CLASS__,
                $method,
                $version,
                $replacement
            ),
            \E_USER_DEPRECATED
        );
    }

    /**
     * Tokens for control structures which can use the alternative control structure syntax.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$alternativeControlStructureSyntaxTokens}
     *                     property.
     *
     * @return array <int> => <int>
     */
    public static function alternativeControlStructureSyntaxes()
    {
        return self::$alternativeControlStructureSyntaxTokens;
    }

    /**
     * Tokens representing alternative control structure syntax closer keywords.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$alternativeControlStructureSyntaxCloserTokens}
     *                     property.
     *
     * @return array <int> => <int>
     */
    public static function alternativeControlStructureSyntaxClosers()
    {
        return self::$alternativeControlStructureSyntaxCloserTokens;
    }

    /**
     * DEPRECATED: Tokens which can represent the arrow function keyword.
     *
     * @since 1.0.0-alpha2
     *
     * @deprecated 1.0.0-alpha4 Use the `T_FN` token constant instead.
     *
     * @return array <int|string> => <int|string>
     */
    public static function arrowFunctionTokensBC()
    {
        self::triggerDeprecation(__FUNCTION__, '1.0.0-alpha4', 'the `T_FN` token');

        return [\T_FN => \T_FN];
    }

    /**
     * Tokens which can represent function calls and function-call-like language constructs.
     *
     * @see \PHPCSUtils\Tokens\Collections::parameterPassingTokens() Related method.
     *
     * @since 1.0.0-alpha4
     *
     * @return array <int|string> => <int|string>
     */
    public static function functionCallTokens()
    {
        // Function calls and class instantiation.
        $tokens              = self::$nameTokens;
        $tokens[\T_VARIABLE] = \T_VARIABLE;

        // Class instantiation only.
        $tokens[\T_ANON_CLASS] = \T_ANON_CLASS;
        $tokens               += self::$OOHierarchyKeywords;

        return $tokens;
    }

    /**
     * DEPRECATED: Tokens which represent a keyword which starts a function declaration.
     *
     * @since 1.0.0-alpha3
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::functionDeclarationTokens()} method instead.
     *
     * @return array <int|string> => <int|string>
     */
    public static function functionDeclarationTokensBC()
    {
        self::triggerDeprecation(
            __FUNCTION__,
            '1.0.0-alpha4',
            \sprintf('the %s::functionDeclarationTokens() method', __CLASS__)
        );

        return self::$functionDeclarationTokens;
    }

    /**
     * Tokens types which can be encountered in a fully, partially or unqualified name.
     *
     * Example:
     * ```php
     * echo namespace\Sub\ClassName::method();
     * ```
     *
     * @since 1.0.0-alpha4
     *
     * @return array <int|string> => <int|string>
     */
    public static function namespacedNameTokens()
    {
        $tokens = [
            \T_NS_SEPARATOR => \T_NS_SEPARATOR,
            \T_NAMESPACE    => \T_NAMESPACE,
        ];

        $tokens += self::$nameTokens;

        return $tokens;
    }

    /**
     * OO structures which can use the "extends" keyword.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$OOCanExtend} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function ooCanExtend()
    {
        return self::$OOCanExtend;
    }

    /**
     * OO structures which can use the "implements" keyword.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$OOCanImplement} property.
     * @since 1.0.0-alpha4 Added the PHP 8.1 T_ENUM token.
     *
     * @return array <int|string> => <int|string>
     */
    public static function ooCanImplement()
    {
        return self::$OOCanImplement;
    }

    /**
     * OO scopes in which constants can be declared.
     *
     * Note: traits can only declare constants since PHP 8.2.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$OOConstantScopes} property.
     * @since 1.0.0-alpha4 Added the PHP 8.1 T_ENUM token.
     * @since 1.0.0-alpha4 Added the T_TRAIT token for PHP 8.2 constants in traits.
     *
     * @return array <int|string> => <int|string>
     */
    public static function ooConstantScopes()
    {
        return self::$OOConstantScopes;
    }

    /**
     * Tokens types used for "forwarding" calls within OO structures.
     *
     * @link https://www.php.net/language.oop5.paamayim-nekudotayim PHP Manual on OO forwarding calls
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$OOHierarchyKeywords} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function ooHierarchyKeywords()
    {
        return self::$OOHierarchyKeywords;
    }

    /**
     * OO scopes in which properties can be declared.
     *
     * Note: interfaces can not declare properties.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$OOPropertyScopes} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function ooPropertyScopes()
    {
        return self::$OOPropertyScopes;
    }

    /**
     * Tokens which can be passed to the methods in the PassedParameter class.
     *
     * @see \PHPCSUtils\Utils\PassedParameters
     *
     * @since 1.0.0-alpha4
     *
     * @return array <int|string> => <int|string>
     */
    public static function parameterPassingTokens()
    {
        // Function call and class instantiation tokens.
        $tokens = self::functionCallTokens();

        // Function-look-a-like language constructs which can take multiple "parameters".
        $tokens[\T_ISSET] = \T_ISSET;
        $tokens[\T_UNSET] = \T_UNSET;

        // Array tokens.
        $tokens += self::$arrayOpenTokensBC;

        return $tokens;
    }

    /**
     * Token types which can be encountered in a parameter type declaration.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$parameterTypeTokens} property.
     * @since 1.0.0-alpha4 Added the T_TYPE_UNION, T_FALSE, T_NULL tokens for PHP 8.0 union type support.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     * @since 1.0.0-alpha4 Added the T_TYPE_INTERSECTION token for PHP 8.1 intersection type support.
     * @since 1.0.0-alpha4 Added the T_TRUE token for PHP 8.2 true type support.
     *
     * @return array <int|string> => <int|string>
     */
    public static function parameterTypeTokens()
    {
        $tokens  = self::$parameterTypeTokens;
        $tokens += self::namespacedNameTokens();

        return $tokens;
    }

    /**
     * DEPRECATED: Token types which can be encountered in a parameter type declaration (cross-version).
     *
     * @see \PHPCSUtils\Tokens\Collections::parameterTypeTokens() Related method (PHPCS 3.3.0+).
     *
     * @since 1.0.0-alpha3
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::parameterTypeTokens()} method instead.
     *
     * @return array <int|string> => <int|string>
     */
    public static function parameterTypeTokensBC()
    {
        self::triggerDeprecation(
            __FUNCTION__,
            '1.0.0-alpha4',
            \sprintf('the %s::parameterTypeTokens() method', __CLASS__)
        );

        return self::parameterTypeTokens();
    }

    /**
     * Token types which can be encountered in a property type declaration.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$propertyTypeTokens} property.
     * @since 1.0.0-alpha4 Added the T_TYPE_UNION, T_FALSE, T_NULL tokens for PHP 8.0 union type support.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     * @since 1.0.0-alpha4 Added the T_TYPE_INTERSECTION token for PHP 8.1 intersection type support.
     * @since 1.0.0-alpha4 Added the T_TRUE token for PHP 8.2 true type support.
     *
     * @return array <int|string> => <int|string>
     */
    public static function propertyTypeTokens()
    {
        $tokens  = self::$propertyTypeTokens;
        $tokens += self::namespacedNameTokens();

        return $tokens;
    }

    /**
     * DEPRECATED: Token types which can be encountered in a property type declaration (cross-version).
     *
     * @see \PHPCSUtils\Tokens\Collections::propertyTypeTokens() Related method (PHPCS 3.3.0+).
     *
     * @since 1.0.0-alpha3
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::propertyTypeTokens()} method instead.
     *
     * @return array <int|string> => <int|string>
     */
    public static function propertyTypeTokensBC()
    {
        self::triggerDeprecation(
            __FUNCTION__,
            '1.0.0-alpha4',
            \sprintf('the %s::propertyTypeTokens() method', __CLASS__)
        );

        return self::propertyTypeTokens();
    }

    /**
     * Token types which can be encountered in a return type declaration.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$returnTypeTokens} property.
     * @since 1.0.0-alpha4 Added the T_TYPE_UNION, T_FALSE, T_NULL tokens for PHP 8.0 union type support.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     * @since 1.0.0-alpha4 Added the T_TYPE_INTERSECTION token for PHP 8.1 intersection type support.
     * @since 1.0.0-alpha4 Added the T_TRUE token for PHP 8.2 true type support.
     *
     * @return array <int|string> => <int|string>
     */
    public static function returnTypeTokens()
    {
        $tokens  = self::$returnTypeTokens;
        $tokens += self::$OOHierarchyKeywords;
        $tokens += self::namespacedNameTokens();

        return $tokens;
    }

    /**
     * DEPRECATED: Token types which can be encountered in a return type declaration (cross-version).
     *
     * @see \PHPCSUtils\Tokens\Collections::returnTypeTokens() Related method (PHPCS 3.3.0+).
     *
     * @since 1.0.0-alpha3
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::returnTypeTokens()} method instead.
     *
     * @return array <int|string> => <int|string>
     */
    public static function returnTypeTokensBC()
    {
        self::triggerDeprecation(
            __FUNCTION__,
            '1.0.0-alpha4',
            \sprintf('the %s::returnTypeTokens() method', __CLASS__)
        );

        return self::returnTypeTokens();
    }

    /**
     * Tokens which can start a - potentially multi-line - text string.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$textStingStartTokens} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function textStringStartTokens()
    {
        return self::$textStingStartTokens;
    }
}
