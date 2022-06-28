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

use PHPCSUtils\Tokens\TokenHelper;

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
     * DEPRECATED: Control structures which can use the alternative control structure syntax.
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
     * DEPRECATED: Alternative control structure syntax closer keyword tokens.
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
     * DEPRECATED: Tokens which are used to create arrays.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::arrayTokens()} or
     *                          {@see Collections::arrayTokensBC()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $arrayTokens = [
        \T_ARRAY             => \T_ARRAY,
        \T_OPEN_SHORT_ARRAY  => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY => \T_CLOSE_SHORT_ARRAY,
    ];

    /**
     * DEPRECATED: Tokens which are used to create arrays.
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
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::classModifierKeywords()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $classModifierKeywords = [
        \T_FINAL    => \T_FINAL,
        \T_ABSTRACT => \T_ABSTRACT,
    ];

    /**
     * DEPRECATED: List of tokens which represent "closed" scopes.
     *
     * @since 1.0.0-alpha1
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
        \T_FUNCTION   => \T_FUNCTION,
        \T_CLOSURE    => \T_CLOSURE,
    ];

    /**
     * DEPRECATED: Control structure tokens.
     *
     * @since 1.0.0-alpha2
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
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::listTokens()} or
     *                          {@see Collections::listTokensBC()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $listTokens = [
        \T_LIST              => \T_LIST,
        \T_OPEN_SHORT_ARRAY  => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY => \T_CLOSE_SHORT_ARRAY,
    ];

    /**
     * DEPRECATED: Tokens which are used to create lists.
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
     * @deprecated 1.0.0-alpha4 Use the {@see \PHPCSUtils\BackCompat\BCTokens::magicConstants()} method instead.
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
     * DEPRECATED: Object operators.
     *
     * @since 1.0.0-alpha3
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::objectOperators()} method instead.
     *
     * @var array <int> => <int>
     */
    public static $objectOperators = [
        \T_OBJECT_OPERATOR => \T_OBJECT_OPERATOR,
        \T_DOUBLE_COLON    => \T_DOUBLE_COLON,
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
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::ooCanImplement()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $OOCanImplement = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
    ];

    /**
     * DEPRECATED: OO scopes in which constants can be declared.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::ooConstantScopes()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $OOConstantScopes = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_INTERFACE  => \T_INTERFACE,
    ];

    /**
     * DEPRECATED: Tokens types used for "forwarding" calls within OO structures.
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
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::parameterTypeTokens()} or
     *                          {@see Collections::parameterTypeTokensBC()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $parameterTypeTokens = [
        \T_CALLABLE     => \T_CALLABLE,
        \T_SELF         => \T_SELF,
        \T_PARENT       => \T_PARENT,
        \T_STRING       => \T_STRING,
        \T_NS_SEPARATOR => \T_NS_SEPARATOR,
    ];

    /**
     * DEPRECATED: Modifier keywords which can be used for a property declaration.
     *
     * @since 1.0.0-alpha1
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
    ];

    /**
     * DEPRECATED: Token types which can be encountered in a property type declaration.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::propertyTypeTokens()} or
     *                          {@see Collections::propertyTypeTokensBC()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $propertyTypeTokens = [
        \T_CALLABLE     => \T_CALLABLE,
        \T_SELF         => \T_SELF,
        \T_PARENT       => \T_PARENT,
        \T_STRING       => \T_STRING,
        \T_NS_SEPARATOR => \T_NS_SEPARATOR,
    ];

    /**
     * DEPRECATED: Token types which can be encountered in a return type declaration.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::returnTypeTokens()} or
     *                          {@see Collections::returnTypeTokensBC()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $returnTypeTokens = [
        \T_STRING       => \T_STRING,
        \T_CALLABLE     => \T_CALLABLE,
        \T_SELF         => \T_SELF,
        \T_PARENT       => \T_PARENT,
        \T_STATIC       => \T_STATIC,
        \T_NS_SEPARATOR => \T_NS_SEPARATOR,
    ];

    /**
     * DEPRECATED: Tokens which are used for short arrays.
     *
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::shortArrayTokens()} or
     *                          {@see Collections::shortArrayTokensBC()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $shortArrayTokens = [
        \T_OPEN_SHORT_ARRAY  => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY => \T_CLOSE_SHORT_ARRAY,
    ];

    /**
     * DEPRECATED: Tokens which are used for short arrays.
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
     * @since 1.0.0-alpha1
     *
     * @deprecated 1.0.0-alpha4 Use the {@see Collections::shortListTokens()} or
     *                          {@see Collections::shortListTokensBC()} method instead.
     *
     * @var array <int|string> => <int|string>
     */
    public static $shortListTokens = [
        \T_OPEN_SHORT_ARRAY  => \T_OPEN_SHORT_ARRAY,
        \T_CLOSE_SHORT_ARRAY => \T_CLOSE_SHORT_ARRAY,
    ];

    /**
     * DEPRECATED: Tokens which are used for short lists.
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
     * Tokens which can open an array.
     *
     * PHPCS cross-version compatible.
     *
     * @since 1.0.0-alpha4
     *
     * @return array <int|string> => <int|string>
     */
    public static function arrayOpenTokensBC()
    {
        return [
            \T_ARRAY               => \T_ARRAY,
            \T_OPEN_SHORT_ARRAY    => \T_OPEN_SHORT_ARRAY,
            \T_OPEN_SQUARE_BRACKET => \T_OPEN_SQUARE_BRACKET,
        ];
    }

    /**
     * Tokens which are used to create arrays.
     *
     * @see \PHPCSUtils\Tokens\Collections::arrayTokensBC()    Related method to retrieve tokens used
     *                                                         for arrays (PHPCS cross-version).
     * @see \PHPCSUtils\Tokens\Collections::shortArrayTokens() Related method to retrieve only tokens used
     *                                                         for short arrays.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$arrayTokens} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function arrayTokens()
    {
        return self::$arrayTokens;
    }

    /**
     * Tokens which are used to create arrays.
     *
     * List which is backward-compatible with PHPCS < 3.3.0.
     * Should only be used selectively.
     *
     * @see \PHPCSUtils\Tokens\Collections::shortArrayTokensBC() Related method to retrieve only tokens used
     *                                                           for short arrays (PHPCS cross-version).
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$arrayTokensBC} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function arrayTokensBC()
    {
        return self::$arrayTokensBC;
    }

    /**
     * Tokens which can represent the arrow function keyword.
     *
     * Note: this is a method, not a property as the `T_FN` token for arrow functions may not exist.
     *
     * @see \PHPCSUtils\Utils\FunctionDeclarations::isArrowFunction()           Determine whether an arbitrary token
     *                                                                          is in actual fact an arrow function
     *                                                                          keyword.
     * @see \PHPCSUtils\Utils\FunctionDeclarations::getArrowFunctionOpenClose() Get an arrow function's parentheses
     *                                                                          and scope openers and closers.
     *
     * @since 1.0.0-alpha2
     *
     * @return array <int|string> => <int|string>
     */
    public static function arrowFunctionTokensBC()
    {
        $tokens = [
            \T_STRING => \T_STRING,
        ];

        if (TokenHelper::tokenExists('T_FN') === true) {
            // PHP 7.4 or PHPCS 3.5.3+.
            $tokens[\T_FN] = \T_FN;
        }

        return $tokens;
    }

    /**
     * Modifier keywords which can be used for a class declaration.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$classModifierKeywords} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function classModifierKeywords()
    {
        return self::$classModifierKeywords;
    }

    /**
     * List of tokens which represent "closed" scopes.
     *
     * I.e. anything declared within that scope - except for other closed scopes - is
     * outside of the global namespace.
     *
     * This list doesn't contain the `T_NAMESPACE` token on purpose as variables declared
     * within a namespace scope are still global and not limited to that namespace.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$closedScopes} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function closedScopes()
    {
        return self::$closedScopes;
    }

    /**
     * Control structure tokens.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$controlStructureTokens} property.
     *
     * @return array <int> => <int>
     */
    public static function controlStructureTokens()
    {
        return self::$controlStructureTokens;
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
        $tokens              = self::nameTokens();
        $tokens[\T_VARIABLE] = \T_VARIABLE;

        // Class instantiation only.
        $tokens[\T_ANON_CLASS] = \T_ANON_CLASS;
        $tokens               += self::ooHierarchyKeywords();

        return $tokens;
    }

    /**
     * Tokens which can represent a keyword which starts a function declaration.
     *
     * Note: this is a method, not a property as the `T_FN` token for arrow functions may not exist.
     *
     * Sister-method to the {@see Collections::functionDeclarationTokensBC()} method.
     * This method supports PHPCS 3.5.3 and up.
     * The {@see Collections::functionDeclarationTokensBC()} method supports PHPCS 2.6.0 and up.
     *
     * @see \PHPCSUtils\Tokens\Collections::functionDeclarationTokensBC() Related method (PHPCS 2.6.0+).
     *
     * @since 1.0.0-alpha3
     *
     * @return array <int|string> => <int|string>
     */
    public static function functionDeclarationTokens()
    {
        $tokens = [
            \T_FUNCTION => \T_FUNCTION,
            \T_CLOSURE  => \T_CLOSURE,
        ];

        if (TokenHelper::tokenExists('T_FN') === true) {
            // PHP 7.4 or PHPCS 3.5.3+.
            $tokens[\T_FN] = \T_FN;
        }

        return $tokens;
    }

    /**
     * Tokens which can represent a keyword which starts a function declaration.
     *
     * Note: this is a method, not a property as the `T_FN` token for arrow functions may not exist.
     *
     * Sister-method to the {@see Collections::functionDeclarationTokens()} method.
     * The {@see Collections::functionDeclarationTokens()} method supports PHPCS 3.5.3 and up.
     * This method supports PHPCS 2.6.0 and up.
     *
     * Notable difference:
     * - This method accounts for when the `T_FN` token doesn't exist.
     *
     * Note: if this method is used, the {@see \PHPCSUtils\Utils\FunctionDeclarations::isArrowFunction()}
     * method needs to be used on arrow function tokens to verify whether it really is an arrow function
     * declaration or not.
     *
     * It is recommended to use the {@see Collections::functionDeclarationTokens()} method instead of
     * this method if a standard does not need to support PHPCS < 3.5.3.
     *
     * @see \PHPCSUtils\Tokens\Collections::functionDeclarationTokens() Related method (PHPCS 3.5.3+).
     * @see \PHPCSUtils\Utils\FunctionDeclarations::isArrowFunction()   Arrow function verification.
     *
     * @since 1.0.0-alpha3
     *
     * @return array <int|string> => <int|string>
     */
    public static function functionDeclarationTokensBC()
    {
        $tokens = [
            \T_FUNCTION => \T_FUNCTION,
            \T_CLOSURE  => \T_CLOSURE,
        ];

        $tokens += self::arrowFunctionTokensBC();

        return $tokens;
    }

    /**
     * Increment/decrement operator tokens.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$incrementDecrementOperators} property.
     *
     * @return array <int> => <int>
     */
    public static function incrementDecrementOperators()
    {
        return self::$incrementDecrementOperators;
    }

    /**
     * Tokens which are used to create lists.
     *
     * @see \PHPCSUtils\Tokens\Collections::listTokensBC()    Related method to retrieve tokens used
     *                                                        for lists (PHPCS cross-version).
     * @see \PHPCSUtils\Tokens\Collections::shortListTokens() Related method to retrieve only tokens used
     *                                                        for short lists.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$listTokens} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function listTokens()
    {
        return self::$listTokens;
    }

    /**
     * Tokens which are used to create lists.
     *
     * List which is backward-compatible with PHPCS < 3.3.0.
     * Should only be used selectively.
     *
     * @see \PHPCSUtils\Tokens\Collections::shortListTokensBC() Related method to retrieve only tokens used
     *                                                          for short lists (cross-version).
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$listTokensBC} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function listTokensBC()
    {
        return self::$listTokensBC;
    }

    /**
     * List of tokens which can end a namespace declaration statement.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$namespaceDeclarationClosers} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function namespaceDeclarationClosers()
    {
        return self::$namespaceDeclarationClosers;
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

        $tokens += self::nameTokens();

        return $tokens;
    }

    /**
     * The tokens used for "names", be it namespace, OO, function or constant names.
     *
     * Includes the tokens introduced in PHP 8.0 for "Namespaced names as single token" when available.
     *
     * Note: this is a method, not a property as the PHP 8.0 identifier name tokens may not exist.
     *
     * @link https://wiki.php.net/rfc/namespaced_names_as_token
     *
     * @since 1.0.0-alpha4
     *
     * @return array <int|string> => <int|string>
     */
    public static function nameTokens()
    {
        $tokens = [
            \T_STRING => \T_STRING,
        ];

        /*
         * PHP >= 8.0 in combination with PHPCS < 3.5.7 and all PHP versions in combination
         * with PHPCS >= 3.5.7, though when using PHPCS 3.5.7 < 4.0.0, these tokens are
         * not yet in use, i.e. the PHP 8.0 change is "undone" for PHPCS 3.x.
         */
        if (TokenHelper::tokenExists('T_NAME_QUALIFIED') === true) {
            $tokens[\T_NAME_QUALIFIED] = \T_NAME_QUALIFIED;
        }

        if (TokenHelper::tokenExists('T_NAME_FULLY_QUALIFIED') === true) {
            $tokens[\T_NAME_FULLY_QUALIFIED] = \T_NAME_FULLY_QUALIFIED;
        }

        if (TokenHelper::tokenExists('T_NAME_RELATIVE') === true) {
            $tokens[\T_NAME_RELATIVE] = \T_NAME_RELATIVE;
        }

        return $tokens;
    }

    /**
     * Tokens which can represent the nullsafe object operator.
     *
     * This method will return the appropriate tokens based on the PHP/PHPCS version used.
     *
     * Note: this is a method, not a property as the `T_NULLSAFE_OBJECT_OPERATOR` token may not exist.
     *
     * Note: if this method is used, the {@see \PHPCSUtils\Utils\Operators::isNullsafeObjectOperator()}
     * method needs to be used on potential nullsafe object operator tokens to verify whether it really
     * is a nullsafe object operator or not.
     *
     * @see \PHPCSUtils\Utils\Operators::isNullsafeObjectOperator() Nullsafe object operator detection for
     *                                                              PHPCS < 3.5.7.
     *
     * @since 1.0.0-alpha4
     *
     * @return array <int|string> => <int|string>
     */
    public static function nullsafeObjectOperatorBC()
    {
        if (TokenHelper::tokenExists('T_NULLSAFE_OBJECT_OPERATOR') === true) {
            // PHP >= 8.0 / PHPCS >= 3.5.7.
            return [
                \T_NULLSAFE_OBJECT_OPERATOR => \T_NULLSAFE_OBJECT_OPERATOR,
            ];
        }

        return [
            \T_INLINE_THEN     => \T_INLINE_THEN,
            \T_OBJECT_OPERATOR => \T_OBJECT_OPERATOR,
        ];
    }

    /**
     * Object operators.
     *
     * Note: this is a method, not a property as the `T_NULLSAFE_OBJECT_OPERATOR` token may not exist.
     *
     * Sister-method to the {@see Collections::objectOperatorsBC()} method.
     * This method supports PHPCS 3.5.7 and up.
     * The {@see Collections::objectOperatorsBC()} method supports PHPCS 2.6.0 and up.
     *
     * This method can also safely be used if the token collection is only used when looking back
     * via `$phpcsFile->findPrevious()` as in that case, a non-backfilled nullsafe object operator
     * will still match the "normal" object operator.
     *
     * @see \PHPCSUtils\Tokens\Collections::objectOperatorsBC() Related method (PHPCS 2.6.0+).
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$objectOperators} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function objectOperators()
    {
        $tokens = [
            \T_OBJECT_OPERATOR => \T_OBJECT_OPERATOR,
            \T_DOUBLE_COLON    => \T_DOUBLE_COLON,
        ];

        if (TokenHelper::tokenExists('T_NULLSAFE_OBJECT_OPERATOR') === true) {
            // PHP >= 8.0 or PHPCS >= 3.5.7.
            $tokens[\T_NULLSAFE_OBJECT_OPERATOR] = \T_NULLSAFE_OBJECT_OPERATOR;
        }

        return $tokens;
    }

    /**
     * Object operators.
     *
     * Note: this is a method, not a property as the `T_NULLSAFE_OBJECT_OPERATOR` token may not exist.
     *
     * Sister-method to the {@see Collections::objectOperators()} method.
     * The {@see Collections::objectOperators()} method supports PHPCS 3.5.7 and up.
     * This method supports PHPCS 2.6.0 and up.
     *
     * Notable difference:
     * - This method accounts for tokens which may be encountered when the `T_NULLSAFE_OBJECT_OPERATOR` token
     *   doesn't exist.
     *
     * It is recommended to use the {@see Collections::objectOperators()} method instead of
     * this method if a standard does not need to support PHPCS < 3.5.7.
     *
     * The {@see Collections::objectOperators()} method can also safely be used if the token collection
     * is only used when looking back via `$phpcsFile->findPrevious()` as in that case, a non-backfilled
     * nullsafe object operator will still match the "normal" object operator.
     *
     * Note: if this method is used, the {@see \PHPCSUtils\Utils\Operators::isNullsafeObjectOperator()}
     * method needs to be used on potential nullsafe object operator tokens to verify whether it really
     * is a nullsafe object operator or not.
     *
     * @see \PHPCSUtils\Tokens\Collections::objectOperators()          Related method (PHPCS 3.5.7+).
     * @see \PHPCSUtils\Tokens\Collections::nullsafeObjectOperatorBC() Tokens which can represent a
     *                                                                 nullsafe object operator.
     * @see \PHPCSUtils\Utils\Operators::isNullsafeObjectOperator()    Nullsafe object operator detection for
     *                                                                 PHPCS < 3.5.7.
     *
     * @since 1.0.0-alpha4
     *
     * @return array <int|string> => <int|string>
     */
    public static function objectOperatorsBC()
    {
        $tokens = [
            \T_OBJECT_OPERATOR => \T_OBJECT_OPERATOR,
            \T_DOUBLE_COLON    => \T_DOUBLE_COLON,
        ];

        $tokens += self::nullsafeObjectOperatorBC();

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
     * Note: traits can not declare constants.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$OOConstantScopes} property.
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
        $tokens += self::arrayOpenTokensBC();

        return $tokens;
    }

    /**
     * Token types which can be encountered in a parameter type declaration.
     *
     * Note: this is a method, not a property as the `T_TYPE_UNION` token for PHP 8.0 union types may not exist.
     *
     * Sister-method to the {@see Collections::parameterTypeTokensBC()} method.
     * This method supports PHPCS 3.3.0 and up.
     * The {@see Collections::parameterTypeTokensBC()} method supports PHPCS 2.6.0 and up.
     *
     * Notable difference:
     * - The {@see Collections::parameterTypeTokensBC()} method will include the `T_ARRAY_HINT` token
     *   when used with PHPCS 2.x and 3.x.
     *   This token constant will no longer exist in PHPCS 4.x.
     *
     * It is recommended to use this method instead of the {@see Collections::parameterTypeTokensBC()}
     * method if a standard does not need to support PHPCS < 3.3.0.
     *
     * @see \PHPCSUtils\Tokens\Collections::parameterTypeTokensBC() Related method (cross-version).
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$parameterTypeTokens} property.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 union types.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     * @since 1.0.0-alpha4 Added the T_TYPE_UNION token for union type support in PHPCS > 3.6.0.
     *
     * @return array <int|string> => <int|string>
     */
    public static function parameterTypeTokens()
    {
        $tokens = [
            \T_CALLABLE   => \T_CALLABLE,
            \T_SELF       => \T_SELF,
            \T_PARENT     => \T_PARENT,
            \T_FALSE      => \T_FALSE,      // Union types only.
            \T_NULL       => \T_NULL,       // Union types only.
            \T_BITWISE_OR => \T_BITWISE_OR, // Union types for PHPCS < 3.6.0.
        ];

        $tokens += self::namespacedNameTokens();

        // PHPCS > 3.6.0: a new token was introduced for the union type separator.
        if (\defined('T_TYPE_UNION') === true) {
            $tokens[\T_TYPE_UNION] = \T_TYPE_UNION;
        }

        return $tokens;
    }

    /**
     * Token types which can be encountered in a parameter type declaration (cross-version).
     *
     * Sister-method to the {@see Collections::parameterTypeTokens()} method.
     * The {@see Collections::parameterTypeTokens()} method supports PHPCS 3.3.0 and up.
     * This method supports PHPCS 2.6.0 and up.
     *
     * Notable difference:
     * - This method will include the `T_ARRAY_HINT` token when used with PHPCS 2.x and 3.x.
     *   This token constant will no longer exist in PHPCS 4.x.
     *
     * It is recommended to use {@see Collections::parameterTypeTokens()} method instead of
     * this method if a standard does not need to support PHPCS < 3.3.0.
     *
     * @see \PHPCSUtils\Tokens\Collections::parameterTypeTokens() Related method (PHPCS 3.3.0+).
     *
     * @since 1.0.0-alpha3
     * @since 1.0.0-alpha4 Added support for PHP 8.0 union types.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     *
     * @return array <int|string> => <int|string>
     */
    public static function parameterTypeTokensBC()
    {
        $tokens = self::parameterTypeTokens();

        // PHPCS < 4.0; Needed for support of PHPCS < 3.3.0. As of PHPCS 3.3.0+ the constant is no longer used.
        if (\defined('T_ARRAY_HINT') === true) {
            $tokens[\T_ARRAY_HINT] = \T_ARRAY_HINT;
        }

        return $tokens;
    }

    /**
     * Tokens which open PHP.
     *
     * @since 1.0.0-alpha4
     *
     * @return array <int|string> => <int|string>
     */
    public static function phpOpenTags()
    {
        return [
            \T_OPEN_TAG           => \T_OPEN_TAG,
            \T_OPEN_TAG_WITH_ECHO => \T_OPEN_TAG_WITH_ECHO,
        ];
    }

    /**
     * Modifier keywords which can be used for a property declaration.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$propertyModifierKeywords} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function propertyModifierKeywords()
    {
        return self::$propertyModifierKeywords;
    }

    /**
     * Token types which can be encountered in a property type declaration.
     *
     * Note: this is a method, not a property as the `T_TYPE_UNION` token for PHP 8.0 union types may not exist.
     *
     * Sister-method to the {@see Collections::propertyTypeTokensBC()} method.
     * This method supports PHPCS 3.3.0 and up.
     * The {@see Collections::propertyTypeTokensBC()} method supports PHPCS 2.6.0 and up.
     *
     * Notable difference:
     * - The {@see Collections::propertyTypeTokensBC()} method will include the `T_ARRAY_HINT` token
     *   when used with PHPCS 2.x and 3.x.
     *   This token constant will no longer exist in PHPCS 4.x.
     *
     * It is recommended to use this method instead of the {@see Collections::propertyTypeTokensBC()}
     * method if a standard does not need to support PHPCS < 3.3.0.
     *
     * @see \PHPCSUtils\Tokens\Collections::propertyTypeTokensBC() Related method (cross-version).
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$propertyTypeTokens} property.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 union types.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     * @since 1.0.0-alpha4 Added the T_TYPE_UNION token for union type support in PHPCS > 3.6.0.
     *
     * @return array <int|string> => <int|string>
     */
    public static function propertyTypeTokens()
    {
        $tokens = [
            \T_CALLABLE   => \T_CALLABLE,
            \T_SELF       => \T_SELF,
            \T_PARENT     => \T_PARENT,
            \T_FALSE      => \T_FALSE,      // Union types only.
            \T_NULL       => \T_NULL,       // Union types only.
            \T_BITWISE_OR => \T_BITWISE_OR, // Union types for PHPCS < 3.6.0.
        ];

        $tokens += self::namespacedNameTokens();

        // PHPCS > 3.6.0: a new token was introduced for the union type separator.
        if (\defined('T_TYPE_UNION') === true) {
            $tokens[\T_TYPE_UNION] = \T_TYPE_UNION;
        }

        return $tokens;
    }

    /**
     * Token types which can be encountered in a property type declaration (cross-version).
     *
     * Sister-method to the {@see Collections::propertyTypeTokens()} method.
     * The {@see Collections::propertyTypeTokens()} method supports PHPCS 3.3.0 and up.
     * This method supports PHPCS 2.6.0 and up.
     *
     * Notable difference:
     * - This method will include the `T_ARRAY_HINT` token when used with PHPCS 2.x and 3.x.
     *   This token constant will no longer exist in PHPCS 4.x.
     *
     * It is recommended to use the {@see Collections::propertyTypeTokens()} method instead of
     * this method if a standard does not need to support PHPCS < 3.3.0.
     *
     * @see \PHPCSUtils\Tokens\Collections::propertyTypeTokens() Related method (PHPCS 3.3.0+).
     *
     * @since 1.0.0-alpha3
     * @since 1.0.0-alpha4 Added support for PHP 8.0 union types.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     *
     * @return array <int|string> => <int|string>
     */
    public static function propertyTypeTokensBC()
    {
        $tokens = self::propertyTypeTokens();

        // PHPCS < 4.0; Needed for support of PHPCS < 3.3.0. For PHPCS 3.3.0+ the constant is no longer used.
        if (\defined('T_ARRAY_HINT') === true) {
            $tokens[\T_ARRAY_HINT] = \T_ARRAY_HINT;
        }

        return $tokens;
    }

    /**
     * Token types which can be encountered in a return type declaration.
     *
     * Note: this is a method, not a property as the `T_TYPE_UNION` token for PHP 8.0 union types may not exist.
     *
     * Sister-method to the {@see Collections::returnTypeTokensBC()} method.
     * This method supports PHPCS 3.3.0 and up.
     * The {@see Collections::returnTypeTokensBC()} method supports PHPCS 2.6.0 and up.
     *
     * Notable differences:
     * - The {@see Collections::returnTypeTokensBC()} method will include the `T_ARRAY_HINT`
     *   and the `T_RETURN_TYPE` tokens when used with PHPCS 2.x and 3.x.
     *   These token constants will no longer exist in PHPCS 4.x.
     *
     * It is recommended to use this method instead of the {@see Collections::returnTypeTokensBC()}
     * method if a standard does not need to support PHPCS < 3.3.0.
     *
     * @see \PHPCSUtils\Tokens\Collections::returnTypeTokensBC() Related method (cross-version).
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$returnTypeTokens} property.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 union types.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     * @since 1.0.0-alpha4 Added the T_TYPE_UNION token for union type support in PHPCS > 3.6.0.
     *
     * @return array <int|string> => <int|string>
     */
    public static function returnTypeTokens()
    {
        $tokens = [
            \T_CALLABLE   => \T_CALLABLE,
            \T_FALSE      => \T_FALSE,      // Union types only.
            \T_NULL       => \T_NULL,       // Union types only.
            \T_ARRAY      => \T_ARRAY,      // Arrow functions PHPCS < 3.5.4 + union types.
            \T_BITWISE_OR => \T_BITWISE_OR, // Union types for PHPCS < 3.6.0.
        ];

        $tokens += self::ooHierarchyKeywords();
        $tokens += self::namespacedNameTokens();

        // PHPCS > 3.6.0: a new token was introduced for the union type separator.
        if (\defined('T_TYPE_UNION') === true) {
            $tokens[\T_TYPE_UNION] = \T_TYPE_UNION;
        }

        return $tokens;
    }

    /**
     * Token types which can be encountered in a return type declaration (cross-version).
     *
     * Sister-method to the {@see Collections::returnTypeTokens()} method.
     * The {@see Collections::returnTypeTokens()} method supports PHPCS 3.3.0 and up.
     * This method supports PHPCS 2.6.0 and up.
     *
     * Notable differences:
     * - This method will include the `T_ARRAY_HINT` and the `T_RETURN_TYPE` tokens when
     *   used with PHPCS 2.x and 3.x.
     *   These token constants will no longer exist in PHPCS 4.x.
     *
     * It is recommended to use the {@see Collections::returnTypeTokens()} method instead of
     * this method if a standard does not need to support PHPCS < 3.3.0.
     *
     * @see \PHPCSUtils\Tokens\Collections::returnTypeTokens() Related method (PHPCS 3.3.0+).
     *
     * @since 1.0.0-alpha3
     * @since 1.0.0-alpha4 Added support for PHP 8.0 union types.
     * @since 1.0.0-alpha4 Added support for PHP 8.0 identifier name tokens.
     *
     * @return array <int|string> => <int|string>
     */
    public static function returnTypeTokensBC()
    {
        $tokens = self::returnTypeTokens();

        /*
         * PHPCS < 4.0. Needed for support of PHPCS 2.4.0 < 3.3.0.
         * For PHPCS 3.3.0+ the constant is no longer used.
         */
        if (\defined('T_RETURN_TYPE') === true) {
            $tokens[\T_RETURN_TYPE] = \T_RETURN_TYPE;
        }

        /*
         * PHPCS < 4.0. Needed for support of PHPCS < 2.8.0 / PHPCS < 3.5.3 for arrow functions.
         * For PHPCS 3.5.3+ the constant is no longer used.
         */
        if (\defined('T_ARRAY_HINT') === true) {
            $tokens[\T_ARRAY_HINT] = \T_ARRAY_HINT;
        }

        return $tokens;
    }

    /**
     * Tokens which are used for short arrays.
     *
     * @see \PHPCSUtils\Tokens\Collections::arrayTokens() Related method to retrieve all tokens used for arrays.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$shortArrayTokens} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function shortArrayTokens()
    {
        return self::$shortArrayTokens;
    }

    /**
     * Tokens which are used for short arrays.
     *
     * List which is backward-compatible with PHPCS < 3.3.0.
     * Should only be used selectively.
     *
     * @see \PHPCSUtils\Tokens\Collections::arrayTokensBC() Related method to retrieve all tokens used for arrays
     *                                                      (cross-version).
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$shortArrayTokensBC} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function shortArrayTokensBC()
    {
        return self::$shortArrayTokensBC;
    }

    /**
     * Tokens which are used for short lists.
     *
     * @see \PHPCSUtils\Tokens\Collections::listTokens() Related method to retrieve all tokens used for lists.
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$shortListTokens} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function shortListTokens()
    {
        return self::$shortListTokens;
    }

    /**
     * Tokens which are used for short lists.
     *
     * List which is backward-compatible with PHPCS < 3.3.0.
     * Should only be used selectively.
     *
     * @see \PHPCSUtils\Tokens\Collections::listTokensBC() Related method to retrieve all tokens used for lists
     *                                                     (cross-version).
     *
     * @since 1.0.0-alpha4 This method replaces the {@see Collections::$shortListTokensBC} property.
     *
     * @return array <int|string> => <int|string>
     */
    public static function shortListTokensBC()
    {
        return self::$shortListTokensBC;
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
