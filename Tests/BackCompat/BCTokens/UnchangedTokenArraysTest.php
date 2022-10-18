<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\BCTokens;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\BCTokens;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\BackCompat\BCTokens::__callStatic
 *
 * @group tokens
 *
 * @since 1.0.0
 */
final class UnchangedTokenArraysTest extends TestCase
{

    /**
     * Tokens that represent assignments.
     *
     * @var array<int|string, int|string>
     */
    private $assignmentTokens = [
        \T_EQUAL          => \T_EQUAL,
        \T_AND_EQUAL      => \T_AND_EQUAL,
        \T_OR_EQUAL       => \T_OR_EQUAL,
        \T_CONCAT_EQUAL   => \T_CONCAT_EQUAL,
        \T_DIV_EQUAL      => \T_DIV_EQUAL,
        \T_MINUS_EQUAL    => \T_MINUS_EQUAL,
        \T_POW_EQUAL      => \T_POW_EQUAL,
        \T_MOD_EQUAL      => \T_MOD_EQUAL,
        \T_MUL_EQUAL      => \T_MUL_EQUAL,
        \T_PLUS_EQUAL     => \T_PLUS_EQUAL,
        \T_XOR_EQUAL      => \T_XOR_EQUAL,
        \T_DOUBLE_ARROW   => \T_DOUBLE_ARROW,
        \T_SL_EQUAL       => \T_SL_EQUAL,
        \T_SR_EQUAL       => \T_SR_EQUAL,
        \T_COALESCE_EQUAL => \T_COALESCE_EQUAL,
        \T_ZSR_EQUAL      => \T_ZSR_EQUAL,
    ];

    /**
     * Tokens that represent equality comparisons.
     *
     * @var array <int|string> => <int|string>
     */
    private $equalityTokens = [
        \T_IS_EQUAL            => \T_IS_EQUAL,
        \T_IS_NOT_EQUAL        => \T_IS_NOT_EQUAL,
        \T_IS_IDENTICAL        => \T_IS_IDENTICAL,
        \T_IS_NOT_IDENTICAL    => \T_IS_NOT_IDENTICAL,
        \T_IS_SMALLER_OR_EQUAL => \T_IS_SMALLER_OR_EQUAL,
        \T_IS_GREATER_OR_EQUAL => \T_IS_GREATER_OR_EQUAL,
    ];

    /**
     * Tokens that represent comparison operator.
     *
     * @var array<int|string, int|string>
     */
    private $comparisonTokens = [
        \T_IS_EQUAL            => \T_IS_EQUAL,
        \T_IS_IDENTICAL        => \T_IS_IDENTICAL,
        \T_IS_NOT_EQUAL        => \T_IS_NOT_EQUAL,
        \T_IS_NOT_IDENTICAL    => \T_IS_NOT_IDENTICAL,
        \T_LESS_THAN           => \T_LESS_THAN,
        \T_GREATER_THAN        => \T_GREATER_THAN,
        \T_IS_SMALLER_OR_EQUAL => \T_IS_SMALLER_OR_EQUAL,
        \T_IS_GREATER_OR_EQUAL => \T_IS_GREATER_OR_EQUAL,
        \T_SPACESHIP           => \T_SPACESHIP,
        \T_COALESCE            => \T_COALESCE,
    ];

    /**
     * Tokens that represent arithmetic operators.
     *
     * @var array<int|string, int|string>
     */
    private $arithmeticTokens = [
        \T_PLUS     => \T_PLUS,
        \T_MINUS    => \T_MINUS,
        \T_MULTIPLY => \T_MULTIPLY,
        \T_DIVIDE   => \T_DIVIDE,
        \T_MODULUS  => \T_MODULUS,
        \T_POW      => \T_POW,
    ];

    /**
     * Tokens that perform operations.
     *
     * @var array<int|string, int|string>
     */
    private $operators = [
        \T_MINUS       => \T_MINUS,
        \T_PLUS        => \T_PLUS,
        \T_MULTIPLY    => \T_MULTIPLY,
        \T_DIVIDE      => \T_DIVIDE,
        \T_MODULUS     => \T_MODULUS,
        \T_POW         => \T_POW,
        \T_SPACESHIP   => \T_SPACESHIP,
        \T_COALESCE    => \T_COALESCE,
        \T_BITWISE_AND => \T_BITWISE_AND,
        \T_BITWISE_OR  => \T_BITWISE_OR,
        \T_BITWISE_XOR => \T_BITWISE_XOR,
        \T_SL          => \T_SL,
        \T_SR          => \T_SR,
    ];

