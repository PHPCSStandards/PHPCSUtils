<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\TextStrings;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\TextStrings;

/**
 * Tests for the \PHPCSUtils\Utils\TextStrings::getCompleteTextString() method.
 *
 * @covers \PHPCSUtils\Utils\TextStrings::getCompleteTextString
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
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException(
            '$stackPtr must be of type T_START_HEREDOC, T_START_NOWDOC, T_CONSTANT_ENCAPSED_STRING'
            . ' or T_DOUBLE_QUOTED_STRING'
        );

        TextStrings::getCompleteTextString(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a non text string is passed.
     *
     * @return void
     */
    public function testNotATextStringException()
    {
        $this->expectPhpcsException(
            '$stackPtr must be of type T_START_HEREDOC, T_START_NOWDOC, T_CONSTANT_ENCAPSED_STRING'
            . ' or T_DOUBLE_QUOTED_STRING'
        );

        $next = $this->getTargetToken('/* testNotATextString */', \T_RETURN);
        TextStrings::getCompleteTextString(self::$phpcsFile, $next);
    }

    /**
     * Test receiving an expected exception when a text string token is not the first token
     * of a multi-line text string.
     *
     * @return void
     */
    public function testNotFirstTextStringException()
    {
        $this->expectPhpcsException('$stackPtr must be the start of the text string');

        $next = $this->getTargetToken(
            '/* testNotFirstTextStringToken */',
            \T_CONSTANT_ENCAPSED_STRING,
            'second line
'
        );
        TextStrings::getCompleteTextString(self::$phpcsFile, $next);
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
                '/* testSingleLineConstantEncapsedString */',
                'single line text string',
                "'single line text string'",
            ],
            'multi-line-constant-encapsed-string' => [
                '/* testMultiLineConstantEncapsedString */',
                'first line
second line
third line
fourth line',
                '"first line
second line
third line
fourth line"',
            ],
            'single-line-double-quoted-string' => [
                '/* testSingleLineDoubleQuotedString */',
                'single $line text string',
                '"single $line text string"',
            ],
            'multi-line-double-quoted-string' => [
                '/* testMultiLineDoubleQuotedString */',
                'first line
second $line
third line
fourth line',
                '"first line
second $line
third line
fourth line"',
            ],
            'heredoc' => [
                '/* testHeredocString */',
                'first line
second $line
third line
fourth line
',
                'first line
second $line
third line
fourth line
',
            ],
            'nowdoc' => [
                '/* testNowdocString */',
                'first line
second line
third line
fourth line
',
                'first line
second line
third line
fourth line
',
            ],
            'text-string-at-end-of-file' => [
                '/* testTextStringAtEndOfFile */',
                'first line
last line',
                "'first line
last line'",
            ],
        ];
    }
}
