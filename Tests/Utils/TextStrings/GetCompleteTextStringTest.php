<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\TextStrings;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\TextStrings;

/**
 * Tests for the \PHPCSUtils\Utils\TextStrings::getCompleteTextString() and
 * \PHPCSUtils\Utils\TextStrings::getEndOfCompleteTextString() methods.
 *
 * @covers \PHPCSUtils\Utils\TextStrings::getCompleteTextString
 * @covers \PHPCSUtils\Utils\TextStrings::getEndOfCompleteTextString
 *
 * @group textstrings
 *
 * @since 1.0.0
 */
class GetCompleteTextStringTest extends UtilityMethodTestCase
{

    /**
     * Token types to target for these tests.
     *
     * @var array
     */
    private $targets = [
        \T_START_HEREDOC,
        \T_START_NOWDOC,
        \T_CONSTANT_ENCAPSED_STRING,
        \T_DOUBLE_QUOTED_STRING,
    ];

    /**
     * Test passing a non-existent token pointer.
     *
     * @dataProvider dataExceptions
     *
     * @param string $method The name of the method to test the exception for.
     *
     * @return void
     */
    public function testNonExistentToken($method)
    {
        $this->expectPhpcsException(
            '$stackPtr must be of type T_START_HEREDOC, T_START_NOWDOC, T_CONSTANT_ENCAPSED_STRING'
            . ' or T_DOUBLE_QUOTED_STRING'
        );

        TextStrings::$method(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a non text string is passed.
     *
     * @dataProvider dataExceptions
     *
     * @param string $method The name of the method to test the exception for.
     *
     * @return void
     */
    public function testNotATextStringException($method)
    {
        $this->expectPhpcsException(
            '$stackPtr must be of type T_START_HEREDOC, T_START_NOWDOC, T_CONSTANT_ENCAPSED_STRING'
            . ' or T_DOUBLE_QUOTED_STRING'
        );

        $next = $this->getTargetToken('/* testNotATextString */', \T_RETURN);
        TextStrings::$method(self::$phpcsFile, $next);
    }

    /**
     * Test receiving an expected exception when a text string token is not the first token
     * of a multi-line text string.
     *
     * @dataProvider dataExceptions
     *
     * @param string $method The name of the method to test the exception for.
     *
     * @return void
     */
    public function testNotFirstTextStringException($method)
    {
        $this->expectPhpcsException('$stackPtr must be the start of the text string');

        $next = $this->getTargetToken(
            '/* testNotFirstTextStringToken */',
            \T_CONSTANT_ENCAPSED_STRING,
            'second line
'
        );
        TextStrings::$method(self::$phpcsFile, $next);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataExceptions()
    {
        return [
            'getCompleteTextString'      => ['getCompleteTextString'],
            'getEndOfCompleteTextString' => ['getEndOfCompleteTextString'],
        ];
    }

    /**
     * Test correctly retrieving the contents of a (potentially) multi-line text string.
     *
     * @dataProvider dataGetCompleteTextString
     *
     * @param string $testMarker         The comment which prefaces the target token in the test file.
     * @param string $expected           The expected function return value.
     * @param string $expectedWithQuotes The expected function return value when $stripQuotes is set to "false".
     *
     * @return void
     */
    public function testGetCompleteTextString($testMarker, $expected, $expectedWithQuotes)
    {
        $stackPtr = $this->getTargetToken($testMarker, $this->targets);

        $result = TextStrings::getCompleteTextString(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result, 'Test failed getting the correct string with quotes stripped');

        $result = TextStrings::getCompleteTextString(self::$phpcsFile, $stackPtr, false);
        $this->assertSame($expectedWithQuotes, $result, 'Test failed getting the correct string (unchanged)');
    }

    /**
     * Data provider.
     *
     * @see testGetCompleteTextString() For the array format.
     *
     * @return array
     */
    public function dataGetCompleteTextString()
    {
        return [
            'single-line-constant-encapsed-string' => [
                'testMarker'         => '/* testSingleLineConstantEncapsedString */',
                'expected'           => 'single line text string',
                'expectedWithQuotes' => "'single line text string'",
            ],
            'multi-line-constant-encapsed-string' => [
                'testMarker'         => '/* testMultiLineConstantEncapsedString */',
                'expected'           => 'first line
second line
third line
fourth line',
                'expectedWithQuotes' => '"first line
second line
third line
fourth line"',
            ],
            'single-line-double-quoted-string' => [
                'testMarker'         => '/* testSingleLineDoubleQuotedString */',
                'expected'           => 'single $line text string',
                'expectedWithQuotes' => '"single $line text string"',
            ],
            'multi-line-double-quoted-string' => [
                'testMarker'         => '/* testMultiLineDoubleQuotedString */',
                'expected'           => 'first line
second $line
third line
fourth line',
                'expectedWithQuotes' => '"first line
second $line
third line
fourth line"',
            ],
            'heredoc' => [
                'testMarker'         => '/* testHeredocString */',
                'expected'           => 'first line
second $line
third line
fourth line',
                'expectedWithQuotes' => 'first line
second $line
third line
fourth line',
            ],
            'nowdoc' => [
                'testMarker'         => '/* testNowdocString */',
                'expected'           => 'first line
second line
third line
fourth line',
                'expectedWithQuotes' => 'first line
second line
third line
fourth line',
            ],

            'Single line double quoted string containing problem embeds' => [
                'testMarker'         => '/* testMultipleProblemEmbedsInSingleLineDoubleQuotedString */',
                'expected'           =>
                    'My ${foo["${bar}"]} and ${foo["${bar[\'baz\']}"]} and also ${foo->{"${\'a\'}"}}',
                'expectedWithQuotes' =>
                    '"My ${foo["${bar}"]} and ${foo["${bar[\'baz\']}"]} and also ${foo->{"${\'a\'}"}}"',
            ],
            'Multi-line double quoted string containing problem embeds' => [
                'testMarker'         => '/* testProblemEmbedAtEndOfLineInMultiLineDoubleQuotedString */',
                'expected'           => 'Testing ${foo["${bar[\'baz\']}"]}
and more ${foo["${bar}"]} testing',
                'expectedWithQuotes' => '"Testing ${foo["${bar[\'baz\']}"]}
and more ${foo["${bar}"]} testing"',
            ],
            'Multi-line double quoted string containing multi-line problem embed' => [
                'testMarker'         => '/* testMultilineProblemEmbedInMultiLineDoubleQuotedString */',
                'expected'           => 'Testing ${foo["${bar
  [\'baz\']
}"]} and more testing',
                'expectedWithQuotes' => '"Testing ${foo["${bar
  [\'baz\']
}"]} and more testing"',
            ],

            'text-string-at-end-of-file' => [
                'testMarker'         => '/* testTextStringAtEndOfFile */',
                'expected'           => 'first line
last line',
                'expectedWithQuotes' => "'first line
last line'",
            ],
        ];
    }
}
