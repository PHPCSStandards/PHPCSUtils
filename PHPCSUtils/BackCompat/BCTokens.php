<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\BackCompat;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Exceptions\InvalidTokenArray;

/**
 * Token arrays related utility methods.
 *
 * PHPCS provides a number of static token arrays in the {@see \PHP_CodeSniffer\Util\Tokens}
 * class.
 * Some of these token arrays will not be available in older PHPCS versions.
 * Some will not contain the same set of tokens across PHPCS versions.
 *
 * This class is a compatibility layer to allow for retrieving these token arrays
 * with a consistent token content across PHPCS versions.
 * The one caveat is that the token constants do need to be available.
 *
 * Recommended usage:
 * Only use the methods in this class when needed. I.e. when your sniff unit tests indicate
 * a PHPCS cross-version compatibility issue related to inconsistent token arrays.
 *
 * All PHPCS token arrays are supported, though only a limited number of them are different
 * across PHPCS versions.
 *
 * The names of the PHPCS native token arrays translate one-on-one to the methods in this class:
 * - `PHP_CodeSniffer\Util\Tokens::EMPTY_TOKENS` => `PHPCSUtils\BackCompat\BCTokens::emptyTokens()`
 * - `PHP_CodeSniffer\Util\Tokens::OPERATORS`    => `PHPCSUtils\BackCompat\BCTokens::operators()`
 * - ... etc
 *
 * The order of the tokens in the arrays may differ between the PHPCS native token arrays and
 * the token arrays returned by this class.
 *
 * @since 1.0.0
 *
 * @method static array<int|string, int|string> arithmeticTokens()         Tokens that represent arithmetic operators.
 * @method static array<int|string, int|string> assignmentTokens()         Tokens that represent assignments.
 * @method static array<int|string, int|string> blockOpeners()             Tokens that open code blocks.
 * @method static array<int|string, int|string> booleanOperators()         Tokens that perform boolean operations.
 * @method static array<int|string, int|string> bracketTokens()            Tokens that represent brackets and parenthesis.
 * @method static array<int|string, int|string> castTokens()               Tokens that represent type casting.
 * @method static array<int|string, int|string> commentTokens()            Tokens that are comments.
 * @method static array<int|string, int|string> comparisonTokens()         Tokens that represent comparison operator.
 * @method static array<int|string, int|string> contextSensitiveKeywords() Tokens representing context sensitive keywords
 *                                                                         in PHP.
 * @method static array<int|string, int|string> emptyTokens()              Tokens that don't represent code.
 * @method static array<int|string, int|string> equalityTokens()           Tokens that represent equality comparisons.
 * @method static array<int|string, int|string> functionNameTokens()       Tokens that represent the names of called
 *                                                                         functions.
 * @method static array<int|string, int|string> heredocTokens()            Tokens that make up a heredoc string.
 * @method static array<int|string, int|string> includeTokens()            Tokens that include files.
 * @method static array<int|string, int|string> magicConstants()           Tokens representing PHP magic constants.
 * @method static array<int|string, int|string> methodPrefixes()           Tokens that can prefix a method name.
 * @method static array<int|string, int|string> nameTokens()               Tokens used for "names", be it namespace, OO,
 *                                                                         function or constant names.
 * @method static array<int|string, int|string> ooScopeTokens()            Tokens that open class and object scopes.
 * @method static array<int|string, int|string> operators()                Tokens that perform operations.
 * @method static array<int|string, int|string> parenthesisOpeners()       Token types that open parenthesis.
 * @method static array<int|string, int|string> phpcsCommentTokens()       Tokens that are comments containing PHPCS
 *                                                                         instructions.
 * @method static array<int|string, int|string> scopeModifiers()           Tokens that represent scope modifiers.
 * @method static array<int|string, int|string> scopeOpeners()             Tokens that are allowed to open scopes.
 * @method static array<int|string, int|string> stringTokens()             Tokens that represent strings.
 *                                                                         Note that `T_STRING`s are NOT represented in this
 *                                                                         list as this list is about _text_ strings.
 * @method static array<int|string, int|string> textStringTokens()         Tokens that represent text strings.
 */
final class BCTokens
{

    /**
     * Translation table to translate PHPCS 3.x variable names (which are also used for the BCTokens method names)
     * to their corresponding PHPCS 4.x class constant names.
     *
     * @since x.x.x
     *
     * @var array<string, string>
     */
    private const NAME_TRANSLATION = [
        'arithmeticTokens'         => 'ARITHMETIC_TOKENS',
        'assignmentTokens'         => 'ASSIGNMENT_TOKENS',
        'blockOpeners'             => 'BLOCK_OPENERS',
        'booleanOperators'         => 'BOOLEAN_OPERATORS',
        'bracketTokens'            => 'BRACKET_TOKENS',
        'castTokens'               => 'CAST_TOKENS',
        'commentTokens'            => 'COMMENT_TOKENS',
        'comparisonTokens'         => 'COMPARISON_TOKENS',
        'contextSensitiveKeywords' => 'CONTEXT_SENSITIVE_KEYWORDS',
        'emptyTokens'              => 'EMPTY_TOKENS',
        'equalityTokens'           => 'EQUALITY_TOKENS',
        'functionNameTokens'       => 'FUNCTION_NAME_TOKENS',
        'heredocTokens'            => 'HEREDOC_TOKENS',
        'includeTokens'            => 'INCLUDE_TOKENS',
        'magicConstants'           => 'MAGIC_CONSTANTS',
        'methodPrefixes'           => 'METHOD_MODIFIERS',
        'nameTokens'               => 'NAME_TOKENS',
        'ooScopeTokens'            => 'OO_SCOPE_TOKENS',
        'operators'                => 'OPERATORS',
        'parenthesisOpeners'       => 'PARENTHESIS_OPENERS',
        'phpcsCommentTokens'       => 'PHPCS_ANNOTATION_TOKENS',
        'scopeModifiers'           => 'SCOPE_MODIFIERS',
        'scopeOpeners'             => 'SCOPE_OPENERS',
        'stringTokens'             => 'STRING_TOKENS',
        'textStringTokens'         => 'TEXT_STRING_TOKENS',
    ];

    /**
     * Handle calls to (undeclared) methods for token arrays which haven't received any
     * changes since PHPCS 4.0.0.
     *
     * @since 1.0.0
     *
     * @param string       $name The name of the method which has been called.
     * @param array<mixed> $args Any arguments passed to the method.
     *                           Unused as none of the methods take arguments.
     *
     * @return array<int|string, int|string> Token array
     *
     * @throws \PHPCSUtils\Exceptions\InvalidTokenArray When an invalid token array is requested.
     */
    public static function __callStatic($name, $args)
    {
        if (isset(self::NAME_TRANSLATION[$name]) && \defined(Tokens::class . '::' . self::NAME_TRANSLATION[$name])) {
            return \constant(Tokens::class . '::' . self::NAME_TRANSLATION[$name]);
        }

        // Unknown token array requested.
        throw InvalidTokenArray::create($name);
    }
}
