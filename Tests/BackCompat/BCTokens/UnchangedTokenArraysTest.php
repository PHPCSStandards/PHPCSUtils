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
class UnchangedTokenArraysTest extends TestCase
{

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
     * Tokens that represent strings.
     *
     * @var array <int|string> => <int|string>
     */
    private $stringTokens = [
        \T_CONSTANT_ENCAPSED_STRING => \T_CONSTANT_ENCAPSED_STRING,
        \T_DOUBLE_QUOTED_STRING     => \T_DOUBLE_QUOTED_STRING,
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
