<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Operators;

use PHPCSUtils\Tests\Utils\Operators\IsUnaryPlusMinusTestCase;

/**
 * Tests for the \PHPCSUtils\Utils\Operators::isUnaryPlusMinus() method.
 *
 * @covers \PHPCSUtils\Utils\Operators::isUnaryPlusMinus
 *
 * @group operators
 *
 * @since 1.0.0
 */
final class IsUnaryPlusMinusTest extends IsUnaryPlusMinusTestCase
{

    /**
     * Data provider.
     *
     * @see testIsUnaryPlusMinus() For the array format.
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataIsUnaryPlusMinus()
    {
        return [
            'non-unary-plus' => [
                'testMarker' => '/* testNonUnaryPlus */',
                'expected'   => false,
            ],
            'non-unary-minus' => [
                'testMarker' => '/* testNonUnaryMinus */',
                'expected'   => false,
            ],
            'non-unary-plus-arrays' => [
                'testMarker' => '/* testNonUnaryPlusArrays */',
                'expected'   => false,
            ],
            'unary-minus-arithmetic' => [
                'testMarker' => '/* testUnaryMinusArithmetic */',
                'expected'   => true,
            ],
            'unary-plus-arithmetic' => [
                'testMarker' => '/* testUnaryPlusArithmetic */',
                'expected'   => true,
            ],
            'unary-minus-concatenation' => [
                'testMarker' => '/* testUnaryMinusConcatenation */',
                'expected'   => true,
            ],
            'unary-plus-int-assignment' => [
                'testMarker' => '/* testUnaryPlusIntAssignment */',
                'expected'   => true,
            ],
            'unary-minus-variable-assignment' => [
                'testMarker' => '/* testUnaryMinusVariableAssignment */',
                'expected'   => true,
            ],
            'unary-plus-float-assignment' => [
                'testMarker' => '/* testUnaryPlusFloatAssignment */',
                'expected'   => true,
            ],
            'unary-minus-bool-assignment' => [
                'testMarker' => '/* testUnaryMinusBoolAssignment */',
                'expected'   => true,
            ],
            'unary-plus-string-assignment-with-comment' => [
                'testMarker' => '/* testUnaryPlusStringAssignmentWithComment */',
                'expected'   => true,
            ],
            'unary-minus-string-assignment' => [
                'testMarker' => '/* testUnaryMinusStringAssignment */',
                'expected'   => true,
            ],
            'unary-plus-plus-null-assignment' => [
                'testMarker' => '/* testUnaryPlusNullAssignment */',
                'expected'   => true,
            ],
            'unary-minus-variable-variable-assignment' => [
                'testMarker' => '/* testUnaryMinusVariableVariableAssignment */',
                'expected'   => true,
            ],
            'unary-plus-int-comparison' => [
                'testMarker' => '/* testUnaryPlusIntComparison */',
                'expected'   => true,
            ],
            'unary-plus-int-comparison-yoda' => [
                'testMarker' => '/* testUnaryPlusIntComparisonYoda */',
                'expected'   => true,
            ],
            'unary-minus-float-comparison' => [
                'testMarker' => '/* testUnaryMinusFloatComparison */',
                'expected'   => true,
            ],
            'unary-minus-string-comparison-yoda' => [
                'testMarker' => '/* testUnaryMinusStringComparisonYoda */',
                'expected'   => true,
            ],
            'unary-plus-variable-boolean' => [
                'testMarker' => '/* testUnaryPlusVariableBoolean */',
                'expected'   => true,
            ],
            'unary-minus-variable-boolean' => [
                'testMarker' => '/* testUnaryMinusVariableBoolean */',
                'expected'   => true,
            ],
            'unary-plus-logical-xor' => [
                'testMarker' => '/* testUnaryPlusLogicalXor */',
                'expected'   => true,
            ],
            'unary-minus-ternary-then' => [
                'testMarker' => '/* testUnaryMinusTernaryThen */',
                'expected'   => true,
            ],
            'unary-plus-ternary-else' => [
                'testMarker' => '/* testUnaryPlusTernaryElse */',
                'expected'   => true,
            ],
            'unary-minus-coalesce' => [
                'testMarker' => '/* testUnaryMinusCoalesce */',
                'expected'   => true,
            ],
            'unary-plus-int-return' => [
                'testMarker' => '/* testUnaryPlusIntReturn */',
                'expected'   => true,
            ],
            'unary-minus-float-return' => [
                'testMarker' => '/* testUnaryMinusFloatReturn */',
                'expected'   => true,
            ],
            'unary-minus-int-exit' => [
                'testMarker' => '/* testUnaryPlusIntExit */',
                'expected'   => true,
            ],
            'unary-plus-print' => [
                'testMarker' => '/* testUnaryPlusPrint */',
                'expected'   => true,
            ],
            'unary-minus-echo' => [
                'testMarker' => '/* testUnaryMinusEcho */',
                'expected'   => true,
            ],
            'unary-plus-yield' => [
                'testMarker' => '/* testUnaryPlusYield */',
                'expected'   => true,
            ],
            'unary-plus-array-access' => [
                'testMarker' => '/* testUnaryPlusArrayAccess */',
                'expected'   => true,
            ],
            'unary-minus-string-array-access' => [
                'testMarker' => '/* testUnaryMinusStringArrayAccess */',
                'expected'   => true,
            ],
            'unary-plus-long-array-assignment' => [
                'testMarker' => '/* testUnaryPlusLongArrayAssignment */',
                'expected'   => true,
            ],
            'unary-minus-long-array-assignment-key' => [
                'testMarker' => '/* testUnaryMinusLongArrayAssignmentKey */',
                'expected'   => true,
            ],
            'unary-plus-long-array-assignment-value' => [
                'testMarker' => '/* testUnaryPlusLongArrayAssignmentValue */',
                'expected'   => true,
            ],
            'unary-plus-short-array-assignment' => [
                'testMarker' => '/* testUnaryPlusShortArrayAssignment */',
                'expected'   => true,
            ],
            'non-unary-minus-short-array-assignment' => [
                'testMarker' => '/* testNonUnaryMinusShortArrayAssignment */',
                'expected'   => false,
            ],
            'unary-minus-casts' => [
                'testMarker' => '/* testUnaryMinusCast */',
                'expected'   => true,
            ],
            'unary-plus-function-call-param' => [
                'testMarker' => '/* testUnaryPlusFunctionCallParam */',
                'expected'   => true,
            ],
            'unary-minus-function-call-param' => [
                'testMarker' => '/* testUnaryMinusFunctionCallParam */',
                'expected'   => true,
            ],
            'unary-plus-declare' => [
                'testMarker' => '/* testUnaryPlusDeclare */',
                'expected'   => true,
            ],
            'unary-plus-switch-case' => [
                'testMarker' => '/* testUnaryPlusCase */',
                'expected'   => true,
            ],
            'unary-plus-continue' => [
                'testMarker' => '/* testUnaryPlusContinue */',
                'expected'   => true,
            ],
            'unary-minus-switch-case' => [
                'testMarker' => '/* testUnaryMinusCase */',
                'expected'   => true,
            ],
            'unary-plus-break' => [
                'testMarker' => '/* testUnaryPlusBreak */',
                'expected'   => true,
            ],
            'unary-minus-arrow-function' => [
                'testMarker' => '/* testUnaryMinusArrowFunction */',
                'expected'   => true,
            ],
            'unary-plus-match-arrow' => [
                'testMarker' => '/* testUnaryPlusMatchArrow */',
                'expected'   => true,
            ],
            'unary-minus-match-arrow' => [
                'testMarker' => '/* testUnaryMinusMatchArrow */',
                'expected'   => true,
            ],
            'unary-minus-match-default' => [
                'testMarker' => '/* testUnaryMinusMatchDefault */',
                'expected'   => true,
            ],
            'operator-sequence-non-unary-1' => [
                'testMarker' => '/* testSequenceNonUnary1 */',
                'expected'   => false,
            ],
            'operator-sequence-non-unary-2' => [
                'testMarker' => '/* testSequenceNonUnary2 */',
                'expected'   => false,
            ],
            'operator-sequence-non-unary-3' => [
                'testMarker' => '/* testSequenceNonUnary3 */',
                'expected'   => false,
            ],
            'operator-sequence-unary-end' => [
                'testMarker' => '/* testSequenceUnaryEnd */',
                'expected'   => true,
            ],
            'php-7.4-underscore-int-calculation-1' => [
                'testMarker' => '/* testPHP74NumericLiteralIntCalc1 */',
                'expected'   => false,
            ],
            'php-7.4-underscore-int-calculation-2' => [
                'testMarker' => '/* testPHP74NumericLiteralIntCalc2 */',
                'expected'   => false,
            ],
            'php-7.4-underscore-float-calculation-1' => [
                'testMarker' => '/* testPHP74NumericLiteralFloatCalc1 */',
                'expected'   => false,
            ],
            'php-7.4-underscore-float-calculation-2' => [
                'testMarker' => '/* testPHP74NumericLiteralFloatCalc2 */',
                'expected'   => false,
            ],

            'parse-error' => [
                'testMarker' => '/* testParseError */',
                'expected'   => false,
            ],
        ];
    }
}
