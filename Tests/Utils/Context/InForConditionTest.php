<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Context;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Context;

/**
 * Tests for the \PHPCSUtils\Utils\Context::inForCondition() method.
 *
 * @covers \PHPCSUtils\Utils\Context::inForCondition
 *
 * @since 1.0.0
 */
final class InForConditionTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Context::inForCondition(self::$phpcsFile, 10000));
    }

    /**
     * Test receiving `false` when the token passed is not in a for condition.
     *
     * @dataProvider dataNotInFor
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @return void
     */
    public function testNotInFor($testMarker)
    {
        $target = $this->getTargetToken($testMarker, \T_VARIABLE, '$target');
        $this->assertFalse(Context::inForCondition(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testNotInFor()
     *
     * @return array<string, array<string>>
     */
    public static function dataNotInFor()
    {
        return [
            'no-parenthesis'                   => ['/* testNoParentheses */'],
            'no-parenthesis-owner'             => ['/* testNoParenthesisOwner */'],
            'owner-not-for'                    => ['/* testOwnerNotFor */'],
            'owner-not-for-nested-parentheses' => ['/* testOwnerNotForNestedParentheses */'],
            'method-called-for'                => ['/* testNotForMethodCall */'],
            'for-two-expressions'              => ['/* testForParseErrorTwoExpressions */'],
            'for-four-expressions'             => ['/* testForParseErrorFourExpressions */'],
            'parse-error'                      => ['/* testParseError */'],
        ];
    }

    /**
     * Test correctly identifying the position of a token in a for condition.
     *
     * @dataProvider dataInForCondition
     *
     * @param string     $testMarker    The comment which prefaces the target token in the test file.
     * @param string     $expected      The expected function return value.
     * @param int|string $targetType    Optional. The token type of the target token.
     *                                  Defaults to T_VARIABLE.
     * @param string     $targetContent Optional. The token content of the target token.
     *                                  Defaults to `$target` for `T_VARIABLE`.
     *
     * @return void
     */
    public function testInForCondition($testMarker, $expected, $targetType = \T_VARIABLE, $targetContent = null)
    {
        if ($targetType === \T_VARIABLE && $targetContent === null) {
            $targetContent = '$target';
        }

        $stackPtr = $this->getTargetToken($testMarker, $targetType, $targetContent);
        $result   = Context::inForCondition(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testInForCondition()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataInForCondition()
    {
        return [
            'expr1' => [
                'testMarker' => '/* testFor */',
                'expected'   => 'expr1',
                'targetType' => \T_EQUAL,
            ],
            'expr1-semicolon' => [
                'testMarker' => '/* testFor */',
                'expected'   => 'expr1',
                'targetType' => \T_SEMICOLON,
            ],
            'expr2' => [
                'testMarker'    => '/* testFor */',
                'expected'      => 'expr2',
                'targetType'    => \T_LNUMBER,
                'targetContent' => '10',
            ],
            'expr3' => [
                'testMarker' => '/* testFor */',
                'expected'   => 'expr3',
            ],
            'multi-expression-expr1' => [
                'testMarker' => '/* testForMultipleStatementsInExpr */',
                'expected'   => 'expr1',
            ],
            'multi-expression-expr2' => [
                'testMarker' => '/* testForMultipleStatementsInExpr */',
                'expected'   => 'expr2',
                'targetType' => \T_LESS_THAN,
            ],
            'multi-expression-expr2-semicolon' => [
                'testMarker' => '/* testForSecondSemicolon */',
                'expected'   => 'expr2',
                'targetType' => \T_SEMICOLON,
            ],
            'multi-expression-expr3' => [
                'testMarker' => '/* testForSecondSemicolon */',
                'expected'   => 'expr3',
                'targetType' => \T_PRINT,
            ],
            'empty-1-expr2' => [
                'testMarker'    => '/* testForEmptyExpr1 */',
                'expected'      => 'expr2',
                'targetType'    => \T_LNUMBER,
                'targetContent' => '10',
            ],
            'empty-1-expr3' => [
                'testMarker' => '/* testForEmptyExpr1 */',
                'expected'   => 'expr3',
            ],
            'empty-2-expr1' => [
                'testMarker' => '/* testForEmptyExpr2 */',
                'expected'   => 'expr1',
            ],
            'empty-2-expr3' => [
                'testMarker' => '/* testForEmptyExpr2 */',
                'expected'   => 'expr3',
                'targetType' => \T_INC,
            ],
            'empty-3-expr1' => [
                'testMarker'    => '/* testForEmptyExpr3 */',
                'expected'      => 'expr1',
                'targetType'    => \T_VARIABLE,
                'targetContent' => '$i',
            ],
            'empty-3-expr2' => [
                'testMarker' => '/* testForEmptyExpr3 */',
                'expected'   => 'expr2',
            ],
            'empty-12-expr3' => [
                'testMarker' => '/* testForEmptyExpr12 */',
                'expected'   => 'expr3',
            ],
            'empty-13-expr2' => [
                'testMarker' => '/* testForEmptyExpr13 */',
                'expected'   => 'expr2',
            ],
            'empty-23-expr1' => [
                'testMarker' => '/* testForEmptyExpr23 */',
                'expected'   => 'expr1',
            ],
            'empty-23-expr3' => [
                'testMarker' => '/* testForEmptyExpr23 */',
                'expected'   => 'expr3',
                'targetType' => \T_COMMENT,
            ],
            'empty-123-expr1' => [
                'testMarker' => '/* testForEmptyExpr123 */',
                'expected'   => 'expr1',
                'targetType' => \T_SEMICOLON,
            ],
            'nested-semicolon-expr1' => [
                'testMarker' => '/* testForWithNestedSemiColon */',
                'expected'   => 'expr1',
                'targetType' => \T_RETURN,
            ],
            'nested-semicolon-expr2' => [
                'testMarker' => '/* testForWithNestedSemiColon */',
                'expected'   => 'expr2',
                'targetType' => \T_LESS_THAN,
            ],
            'nested-semicolon-expr3' => [
                'testMarker' => '/* testForWithNestedSemiColon */',
                'expected'   => 'expr3',
                'targetType' => \T_INC,
            ],
            'nested-for-expr2' => [
                'testMarker'    => '/* testNestedFor */',
                'expected'      => 'expr2',
                'targetType'    => \T_STRING,
                'targetContent' => 'valid',
            ],
        ];
    }
}