    /**
     * Tokens that perform boolean operations.
     *
     * @var array <int|string> => <int|string>
     */
    private $booleanOperators = [
        \T_BOOLEAN_AND => \T_BOOLEAN_AND,
        \T_BOOLEAN_OR  => \T_BOOLEAN_OR,
        \T_LOGICAL_AND => \T_LOGICAL_AND,
        \T_LOGICAL_OR  => \T_LOGICAL_OR,
        \T_LOGICAL_XOR => \T_LOGICAL_XOR,
    ];

    /**
     * Tokens that represent casting.
     *
     * @var array <int|string> => <int|string>
     */
    private $castTokens = [
        \T_INT_CAST    => \T_INT_CAST,
        \T_STRING_CAST => \T_STRING_CAST,
        \T_DOUBLE_CAST => \T_DOUBLE_CAST,
        \T_ARRAY_CAST  => \T_ARRAY_CAST,
        \T_BOOL_CAST   => \T_BOOL_CAST,
        \T_OBJECT_CAST => \T_OBJECT_CAST,
        \T_UNSET_CAST  => \T_UNSET_CAST,
        \T_BINARY_CAST => \T_BINARY_CAST,
    ];

    /**
     * Tokens that are allowed to open scopes.
     *
     * @var array<int|string, int|string>
     */
    private $scopeOpeners = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_INTERFACE  => \T_INTERFACE,
        \T_TRAIT      => \T_TRAIT,
        \T_ENUM       => \T_ENUM,
        \T_NAMESPACE  => \T_NAMESPACE,
        \T_FUNCTION   => \T_FUNCTION,
        \T_CLOSURE    => \T_CLOSURE,
        \T_IF         => \T_IF,
        \T_SWITCH     => \T_SWITCH,
        \T_CASE       => \T_CASE,
        \T_DECLARE    => \T_DECLARE,
        \T_DEFAULT    => \T_DEFAULT,
        \T_WHILE      => \T_WHILE,
        \T_ELSE       => \T_ELSE,
        \T_ELSEIF     => \T_ELSEIF,
        \T_FOR        => \T_FOR,
        \T_FOREACH    => \T_FOREACH,
        \T_DO         => \T_DO,
        \T_TRY        => \T_TRY,
        \T_CATCH      => \T_CATCH,
        \T_FINALLY    => \T_FINALLY,
        \T_PROPERTY   => \T_PROPERTY,
        \T_OBJECT     => \T_OBJECT,
        \T_USE        => \T_USE,
        \T_MATCH      => \T_MATCH,
    ];

    /**
     * Tokens that represent scope modifiers.
     *
     * @var array <int|string> => <int|string>
     */
    private $scopeModifiers = [
        \T_PRIVATE   => \T_PRIVATE,
        \T_PUBLIC    => \T_PUBLIC,
        \T_PROTECTED => \T_PROTECTED,
    ];

    /**
     * Tokens that can prefix a method name
     *
     * @var array <int|string> => <int|string>
     */
    private $methodPrefixes = [
        \T_PRIVATE   => \T_PRIVATE,
        \T_PUBLIC    => \T_PUBLIC,
        \T_PROTECTED => \T_PROTECTED,
        \T_ABSTRACT  => \T_ABSTRACT,
        \T_STATIC    => \T_STATIC,
        \T_FINAL     => \T_FINAL,
    ];

    /**
     * Tokens that open code blocks.
     *
     * @var array <int|string> => <int|string>
     */
    private $blockOpeners = [
        \T_OPEN_CURLY_BRACKET  => \T_OPEN_CURLY_BRACKET,
        \T_OPEN_SQUARE_BRACKET => \T_OPEN_SQUARE_BRACKET,
        \T_OPEN_PARENTHESIS    => \T_OPEN_PARENTHESIS,
        \T_OBJECT              => \T_OBJECT,
    ];

    /**
     * Tokens that don't represent code.
     *
     * @var array<int|string, int|string>
     */
    private $emptyTokens = [
        \T_WHITESPACE             => \T_WHITESPACE,
        \T_COMMENT                => \T_COMMENT,
        \T_DOC_COMMENT            => \T_DOC_COMMENT,
        \T_DOC_COMMENT_STAR       => \T_DOC_COMMENT_STAR,
        \T_DOC_COMMENT_WHITESPACE => \T_DOC_COMMENT_WHITESPACE,
        \T_DOC_COMMENT_TAG        => \T_DOC_COMMENT_TAG,
        \T_DOC_COMMENT_OPEN_TAG   => \T_DOC_COMMENT_OPEN_TAG,
        \T_DOC_COMMENT_CLOSE_TAG  => \T_DOC_COMMENT_CLOSE_TAG,
        \T_DOC_COMMENT_STRING     => \T_DOC_COMMENT_STRING,
        \T_PHPCS_ENABLE           => \T_PHPCS_ENABLE,
        \T_PHPCS_DISABLE          => \T_PHPCS_DISABLE,
        \T_PHPCS_SET              => \T_PHPCS_SET,
        \T_PHPCS_IGNORE           => \T_PHPCS_IGNORE,
        \T_PHPCS_IGNORE_FILE      => \T_PHPCS_IGNORE_FILE,
    ];

    /**
     * Tokens that are comments.
     *
     * @var array<int|string, int|string>
     */
    private $commentTokens = [
        \T_COMMENT                => \T_COMMENT,
        \T_DOC_COMMENT            => \T_DOC_COMMENT,
        \T_DOC_COMMENT_STAR       => \T_DOC_COMMENT_STAR,
        \T_DOC_COMMENT_WHITESPACE => \T_DOC_COMMENT_WHITESPACE,
        \T_DOC_COMMENT_TAG        => \T_DOC_COMMENT_TAG,
        \T_DOC_COMMENT_OPEN_TAG   => \T_DOC_COMMENT_OPEN_TAG,
        \T_DOC_COMMENT_CLOSE_TAG  => \T_DOC_COMMENT_CLOSE_TAG,
        \T_DOC_COMMENT_STRING     => \T_DOC_COMMENT_STRING,
        \T_PHPCS_ENABLE           => \T_PHPCS_ENABLE,
        \T_PHPCS_DISABLE          => \T_PHPCS_DISABLE,
        \T_PHPCS_SET              => \T_PHPCS_SET,
        \T_PHPCS_IGNORE           => \T_PHPCS_IGNORE,
        \T_PHPCS_IGNORE_FILE      => \T_PHPCS_IGNORE_FILE,
    ];

    /**
     * Tokens that are comments containing PHPCS instructions.
     *
     * @var array<int|string, int|string>
     */
    private $phpcsCommentTokens = [
        \T_PHPCS_ENABLE      => \T_PHPCS_ENABLE,
        \T_PHPCS_DISABLE     => \T_PHPCS_DISABLE,
        \T_PHPCS_SET         => \T_PHPCS_SET,
        \T_PHPCS_IGNORE      => \T_PHPCS_IGNORE,
        \T_PHPCS_IGNORE_FILE => \T_PHPCS_IGNORE_FILE,
    ];

    /**
     * Tokens that represent strings.
     *
     * @var array <int|string> => <int|string>
     */
    private $stringTokens = [
        \T_CONSTANT_ENCAPSED_STRING => \T_CONSTANT_ENCAPSED_STRING,
        \T_DOUBLE_QUOTED_STRING     => \T_DOUBLE_QUOTED_STRING,
    ];

    /**
     * Tokens that represent text strings.
     *
     * @var array<int|string, int|string>
     */
    private $textStringTokens = [
        \T_CONSTANT_ENCAPSED_STRING => \T_CONSTANT_ENCAPSED_STRING,
        \T_DOUBLE_QUOTED_STRING     => \T_DOUBLE_QUOTED_STRING,
        \T_INLINE_HTML              => \T_INLINE_HTML,
        \T_HEREDOC                  => \T_HEREDOC,
        \T_NOWDOC                   => \T_NOWDOC,
    ];

    /**
     * Tokens that represent brackets and parenthesis.
     *
     * @var array <int|string> => <int|string>
     */
    private $bracketTokens = [
        \T_OPEN_CURLY_BRACKET   => \T_OPEN_CURLY_BRACKET,
        \T_CLOSE_CURLY_BRACKET  => \T_CLOSE_CURLY_BRACKET,
        \T_OPEN_SQUARE_BRACKET  => \T_OPEN_SQUARE_BRACKET,
        \T_CLOSE_SQUARE_BRACKET => \T_CLOSE_SQUARE_BRACKET,
        \T_OPEN_PARENTHESIS     => \T_OPEN_PARENTHESIS,
        \T_CLOSE_PARENTHESIS    => \T_CLOSE_PARENTHESIS,
    ];

    /**
     * Tokens that include files.
     *
     * @var array <int|string> => <int|string>
     */
    private $includeTokens = [
        \T_REQUIRE_ONCE => \T_REQUIRE_ONCE,
        \T_REQUIRE      => \T_REQUIRE,
        \T_INCLUDE_ONCE => \T_INCLUDE_ONCE,
        \T_INCLUDE      => \T_INCLUDE,
    ];

    /**
     * Tokens that make up a heredoc string.
     *
     * @var array <int|string> => <int|string>
     */
    private $heredocTokens = [
        \T_START_HEREDOC => \T_START_HEREDOC,
        \T_END_HEREDOC   => \T_END_HEREDOC,
        \T_HEREDOC       => \T_HEREDOC,
        \T_START_NOWDOC  => \T_START_NOWDOC,
        \T_END_NOWDOC    => \T_END_NOWDOC,
        \T_NOWDOC        => \T_NOWDOC,
    ];

    /**
     * Tokens that open class and object scopes.
     *
     * @var array<int|string, int|string>
     */
    private $ooScopeTokens = [
        \T_CLASS      => \T_CLASS,
        \T_ANON_CLASS => \T_ANON_CLASS,
        \T_INTERFACE  => \T_INTERFACE,
        \T_TRAIT      => \T_TRAIT,
        \T_ENUM       => \T_ENUM,
    ];

    /**
     * Tokens representing PHP magic constants.
     *
     * @var array <int|string> => <int|string>
     *
     * @link https://www.php.net/language.constants.predefined PHP Manual on magic constants
     */
    private $magicConstants = [
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
     * Tokens representing context sensitive keywords in PHP.
     *
     * @var array<int|string, int|string>
     *
     * https://wiki.php.net/rfc/context_sensitive_lexer
     */
    private $contextSensitiveKeywords = [
        \T_ABSTRACT     => \T_ABSTRACT,
        \T_ARRAY        => \T_ARRAY,
        \T_AS           => \T_AS,
        \T_BREAK        => \T_BREAK,
        \T_CALLABLE     => \T_CALLABLE,
        \T_CASE         => \T_CASE,
        \T_CATCH        => \T_CATCH,
        \T_CLASS        => \T_CLASS,
        \T_CLONE        => \T_CLONE,
        \T_CONST        => \T_CONST,
        \T_CONTINUE     => \T_CONTINUE,
        \T_DECLARE      => \T_DECLARE,
        \T_DEFAULT      => \T_DEFAULT,
        \T_DO           => \T_DO,
        \T_ECHO         => \T_ECHO,
        \T_ELSE         => \T_ELSE,
        \T_ELSEIF       => \T_ELSEIF,
        \T_EMPTY        => \T_EMPTY,
        \T_ENDDECLARE   => \T_ENDDECLARE,
        \T_ENDFOR       => \T_ENDFOR,
        \T_ENDFOREACH   => \T_ENDFOREACH,
        \T_ENDIF        => \T_ENDIF,
        \T_ENDSWITCH    => \T_ENDSWITCH,
        \T_ENDWHILE     => \T_ENDWHILE,
        \T_ENUM         => \T_ENUM,
        \T_EVAL         => \T_EVAL,
        \T_EXIT         => \T_EXIT,
        \T_EXTENDS      => \T_EXTENDS,
        \T_FINAL        => \T_FINAL,
        \T_FINALLY      => \T_FINALLY,
        \T_FN           => \T_FN,
        \T_FOR          => \T_FOR,
        \T_FOREACH      => \T_FOREACH,
        \T_FUNCTION     => \T_FUNCTION,
        \T_GLOBAL       => \T_GLOBAL,
        \T_GOTO         => \T_GOTO,
        \T_IF           => \T_IF,
        \T_IMPLEMENTS   => \T_IMPLEMENTS,
        \T_INCLUDE      => \T_INCLUDE,
        \T_INCLUDE_ONCE => \T_INCLUDE_ONCE,
        \T_INSTANCEOF   => \T_INSTANCEOF,
        \T_INSTEADOF    => \T_INSTEADOF,
        \T_INTERFACE    => \T_INTERFACE,
        \T_ISSET        => \T_ISSET,
        \T_LIST         => \T_LIST,
        \T_LOGICAL_AND  => \T_LOGICAL_AND,
        \T_LOGICAL_OR   => \T_LOGICAL_OR,
        \T_LOGICAL_XOR  => \T_LOGICAL_XOR,
        \T_MATCH        => \T_MATCH,
        \T_NAMESPACE    => \T_NAMESPACE,
        \T_NEW          => \T_NEW,
        \T_PRINT        => \T_PRINT,
        \T_PRIVATE      => \T_PRIVATE,
        \T_PROTECTED    => \T_PROTECTED,
        \T_PUBLIC       => \T_PUBLIC,
        \T_READONLY     => \T_READONLY,
        \T_REQUIRE      => \T_REQUIRE,
        \T_REQUIRE_ONCE => \T_REQUIRE_ONCE,
        \T_RETURN       => \T_RETURN,
        \T_STATIC       => \T_STATIC,
        \T_SWITCH       => \T_SWITCH,
        \T_THROW        => \T_THROW,
        \T_TRAIT        => \T_TRAIT,
        \T_TRY          => \T_TRY,
        \T_UNSET        => \T_UNSET,
        \T_USE          => \T_USE,
        \T_VAR          => \T_VAR,
        \T_WHILE        => \T_WHILE,
        \T_YIELD        => \T_YIELD,
        \T_YIELD_FROM   => \T_YIELD_FROM,
    ];

    /**
     * Test the method.
     *
     * @dataProvider dataUnchangedTokenArrays
     *
     * @param string $name     The token array name.
     * @param array  $expected The token array content.
     *
     * @return void
     */
    public function testUnchangedTokenArrays($name, $expected)
    {
        $this->assertSame($expected, BCTokens::$name());
    }

    /**
     * Data provider.
     *
     * @see testUnchangedTokenArrays() For the array format.
     *
     * @return array
     */
    public function dataUnchangedTokenArrays()
    {
        $phpunitProp = [
            'backupGlobals'                     => true,
            'backupGlobalsBlacklist'            => true,
            'backupGlobalsExcludeList'          => true,
            'backupStaticAttributes'            => true,
            'backupStaticAttributesBlacklist'   => true,
            'backupStaticAttributesExcludeList' => true,
            'runTestInSeparateProcess'          => true,
            'preserveGlobalState'               => true,
            'providedTests'                     => true,
        ];

        $data        = [];
        $tokenArrays = \get_object_vars($this);
        foreach ($tokenArrays as $name => $expected) {
            if (isset($phpunitProp[$name])) {
                continue;
            }

            $data[$name] = [$name, $expected];
        }

        return $data;
    }

    /**
     * Test calling a token property method for a token array which doesn't exist.
     *
     * @return void
     */
    public function testUndeclaredTokenArray()
    {
        $this->expectException('PHPCSUtils\Exceptions\InvalidTokenArray');
        $this->expectExceptionMessage('Call to undefined method PHPCSUtils\BackCompat\BCTokens::notATokenArray()');

        BCTokens::notATokenArray();
    }

    /**
     * Test whether the method in BCTokens is still in sync with the latest version of PHPCS.
     *
     * This group is not run by default and has to be specifically requested to be run.
     *
     * @group compareWithPHPCS
     *
     * @dataProvider dataUnchangedTokenArrays
     *
     * @param string $name The token array name.
     *
     * @return void
     */
    public function testPHPCSUnchangedTokenArrays($name)
    {
        $this->assertSame(Tokens::${$name}, BCTokens::$name());
    }
}
